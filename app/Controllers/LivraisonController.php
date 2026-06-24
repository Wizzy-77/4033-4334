<?php

namespace App\Controllers;

use App\Models\LivreurModel;
use App\Models\LivraisonModel;

class LivraisonController extends BaseController
{
    protected $livreurModel;
    protected $livraisonModel;

    public function __construct()
    {
        $this->livreurModel = new LivreurModel();
        $this->livraisonModel = new LivraisonModel();
    }

    // TABLEAU DE BORD DE DISTRIBUTION (Livraisons du jour en cours et effectuées)
    public function index()
    {
        $data = [
            'livraisons_en_cours' => $this->livraisonModel->getLivraisonsWithLivreur(['EN_COURS', 'EN_ATTENTE']),
            'livraisons_faites'   => $this->livraisonModel->getLivraisonsWithLivreur(['EFFECTUEE']),
            'stats'               => $this->livraisonModel->getStats()
        ];

        return view('livraisons/index', $data);
    }

    // HISTORIQUE DE TOUTES LES LIVRAISONS (Effectuées, Annulées, etc.)
    public function historique()
    {
        $data = [
            'livraisons' => $this->livraisonModel->getLivraisonsWithLivreur()
        ];
        return view('livraisons/historique', $data);
    }

    // FORMULAIRE DE CRÉATION DE LIVRAISON (Avec recommandation de livreur)
    public function create()
    {
        $data = [
            'livreurs_dispo' => $this->livreurModel->getDisponibles()
        ];
        return view('livraisons/create', $data);
    }

    // ENREGISTRER LA LIVRAISON
    public function store()
    {
        $this->livraisonModel->save([
            'vente_id'          => $this->request->getPost('vente_id'),
            'livreur_id'        => $this->request->getPost('livreur_id'),
            'date_prevue'       => $this->request->getPost('date_prevue'),
            'adresse_livraison' => $this->request->getPost('adresse_livraison'),
            'statut'            => 'EN_ATTENTE'
        ]);

        return redirect()->to('/livraisons');
    }

    // CHANGER LE STATUT RAPIDEMENT (Effectuer, Annuler, Mettre en cours)
    public function updateStatut($id, $statut)
    {
        $updateData = ['statut' => strtoupper($statut)];
        if (strtoupper($statut) === 'EFFECTUEE') {
            $updateData['date_effective'] = date('Y-m-d H:i:s');
        }

        $this->livraisonModel->update($id, $updateData);
        return redirect()->to('/livraisons');
    }

    // API AJAX POUR L'HISTORIQUE ET RECHERCHE
    public function ajaxList()
    {
        $request = service('request');
        $search  = $request->getGet('search');
        $statut  = $request->getGet('statut');

        $builder = $this->livraisonModel->select('livraisons.*, livreurs.nom as livreur_nom')
                                        ->join('livreurs', 'livreurs.id = livraisons.livreur_id', 'left');

        if (!empty($statut)) {
            $builder->where('livraisons.statut', $statut);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('adresse_livraison', $search)
                    ->orLike('livreurs.nom', $search)
                    ->groupEnd();
        }

        return $this->response->setJSON($builder->findAll());
    }
}