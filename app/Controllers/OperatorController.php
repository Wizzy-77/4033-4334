<?php

namespace App\Controllers;

class OperatorController extends BaseController
{
    // Affichage global pour l'administrateur
    public function index()
    {
        // Sécurité : Vérifier le rôle
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Accès réservé à l\'administrateur.');
        }

        $db = \Config\Database::connect();

        // 1. Récupération des préfixes
        $prefixes = $db->table('prefixe')->get()->getResultArray();

        // 2. Récupération des types d'opérations
        $types = $db->table('type_operation')->get()->getResultArray();

        // 3. Récupération des barèmes de frais
        $baremes = $db->table('bareme_frais')
                      ->select('bareme_frais.*, type_operation.nom as type_nom')
                      ->join('type_operation', 'type_operation.id = bareme_frais.id_type_operation')
                      ->get()->getResultArray();

        // 4. Récupération de l'historique global des transactions
        $transactions = $db->table('transaction_log')
                           ->select('transaction_log.*, type_operation.nom as type_nom, c1.telephone as client_source, c2.telephone as client_dest')
                           ->join('type_operation', 'type_operation.id = transaction_log.id_type_operation')
                           ->join('client c1', 'c1.id = transaction_log.id_client_source')
                           ->join('client c2', 'c2.id = transaction_log.id_client_dest', 'left')
                           ->orderBy('transaction_log.id', 'DESC')
                           ->get()->getResultArray();

        return view('operator/dashboard', [
            'prefixes'     => $prefixes,
            'types'        => $types,
            'baremes'      => $baremes,
            'transactions' => $transactions
        ]);
    }

    // Ajouter un préfixe
    public function addPrefixe()
    {
        $code = trim($this->request->getPost('code') ?? '');

        if (!empty($code)) {
            $db = \Config\Database::connect();
            $db->table('prefixe')->ignore(true)->insert(['code' => $code]);
        }

        return redirect()->to('/operator')->with('success', 'Préfixe ajouté avec succès.');
    }

    // Supprimer un préfixe
    public function deletePrefixe($id)
    {
        $db = \Config\Database::connect();
        $db->table('prefixe')->where('id', $id)->delete();

        return redirect()->to('/operator')->with('success', 'Préfixe supprimé.');
    }

    // Sauvegarder/Ajouter un barème de frais
    public function saveBareme()
    {
        $data = [
            'id_type_operation' => $this->request->getPost('id_type_operation'),
            'montant_min'        => $this->request->getPost('montant_min'),
            'montant_max'        => $this->request->getPost('montant_max'),
            'frais'              => $this->request->getPost('frais')
        ];

        $db = \Config\Database::connect();
        $db->table('bareme_frais')->insert($data);

        return redirect()->to('/operator')->with('success', 'Tranche de barème ajoutée.');
    }

    // Supprimer un barème de frais
    public function deleteBareme($id)
    {
        $db = \Config\Database::connect();
        $db->table('bareme_frais')->where('id', $id)->delete();

        return redirect()->to('/operator')->with('success', 'Tranche supprimée.');
    }
}