<?php

namespace App\Controllers;

use App\Models\ClientModel;

class ClientController extends BaseController
{
    // Affiche le tableau de bord client et l'historique de ses transactions
    public function dashboard()
    {
        if (session()->get('role') !== 'client') {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter en tant que client.');
        }

        $clientId = session()->get('client_id');
        $clientModel = new ClientModel();
        $client = $clientModel->find($clientId);

        $db = \Config\Database::connect();

        // 1. Récupérer les types d'opérations (Dépôt, Retrait, Transfert)
        $types = $db->table('type_operation')->get()->getResultArray();

        // 2. Récupérer l'historique des transactions du client (envoyées ou reçues)
        $transactions = $db->table('transaction_log')
            ->select('transaction_log.*, type_operation.nom as type_nom, c1.telephone as source_tel, c2.telephone as dest_tel')
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

    // Traitement des opérations (Dépôt, Retrait, Transfert)
    public function transaction()
    {
        if (session()->get('role') !== 'client') {
            return redirect()->to('/');
        }

        $clientId     = session()->get('client_id');
        $typeId       = $this->request->getPost('id_type_operation');
        $montant      = (float) $this->request->getPost('montant');
        $destPhone    = trim($this->request->getPost('destinataire') ?? '');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $db = \Config\Database::connect();
        $clientModel = new ClientModel();
        $clientSource = $clientModel->find($clientId);

        // Récupérer le type d'opération
        $typeOp = $db->table('type_operation')->where('id', $typeId)->get()->getRowArray();
        if (!$typeOp) {
            return redirect()->back()->with('error', 'Type d\'opération invalide.');
        }

        $typeNom = strtolower($typeOp['nom']);

        // --- CALCUL DES FRAIS SELON LE BARÈME ---
        $bareme = $db->table('bareme_frais')
            ->where('id_type_operation', $typeId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->get()->getRowArray();

        $frais = $bareme ? (float) $bareme['frais'] : 0.0;
        $destId = null;

        // --- LOGIQUE SELON LE TYPE D'OPÉRATION ---

        // 1. DÉPÔT
        if (str_contains($typeNom, 'dépôt') || str_contains($typeNom, 'depot')) {
            $nouveauSolde = $clientSource['solde'] + $montant;
            $clientModel->update($clientId, ['solde' => $nouveauSolde]);
        }

        // 2. RETRAIT
        elseif (str_contains($typeNom, 'retrait')) {
            $totalA_Debiter = $montant + $frais;
            if ($clientSource['solde'] < $totalA_Debiter) {
                return redirect()->back()->with('error', "Solde insuffisant (Montant + Frais = $totalA_Debiter Ar).");
            }

            $nouveauSolde = $clientSource['solde'] - $totalA_Debiter;
            $clientModel->update($clientId, ['solde' => $nouveauSolde]);
        }

        // 3. TRANSFERT
        elseif (str_contains($typeNom, 'transfert')) {
            if (empty($destPhone)) {
                return redirect()->back()->with('error', 'Veuillez saisir le numéro du destinataire.');
            }

            if ($destPhone === $clientSource['telephone']) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas effectuer un transfert vers vous-même.');
            }

            $destinataire = $clientModel->where('telephone', $destPhone)->first();
            if (!$destinataire) {
                return redirect()->back()->with('error', 'Numéro destinataire introuvable.');
            }

            $totalA_Debiter = $montant + $frais;
            if ($clientSource['solde'] < $totalA_Debiter) {
                return redirect()->back()->with('error', "Solde insuffisant (Montant + Frais = $totalA_Debiter Ar).");
            }

            // Débit de l'expéditeur et crédit du destinataire
            $destId = $destinataire['id'];
            $clientModel->update($clientId, ['solde' => $clientSource['solde'] - $totalA_Debiter]);
            $clientModel->update($destId, ['solde' => $destinataire['solde'] + $montant]);
        }

        // Enregistrement dans l'historique log
        $db->table('transaction_log')->insert([
            'id_type_operation' => $typeId,
            'id_client_source'  => $clientId,
            'id_client_dest'    => $destId,
            'montant'           => $montant,
            'frais'             => $frais,
            'date_transaction'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/client/dashboard')->with('success', 'Opération effectuée avec succès.');
    }
}