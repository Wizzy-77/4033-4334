-- ============================================================================
-- FICHIER : donnees.sql
-- DESCRIPTION : Jeu de données de test pour ERP_AROVIA
-- ============================================================================

-- 1. DONNÉES DE BASE POUR LES NOUVELLES TABLES (ENTREPRISE & STATUT COMPLÉMENTS)
-- ============================================================================
INSERT INTO entreprise (nom, telephone, email) VALUES
('Hôtel des Baobabs', '+261 34 11 222 33', 'contact@baobab-hotel.mg'),
('Supermarché Jumbo Score', '+261 20 22 444 55', 'achats@jumbo.mg'),
('Distillerie du Vakinankaratra', '+261 32 07 888 99', 'prod@divak.mg'),
('Centrale d''Achat Malagasy', '+261 33 15 777 11', 'cam@moov.mg');

-- Note : Les statuts 'En cours', 'Signé', 'Expiré', 'Annulé' sont déjà insérés par ton script de structure.

-- 2. RH & UTILISATEURS COMPLÉMENTAIRES
-- ============================================================================
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role_id) VALUES
('Raza', 'Andry', 'compta@arovia.com', '$2y$10$changeme', 2), -- COMPTABLE
('Rakoto', 'Jean', 'magasin@arovia.com', '$2y$10$changeme', 3), -- MAGASINIER
('Randria', 'Marc', 'livreur1@arovia.com', '$2y$10$changeme', 4); -- LIVREUR

INSERT INTO employes (matricule, nom, prenom, telephone, email, adresse, poste, salaire_base, date_embauche, statut) VALUES
('EMP-001', 'Raza', 'Andry', '+261 34 55 111 22', 'andry@arovia.com', 'Ambohitrarahaba, Tana', 'Comptable', 1200000.00, '2025-01-10', 'ACTIF'),
('EMP-002', 'Rakoto', 'Jean', '+261 32 44 222 33', 'jean@arovia.com', 'Analamahitsy, Tana', 'Magasinier', 800000.00, '2025-01-15', 'ACTIF'),
('EMP-003', 'Randria', 'Marc', '+261 33 66 333 44', 'marc@arovia.com', 'Itaosy, Tana', 'Chauffeur Livreur', 750000.00, '2025-02-01', 'ACTIF'),
('EMP-004', 'Ravalo', 'Sitraka', '+261 34 77 444 55', 'sitraka@arovia.com', '67Ha, Tana', 'Ouvrier Spécialisé', 700000.00, '2025-02-15', 'ACTIF');

-- Contrats de test
INSERT INTO contrats (sujet, entreprise_id, statut_id, description, date_signature, date_expiration) VALUES
('Fourniture exclusive de bocaux 10cl pour les chambres', 1, 2, 'Contrat cadre pour 500 pièces par an', '2025-02-01', '2026-02-01'),
('Distribution Grand Public Bocaux 25cl et 50cl', 2, 2, 'Référencement dans tous les magasins de Tana', '2025-03-15', '2026-03-15'),
('Partenariat export test Miel de Madagascar', 4, 1, 'Négociation en cours pour le marché régional', '2026-02-10', '2027-02-10');

-- 3. FOURNISSEURS & ENTRÉES MATIÈRE PREMIÈRE (MIEL)
-- ============================================================================
INSERT INTO fournisseurs (nom, contact, telephone, email, localisation) VALUES
('Coopérative Apicole d''Antsirabe', 'Rabe Jean', '+261 34 88 999 00', 'coop.antsirabe@gmail.com', 'Antsirabe'),
('Apiculteur Solo Mananara', 'Solo', '+261 32 11 000 11', 'solo.miel@moov.mg', 'Mananara Nord'),
('Les Ruches du Sud', 'Mme Lala', '+261 33 22 111 22', 'ruches.sud@yahoo.fr', 'Fianarantsoa');

-- Simulation d'achats successifs pour faire évoluer le CUMP
-- Lot 1 : 500L à 12,000 Ar/L
INSERT INTO entrees_matiere_premiere (fournisseur_id, numero_lot, date_entree, quantite_litres, prix_unitaire, valeur_totale, cump_apres_entree)
VALUES (1, 'LOT-2025-01', '2025-01-20 09:00:00', 500.00, 12000.00, 6000000.00, 12000.00);

-- Lot 2 : 300L à 13,000 Ar/L (Le prix augmente, le CUMP va s'ajuster)
INSERT INTO entrees_matiere_premiere (fournisseur_id, numero_lot, date_entree, quantite_litres, prix_unitaire, valeur_totale, cump_apres_entree)
VALUES (2, 'LOT-2025-02', '2025-02-15 14:30:00', 300.00, 13000.00, 3900000.00, 12375.00);

-- Lot 3 : 1000L à 11,500 Ar/L
INSERT INTO entrees_matiere_premiere (fournisseur_id, numero_lot, date_entree, quantite_litres, prix_unitaire, valeur_totale, cump_apres_entree)
VALUES (3, 'LOT-2025-03', '2025-03-01 10:15:00', 1000.00, 11500.00, 11500000.00, 11888.89);

-- Mise à jour globale du stock de matière première après ces entrées (Stock virtuel restant fictif avant transformations)
UPDATE stock_matiere_premiere 
SET quantite_litres = 1800.00, valeur_stock = 21400000.00, cump_actuel = 11888.89, derniere_maj = NOW()
WHERE id = 1;


-- 4. LOGIQUE DE PRODUCTION (TRANSFORMATIONS)
-- ============================================================================
-- Transformation 1 : On utilise 200 Litres de miel pour produire des bocaux
INSERT INTO transformations (date_transformation, quantite_litres_utilisee, cump_applique, valeur_sortie)
VALUES ('2025-02-20 08:00:00', 200.00, 12375.00, 2475000.00);

INSERT INTO transformations_detail (transformation_id, type_bocal_id, quantite_produite) VALUES
(1, 1, 500),  -- 500 bocaux de 10cl = 50L
(1, 2, 400),  -- 400 bocaux de 25cl = 100L
(1, 3, 100);  -- 100 bocaux de 50cl = 50L

-- Transformation 2 : On utilise 500 Litres de miel
INSERT INTO transformations (date_transformation, quantite_litres_utilisee, cump_applique, valeur_sortie)
VALUES ('2025-03-10 11:00:00', 500.00, 11888.89, 5944445.00);

INSERT INTO transformations_detail (transformation_id, type_bocal_id, quantite_produite) VALUES
(2, 2, 400),  -- 400 bocaux de 25cl = 100L
(2, 3, 400),  -- 400 bocaux de 50cl = 200L
(2, 4, 200);  -- 200 bocaux de 1L = 200L

-- Mise à jour des stocks de produits finis disponibles après production
UPDATE stock_produit_fini SET quantite_disponible = 500 WHERE type_bocal_id = 1;
UPDATE stock_produit_fini SET quantite_disponible = 800 WHERE type_bocal_id = 2;
UPDATE stock_produit_fini SET quantite_disponible = 500 WHERE type_bocal_id = 3;
UPDATE stock_produit_fini SET quantite_disponible = 200 WHERE type_bocal_id = 4;
UPDATE stock_produit_fini SET quantite_disponible = 50  WHERE type_bocal_id = 5; -- Achat direct ou stock initial fictif


-- 5. CLIENTS, VENTES & LOGISTIQUE
-- ============================================================================
INSERT INTO clients (nom, type_client, telephone, email, adresse) VALUES
('Rabe Hasina', 'particulier', '+261 34 00 123 45', 'hasina@gmail.com', 'Ivandry'),
('Hôtel Carlton Anosy', 'hotel', '+261 20 22 260 00', 'info@carlton.mg', 'Anosy, Antananarivo'),
('Boutique Épice Fine Nosy Be', 'grossiste', '+261 32 45 789 12', 'nosybe@epices.mg', 'Hell-Ville, Nosy Be'),
('Mme Volona', 'touriste', '+261 33 89 456 12', 'volona@yahoo.com', 'Antsirabe');

-- Vente 1 : Client particulier au comptant (Espèces / Caisse)
INSERT INTO ventes (client_id, date_vente, montant_total, mode_paiement, statut)
VALUES (1, '2025-02-22 15:30:00', 90000.00, 'ESPECES', 'PAYE');

INSERT INTO vente_details (vente_id, type_bocal_id, quantite, prix_unitaire, total_ligne) VALUES
(1, 1, 1, 15000.00, 15000.00),
(1, 2, 3, 25000.00, 75000.00);

-- Vente 2 : Grosse commande Hôtel payée par Banque (BNI)
INSERT INTO ventes (client_id, date_vente, montant_total, mode_paiement, statut)
VALUES (2, '2025-03-12 10:00:00', 5500000.00, 'VIREMENT', 'PAYE');

INSERT INTO vente_details (vente_id, type_bocal_id, quantite, prix_unitaire, total_ligne) VALUES
(2, 1, 100, 15000.00, 1500000.00),
(2, 3, 100, 40000.00, 4000000.00);

-- Vente 3 : Mobile Money (MVola)
INSERT INTO ventes (client_id, date_vente, montant_total, mode_paiement, statut)
VALUES (4, '2025-03-15 16:45:00', 105000.00, 'MVOLA', 'PAYE');

INSERT INTO vente_details (vente_id, type_bocal_id, quantite, prix_unitaire, total_ligne) VALUES
(3, 1, 1, 15000.00, 15000.00),
(3, 2, 1, 25000.00, 25000.00),
(3, 3, 1, 40000.00, 40000.00),
(3, 2, 1, 25000.00, 25000.00);

-- Sorties diverses (Pertes ou Ventes directes hors système classique)
INSERT INTO sorties (date_sortie, type_bocal_id, quantite, motif, commentaire, destinataire_type, destinataire_nom, prix_vente_unitaire, valeur_totale) VALUES
('2025-02-25 11:00:00', 2, 3, 'Perte', 'Bocaux brisés pendant le rangement au magasin', NULL, NULL, 0, 0),
('2025-03-18 14:00:00', 3, 5, 'Vente Directe', 'Vente directe au salon de la gastronomie', 'particulier', 'Visiteurs Salon', 40000.00, 200000.00);

-- Logistique & Livraisons
INSERT INTO livreurs (nom, telephone, vehicule, disponible) VALUES
('Ranja Delivery', '+261 34 99 555 11', 'Scooter Sym', TRUE),
('Trans-Arovia Rapide', '+261 32 88 444 22', 'Camionnette Renault Kangoo', TRUE);

INSERT INTO livraisons (vente_id, livreur_id, date_prevue, date_effective, adresse_livraison, statut) VALUES
(2, 2, '2025-03-13 09:00:00', '2025-03-13 10:30:00', 'Hôtel Carlton, Réception stock, Anosy', 'LIVRE');

INSERT INTO disponibilites_livreurs (livreur_id, date_debut, date_fin) VALUES
(1, '2025-03-01 08:00:00', '2025-03-31 18:00:00'),
(2, '2025-03-01 07:00:00', '2025-03-31 19:00:00');


-- 6. RESSOUCES HUMAINES (SALAIRES & PLANNING)
-- ============================================================================
-- Paiements de salaires pour Février 2025
INSERT INTO paiements_salaires (employe_id, mois, annee, salaire_base, primes, deductions, montant_paye, date_paiement, commentaire) VALUES
(1, 2, 2025, 1200000.00, 50000.00, 0.00, 1250000.00, '2025-02-28 16:00:00', 'Salaire Février + Prime performance'),
(2, 2, 2025, 800000.00, 0.00, 20000.00, 780000.00, '2025-02-28 16:05:00', 'Salaire Février - Avance sur salaire'),
(3, 2, 2025, 750000.00, 30000.00, 0.00, 780000.00, '2025-02-28 16:10:00', 'Salaire Février + Prime carburant');

-- Événements Planning
INSERT INTO planning (employe_id, date_debut, date_fin, type_evenement, description) VALUES
(1, '2025-03-05 08:00:00', '2025-03-05 17:00:00', 'FORMATION', 'Formation sur le nouveau module fiscal de l''ERP'),
(4, '2025-03-12 08:00:00', '2025-03-14 17:00:00', 'CONGE', 'Congé exceptionnel pour événement familial');


-- 7. COMPTABILITÉ & TRÉSORERIE (MOUVEMENTS FINANCIERS)
-- ============================================================================
-- Alimentation initiale des comptes pour pouvoir travailler (Soldes de départ fictifs)
UPDATE comptes_tresorerie SET solde = 1500000.00 WHERE id = 1; -- CAISSE
UPDATE comptes_tresorerie SET solde = 25000000.00 WHERE id = 2; -- BNI
UPDATE comptes_tresorerie SET solde = 4500000.00 WHERE id = 3; -- MVOLA

-- Enregistrement des mouvements correspondants aux opérations précédentes 
-- Flux d'achats de matières premières
INSERT INTO mouvements_financiers (compte_id, type, categorie, montant, description, date_transaction) VALUES
(2, 'depense', 'Achat Matière Première', 6000000.00, 'Paiement Lot LOT-2025-01 à Coop d Antsirabe', '2025-01-20 09:30:00'),
(3, 'depense', 'Achat Matière Première', 3900000.00, 'Paiement Mobile Lot LOT-2025-02 à Solo', '2025-02-15 15:00:00'),
(2, 'depense', 'Achat Matière Première', 11500000.00, 'Virement Chèque Lot LOT-2025-03 aux Ruches du Sud', '2025-03-01 11:00:00');

-- Flux de Paiement des Salaires
INSERT INTO mouvements_financiers (compte_id, type, categorie, montant, description, date_transaction) VALUES
(2, 'depense', 'Salaires & Charges', 1250000.00, 'Salaire Février Andry Raza', '2025-02-28 16:00:00'),
(2, 'depense', 'Salaires & Charges', 780000.00, 'Salaire Février Jean Rakoto', '2025-02-28 16:05:00'),
(2, 'depense', 'Salaires & Charges', 780000.00, 'Salaire Février Marc Randria', '2025-02-28 16:10:00');

-- Flux d'encaissement des Ventes
INSERT INTO mouvements_financiers (compte_id, type, categorie, montant, description, date_transaction) VALUES
(1, 'recette', 'Ventes Produits Finis', 90000.00, 'Encaissement Espèces - Vente #1 Client Hasina', '2025-02-22 15:35:00'),
(2, 'recette', 'Ventes Produits Finis', 5500000.00, 'Virement reçu - Vente #2 Hôtel Carlton', '2025-03-12 10:15:00'),
(3, 'recette', 'Ventes Produits Finis', 105000.00, 'Paiement MVola - Vente #3 Mme Volona', '2025-03-15 16:50:00');

-- Autre dépense diverse (Frais généraux)
INSERT INTO mouvements_financiers (compte_id, type, categorie, montant, description, date_transaction) VALUES
(1, 'depense', 'Frais Généraux', 45000.00, 'Achat fournitures de bureau et étiquettes', '2025-03-02 14:20:00');


-- Recalcul dynamique et réel des soldes des comptes de trésorerie après tous ces mouvements
UPDATE comptes_tresorerie SET solde = 1500000.00 + 90000.00 - 45000.00 WHERE id = 1; -- CAISSE : 1,545,000 Ar
UPDATE comptes_tresorerie SET solde = 25000000.00 - 6000000.00 - 11500000.00 - 1250000.00 - 780000.00 - 780000.00 + 5500000.00 WHERE id = 2; -- BNI : 10,190,000 Ar
UPDATE comptes_tresorerie SET solde = 4500000.00 - 3900000.00 + 105000.00 WHERE id = 3; -- MVOLA : 705,000 Ar