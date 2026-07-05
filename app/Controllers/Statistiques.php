<?php

namespace App\Controllers;

use App\Models\EntreeMatierePremiereModel;
use App\Models\SortieModel;

class Statistiques extends BaseController
{
    public function index()
    {
        $entreeModel = new EntreeMatierePremiereModel();
        $sortieModel = new SortieModel();

        $data['entreesParDate']        = $entreeModel->getStatistiquesParDate();
        $data['entreesParFournisseur'] = $entreeModel->getStatistiquesParFournisseur();
        $data['sortiesParDate']        = $sortieModel->getStatistiquesParDate();
        $data['sortiesParDestinataire'] = $sortieModel->getStatistiquesParDestinataire();

        $data['totalLitresEntre'] = array_sum(array_column($data['entreesParDate'], 'total_litres'));
        $data['totalBocauxVendus'] = array_sum(array_column($data['sortiesParDate'], 'total_quantite'));
        $data['fournisseurPrincipal'] = $data['entreesParFournisseur'][0]['fournisseur_nom'] ?? 'Aucun';
        $data['tauxVente'] = $data['totalLitresEntre'] > 0
            ? round(($data['totalBocauxVendus'] / $data['totalLitresEntre']) * 100, 2)
            : 0;
        $data['stockEstime'] = max(0, $data['totalLitresEntre'] - $data['totalBocauxVendus']);

        return view('statistiques/index', $data);
    }
}