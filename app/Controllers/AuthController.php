<?php

namespace App\Controllers;

use App\Models\ClientModel;

class AuthController extends BaseController
{
    // Affiche la vue du formulaire de connexion
    public function login()
    {
        return view('auth/login');
    }

    // Connexion directe pour l'Administrateur / Opérateur
    public function adminLogin()
    {
        session()->set([
            'isLoggedIn' => true,
            'role'       => 'admin',
            'user'       => 'Administrateur'
        ]);

        return redirect()->to('/operator');
    }

    // Traitement de la connexion du client par numéro de téléphone
    public function processLogin()
    {
        $telephone = trim($this->request->getPost('telephone') ?? '');

        // 1. Vérification du format (exactement 10 chiffres, commence par 0)
        if (!preg_match('/^0[0-9]{9}$/', $telephone)) {
            return redirect()->back()->with('error', 'Le numéro doit contenir 10 chiffres et commencer par 0.');
        }

        // 2. Extraction du préfixe (les 3 premiers chiffres, ex: "034", "038")
        $prefixe = substr($telephone, 0, 3);

        // 3. Vérification si le préfixe existe dans la table 'prefixe' (colonne 'code')
        $db = \Config\Database::connect();
        $prefixeExiste = $db->table('prefixe')
                            ->where('code', $prefixe)
                            ->countAllResults();

        if ($prefixeExiste === 0) {
            return redirect()->back()->with('error', 'Seuls les numéros Yas seront autorisés.');
        }

        // 4. Inscription / Création automatique du compte client s'il n'existe pas encore
        $clientModel = new ClientModel();
        $client = $clientModel->where('telephone', $telephone)->first();

        // Si le client n'existe pas encore, on le crée avec 0 Ar (ou solde par défaut)
        if (!$client) {
            $clientId = $clientModel->insert([
                'telephone' => $telephone,
                'solde'     => 0 
            ]);
            $client = $clientModel->find($clientId);
        }

        // 5. Enregistrement des informations en session et redirection
        session()->set([
            'isLoggedIn' => true,
            'role'       => 'client',
            'client_id'  => $client['id'],
            'telephone'  => $client['telephone']
        ]);

        return redirect()->to('/client/dashboard');
    }

    // Déconnexion de la session et retour à la page d'accueil
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}