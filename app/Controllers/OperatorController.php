<?php

namespace App\Controllers;

use App\Models\ClientModel;

class OperatorController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login/admin')->with('error', 'Accès réservé à l\'administrateur.');
        }

        $db = \Config\Database::connect();
        $clientModel = new ClientModel();

        // 1. Récupérer les préfixes autorisés / locaux
        $prefixes = $db->table('prefixe')->get()->getResultArray();
        $prefixesLocaux = array_column($prefixes, 'code');

        // 2. Récupérer le barème des frais
        $baremes = $db->table('bareme_frais')
            ->select('bareme_frais.*, type_operation.nom as type_nom')
            ->join('type_operation', 'type_operation.id = bareme_frais.id_type_operation')
            ->get()->getResultArray();

        // 3. Récupérer les types d'opérations
        $types = $db->table('type_operation')->get()->getResultArray();

        // 4. Historique général des transactions
        $transactions = $db->table('transaction_log')
            ->select('transaction_log.*, type_operation.nom as type_nom, c1.telephone as client_source, c2.telephone as client_dest')
            ->join('type_operation', 'type_operation.id = transaction_log.id_type_operation')
            ->join('client c1', 'c1.id = transaction_log.id_client_source')
            ->join('client c2', 'c2.id = transaction_log.id_client_dest', 'left')
            ->orderBy('transaction_log.id', 'DESC')
            ->get()->getResultArray();

        // 5. Calcul des clients de l'opérateur local
        $allClients = $clientModel->findAll();
        $nbClientsLocaux = 0;

        foreach ($allClients as $client) {
            $prefixe = substr($client['telephone'], 0, 3);
            if (in_array($prefixe, $prefixesLocaux)) {
                $nbClientsLocaux++;
            }
        }

        // 6. Calcul des gains (Mon Opérateur vs Autres Opérateurs)
        $caMonOperateur = 0.0;
        $caAutresOperateurs = 0.0;

        foreach ($transactions as $t) {
            if ((float)$t['frais'] > 0) {
                $prefixeSource = substr($t['client_source'], 0, 3);
                $prefixeDest   = $t['client_dest'] ? substr($t['client_dest'], 0, 3) : '';

                $isSourceLocal = in_array($prefixeSource, $prefixesLocaux);
                $isDestLocal   = !empty($prefixeDest) ? in_array($prefixeDest, $prefixesLocaux) : true;

                // Si inter-opérateur (au moins un préfixe externe)
                if (!$isSourceLocal || !$isDestLocal) {
                    $fraisTotal = (float)$t['frais'];
                    $fraisBase  = $fraisTotal / 1.10;
                    $commission = $fraisTotal - $fraisBase;

                    $caMonOperateur += $fraisBase;
                    $caAutresOperateurs += $commission;
                } else {
                    // Transaction locale
                    $caMonOperateur += (float)$t['frais'];
                }
            }
        }

        return view('operator/dashboard', [
            'prefixes'           => $prefixes,
            'baremes'            => $baremes,
            'types'              => $types,
            'transactions'       => $transactions,
            'nbClientsLocaux'    => $nbClientsLocaux,
            'totalClients'       => count($allClients),
            'caMonOperateur'     => $caMonOperateur,
            'caAutresOperateurs' => $caAutresOperateurs,
            'caTotal'            => $caMonOperateur + $caAutresOperateurs
        ]);
    }

    // --- GESTION DES PRÉFIXES ---
    public function addPrefixe()
    {
        $code = trim($this->request->getPost('code'));
        if (!empty($code)) {
            $db = \Config\Database::connect();
            $db->table('prefixe')->insert(['code' => $code]);
        }
        return redirect()->to('/operator')->with('success', 'Préfixe ajouté avec succès.');
    }

    public function deletePrefixe($id)
    {
        $db = \Config\Database::connect();
        $db->table('prefixe')->where('id', $id)->delete();
        return redirect()->to('/operator')->with('success', 'Préfixe supprimé.');
    }

    // --- GESTION DU BARÈME ---
    public function saveBareme()
    {
        $db = \Config\Database::connect();
        $data = [
            'id_type_operation' => $this->request->getPost('id_type_operation'),
            'montant_min'       => $this->request->getPost('montant_min'),
            'montant_max'       => $this->request->getPost('montant_max'),
            'frais'             => $this->request->getPost('frais'),
        ];

        $db->table('bareme_frais')->insert($data);
        return redirect()->to('/operator')->with('success', 'Tranche de barème ajoutée.');
    }

    public function deleteBareme($id)
    {
        $db = \Config\Database::connect();
        $db->table('bareme_frais')->where('id', $id)->delete();
        return redirect()->to('/operator')->with('success', 'Tranche supprimée.');
    }
}