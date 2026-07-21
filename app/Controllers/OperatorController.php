<?php

namespace App\Controllers;

use App\Models\ClientModel;

class OperatorController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'operator' && session()->get('role') !== 'admin') {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $clientModel = new ClientModel();

        // 1. Récupération des préfixes locaux (Telma)
        $prefixesBD = $db->table('prefixe')->select('code')->get()->getResultArray();
        $prefixesLocaux = array_column($prefixesBD, 'code');

        // 2. Statistiques clients locaux
        $totalClients = $clientModel->countAllResults();
        $nbClientsLocaux = 0;
        $allClients = $clientModel->findAll();
        foreach ($allClients as $c) {
            if (in_array(substr($c['telephone'], 0, 3), $prefixesLocaux)) {
                $nbClientsLocaux++;
            }
        }

        // 3. Calcul financier selon la documentation d'interopérabilité
        $transactions = $db->table('transaction_log')
            ->select('transaction_log.*, type_operation.nom as type_nom, c1.telephone as client_source, c2.telephone as client_dest_tel')
            ->join('type_operation', 'type_operation.id = transaction_log.id_type_operation')
            ->join('client c1', 'c1.id = transaction_log.id_client_source')
            ->join('client c2', 'c2.id = transaction_log.id_client_dest', 'left')
            ->orderBy('transaction_log.id', 'DESC')
            ->get()->getResultArray();

        $caMonOperateur     = 0.0; // Part conservée par Telma (Expéditeur)
        $caAutresOperateurs = 0.0; // Part reversée à l'opérateur destinataire (Commission d'interopérabilité)

        // Définition de la surtaxe à 50%
        $tauxSurtaxe = 0.50; 
        $facteur     = 1 + $tauxSurtaxe; // 1.50

        foreach ($transactions as $tx) {
            $fraisTotal = (float) $tx['frais'];
            $sourceTel  = $tx['client_source'];
            $destTel    = !empty($tx['client_dest_tel']) ? $tx['client_dest_tel'] : $tx['telephone_dest'];

            $prefixeSource = substr($sourceTel, 0, 3);
            $prefixeDest   = !empty($destTel) ? substr($destTel, 0, 3) : '';

            $isSourceLocal = in_array($prefixeSource, $prefixesLocaux);
            $isDestLocal   = !empty($prefixeDest) ? in_array($prefixeDest, $prefixesLocaux) : true;

            // Cas de Transfert Inter-Opérateur (ex: Telma -> Orange/Airtel)
            if (!$isSourceLocal || !$isDestLocal) {
                // Surtaxe de 50% : La part de base est calculée en divisant par 1.50
                $partTelma              = $fraisTotal / $facteur; 
                $partCommissionDistant = $fraisTotal - $partTelma; // Les 50% de surtaxe reversés au réseau partenaire

                $caMonOperateur     += $partTelma;
                $caAutresOperateurs += $partCommissionDistant;
            } else {
                // Transaction Intra-Réseau (Telma -> Telma) : 100% pour Telma
                $caMonOperateur += $fraisTotal;
            }
        }

        $prefixes = $db->table('prefixe')->get()->getResultArray();
        $types    = $db->table('type_operation')->get()->getResultArray();
        $baremes  = $db->table('bareme_frais')
            ->select('bareme_frais.*, type_operation.nom as type_nom')
            ->join('type_operation', 'type_operation.id = bareme_frais.id_type_operation')
            ->get()->getResultArray();

        return view('operator/dashboard', [
            'nbClientsLocaux'    => $nbClientsLocaux,
            'totalClients'       => $totalClients,
            'caMonOperateur'     => $caMonOperateur,
            'caAutresOperateurs' => $caAutresOperateurs,
            'prefixes'           => $prefixes,
            'types'              => $types,
            'baremes'            => $baremes,
            'transactions'       => $transactions
        ]);
    }
}