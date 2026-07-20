<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Page d'accueil par défaut
$routes->get('/', 'Home::index');

// Connexion Directe Admin / Opérateur
$routes->get('/login/admin', 'AuthController::adminLogin');

// Formulaire et traitement Client
$routes->get('/login/client', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');

// Déconnexion
$routes->get('/logout', 'AuthController::logout');

// --- ROUTES ESPACE CLIENT ---
$routes->get('/client/dashboard', 'ClientController::dashboard');
$routes->post('/client/transaction', 'ClientController::transaction');

// --- ROUTES ESPACE OPERATEUR ---
$routes->get('/operator', 'OperatorController::index');

// Prefix
$routes->post('/operator/prefixe/add', 'OperatorController::addPrefixe');
$routes->get('/operator/prefixe/delete/(:num)', 'OperatorController::deletePrefixe/$1');

// Bareme
$routes->post('/operator/bareme/save', 'OperatorController::saveBareme');
$routes->get('/operator/bareme/delete/(:num)', 'OperatorController::deleteBareme/$1');