<?php

namespace App\Controllers;

use App\Models\ClientModel;

class ClientController extends BaseController
{
    public function dashboard()
    {
        if (session()->get('role') !== 'client') {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter en tant que client.');
        }

        $clientId = session()->get('client_id');
        $clientModel = new ClientModel();
        $client = $clientModel->find($clientId);

        $db = \Config\Database::connect();

        // 1. Types d'opérations
        $types = $db->table('type_operation')->get()->getResultArray();

        // 2. Historique des transactions
        $transactions = $db->table('transaction_log')
            ->select('
                transaction_log.*, 
                type_operation.nom as type_nom, 
                c1.telephone as source_tel, 
                COALESCE(c2.telephone, transaction_log.telephone_dest) as dest_tel
            ')
            ->join('type_operation', 'type_operation.id = transaction_log.id_type_operation')
            ->join('client c1', 'c1.id = transaction_log.id_client_source')
            ->join('client c2', 'c2.id = transaction_log.id_client_dest', 'left')
            ->where('id_client_source', $clientId)
            ->orWhere('id_client_dest', $clientId)
            ->orderBy('transaction_log.id', 'DESC')
            ->get()->getResultArray();

        return view('client/dashboard', [
            'client'       => $client,
            'types'        => $types,
            'transactions' => $transactions
        ]);
    }

    public function transaction()
    {
        if (session()->get('role') !== 'client') {
            return redirect()->to('/');
        }

        $clientId       = session()->get('client_id');
        $typeId         = $this->request->getPost('id_type_operation');
        $montant        = (float) $this->request->getPost('montant');
        $destPhone      = trim($this->request->getPost('destinataire') ?? '');
        $inclureRetrait = $this->request->getPost('inclure_frais_retrait') ? true : false;

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
        }


        
        $db = \Config\Database::connect();
        $clientModel = new ClientModel();
        $clientSource = $clientModel->find($clientId);

        $typeOp = $db->table('type_operation')->where('id', $typeId)->get()->getRowArray();
        if (!$typeOp) {
            return redirect()->back()->with('error', 'Type d\'opération invalide.');
        }

        $typeNom = strtolower($typeOp['nom']);

        // --- CALCUL DES FRAIS DE BASE DE L'OPÉRATION ---
        $bareme = $db->table('bareme_frais')
            ->where('id_type_operation', $typeId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->get()->getRowArray();

        $frais = $bareme ? (float) $bareme['frais'] : 0.0;

        // 1. DÉPÔT
        if (str_contains($typeNom, 'dépôt') || str_contains($typeNom, 'depot')) {
            $nouveauSolde = $clientSource['solde'] + $montant;
            $clientModel->update($clientId, ['solde' => $nouveauSolde]);

            // Historisation dépôt
            $db->table('transaction_log')->insert([
                'id_type_operation' => $typeId,
                'id_client_source'  => $clientId,
                'id_client_dest'    => null,
                'telephone_dest'    => null,
                'montant'           => $montant,
                'frais'             => $frais,
                'date_transaction'  => date('Y-m-d H:i:s')
            ]);
        }

        // 2. RETRAIT
        elseif (str_contains($typeNom, 'retrait')) {
            if (!empty($destPhone)) {
                $prefixeSourceCode = substr($clientSource['telephone'], 0, 3);
                $prefixeDestCode   = substr($destPhone, 0, 3);

                $isSourceLocal = $db->table('prefixe')->where('code', $prefixeSourceCode)->countAllResults() > 0;
                $isDestLocal   = $db->table('prefixe')->where('code', $prefixeDestCode)->countAllResults() > 0;

                if (!$isSourceLocal || !$isDestLocal) {
                    $frais = $frais * 1.50; // Alignement de la surtaxe à 50%
                }
            }

            $totalA_Debiter = $montant + $frais;
            if ($clientSource['solde'] < $totalA_Debiter) {
                return redirect()->back()->with('error', "Solde insuffisant (Montant + Frais = " . number_format($totalA_Debiter, 2, ',', ' ') . " Ar).");
            }

            $nouveauSolde = $clientSource['solde'] - $totalA_Debiter;
            $clientModel->update($clientId, ['solde' => $nouveauSolde]);

            // Historisation retrait
            $db->table('transaction_log')->insert([
                'id_type_operation' => $typeId,
                'id_client_source'  => $clientId,
                'id_client_dest'    => null,
                'telephone_dest'    => !empty($destPhone) ? $destPhone : null,
                'montant'           => $montant,
                'frais'             => $frais,
                'date_transaction'  => date('Y-m-d H:i:s')
            ]);
        }

        // 3. TRANSFERT (MULTI-DESTINATAIRES INCLUS)
        elseif (str_contains($typeNom, 'transfert') || str_contains($typeNom, 'envoi')) {
            if (empty($destPhone)) {
                return redirect()->back()->with('error', 'Veuillez saisir au moins un numéro de destinataire.');
            }

            // Extraction et nettoyage des numéros
            $destinatairesRaw = explode(',', $destPhone);
            $destinatairesList = [];
            foreach ($destinatairesRaw as $d) {
                $d = trim($d);
                if (!empty($d)) {
                    $destinatairesList[] = $d;
                }
            }
            $destinatairesList = array_values(array_unique($destinatairesList));
            $nbDest = count($destinatairesList);

            if ($nbDest === 0) {
                return redirect()->back()->with('error', 'Numéro(s) de destinataire invalide(s).');
            }

            if (in_array($clientSource['telephone'], $destinatairesList)) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas inclure votre propre numéro dans le transfert.');
            }

            // --- RESTRICTION : ENVOI MULTIPLE AUTORISÉ UNIQUEMENT VERS TELMA (034, 038) ---
            if ($nbDest > 1) {
                $prefixesTelma = ['034', '038'];
                foreach ($destinatairesList as $phone) {
                    $prefixe = substr($phone, 0, 3);
                    if (!in_array($prefixe, $prefixesTelma)) {
                        return redirect()->back()->with('error', "L'envoi multiple est uniquement autorisé vers les numéros Telma (034 ou 038). Le numéro $phone est refusé.");
                    }
                }
            }

            // Division du montant global
            $montantParPersonne = $montant / $nbDest;

            // Détection réseau de la source
            $prefixeSourceCode = substr($clientSource['telephone'], 0, 3);
            $isSourceLocal = $db->table('prefixe')->where('code', $prefixeSourceCode)->countAllResults() > 0;

            // Type retrait pour l'option frais inclus
            $typeRetraitId = null;
            if ($inclureRetrait) {
                $typeRetrait = $db->table('type_operation')->like('nom', 'retrait')->get()->getRowArray();
                $typeRetraitId = $typeRetrait ? $typeRetrait['id'] : null;
            }

            $totalGeneralA_Debiter = 0;
            $simulations = [];

            // Calcul des frais par destinataire
            foreach ($destinatairesList as $phone) {
                // Frais de transfert
                $baremeTransfert = $db->table('bareme_frais')
                    ->where('id_type_operation', $typeId)
                    ->where('montant_min <=', $montantParPersonne)
                    ->where('montant_max >=', $montantParPersonne)
                    ->get()->getRowArray();
                $fraisTransfert = $baremeTransfert ? (float) $baremeTransfert['frais'] : 0.0;

                // Frais de retrait facultatifs
                $fraisRetrait = 0.0;
                if ($inclureRetrait && $typeRetraitId) {
                    $baremeRetrait = $db->table('bareme_frais')
                        ->where('id_type_operation', $typeRetraitId)
                        ->where('montant_min <=', $montantParPersonne)
                        ->where('montant_max >=', $montantParPersonne)
                        ->get()->getRowArray();
                    $fraisRetrait = $baremeRetrait ? (float) $baremeRetrait['frais'] : 0.0;
                }

                // Application surtaxe inter-opérateur (+50%)
                $prefixeDestCode = substr($phone, 0, 3);
                $isDestLocal = $db->table('prefixe')->where('code', $prefixeDestCode)->countAllResults() > 0;

                if (!$isSourceLocal || !$isDestLocal) {
                    $fraisTransfert *= 1.50;
                    if ($fraisRetrait > 0) {
                        $fraisRetrait *= 1.50;
                    }
                }

                $fraisTotaux = $fraisTransfert + $fraisRetrait;
                $totalGeneralA_Debiter += ($montantParPersonne + $fraisTotaux);

                $simulations[] = [
                    'phone'   => $phone,
                    'montant' => $montantParPersonne,
                    'frais'   => $fraisTotaux
                ];
            }

            // Vérification solde
            if ($clientSource['solde'] < $totalGeneralA_Debiter) {
                return redirect()->back()->with('error', "Solde insuffisant pour ce transfert. Total requis (Montant + Frais) : " . number_format($totalGeneralA_Debiter, 2, ',', ' ') . " Ar.");
            }

            // Execution : Débit expéditeur
            $nouveauSoldeExpediteur = $clientSource['solde'] - $totalGeneralA_Debiter;
            $clientModel->update($clientId, ['solde' => $nouveauSoldeExpediteur]);

            // Execution : Crédit destinataires + Historisation
            foreach ($simulations as $sim) {
                $destinataire = $clientModel->where('telephone', $sim['phone'])->first();
                $destId = null;

                if ($destinataire) {
                    $destId = $destinataire['id'];
                    $clientModel->update($destId, ['solde' => $destinataire['solde'] + $sim['montant']]);
                }

                $db->table('transaction_log')->insert([
                    'id_type_operation' => $typeId,
                    'id_client_source'  => $clientId,
                    'id_client_dest'    => $destId,
                    'telephone_dest'    => $sim['phone'],
                    'montant'           => $sim['montant'],
                    'frais'             => $sim['frais'],
                    'date_transaction'  => date('Y-m-d H:i:s')
                ]);
            }

            $messageSuccess = $nbDest > 1 
                ? "Transfert groupé effectué vers $nbDest destinataires Telma (" . number_format($montantParPersonne, 2, ',', ' ') . " Ar chacun)." 
                : "Transfert effectué avec succès.";

            return redirect()->to('/client/dashboard')->with('success', $messageSuccess);
        }

        return redirect()->to('/client/dashboard')->with('success', 'Opération effectuée avec succès.');
    }
}