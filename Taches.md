# Répartition des travaux Exam S4

## Version 1 (v1)

### 4334
- Conception et initialisation de la base
- Développement du module Opérateur
- Mise en place de la gestion des préfixes et des barèmes de frais modifiables par tranche.
- Affichage de la situation des gains (frais récoltés) et du solde des comptes clients.

### 4033
- Développement du module d'Authentification 
- Développement du module Client 
- Implémentation des transactions : Dépôt, Retrait avec calcul de frais, Transfert inter-comptes.


### 4033 - 4334
- Intégration de l'interface client Bootstrap (`dashboard.php`) et affichage de l'historique des opérations.

## Version 2 (v2)
### 4334
Configuration % en plus de commissions pour les transferts vers les autres opérateurs 
Sur la page “Situation gain via les différents frais” , séparer opérateur et autres opérateurs
Situation des montants à envoyer à chaque opérateur

### 4033
Configuration des préfixes valable pour les autres opérateurs (ex: 032 et 031, …)
Option inclure frais de retrait lors de l’envoi
Envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro)

## Version 3 (v3)
### 4334

### 4033
Mettre dans la base de donnee
Mettre une page pour epargne
le client entre dans la page et dit combien de pourcentage
%= 5% si il y a un trandfer reçu