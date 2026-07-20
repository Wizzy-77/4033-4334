-- Table des préfixes autorisés par l'opérateur (ex: 033, 037)
CREATE TABLE IF NOT EXISTS prefixe (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(10) NOT NULL UNIQUE
);

-- Table des types d'opérations (Dépôt, Retrait, Transfert)
CREATE TABLE IF NOT EXISTS type_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);

-- Table des tranches de frais
CREATE TABLE IF NOT EXISTS bareme_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id)
);

-- Table des clients
CREATE TABLE IF NOT EXISTS client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone VARCHAR(20) NOT NULL UNIQUE,
    solde REAL NOT NULL DEFAULT 0.0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des transactions
CREATE TABLE IF NOT EXISTS transaction_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    id_client_source INTEGER NOT NULL,
    id_client_dest INTEGER NULL, -- Utile uniquement pour les transferts
    montant REAL NOT NULL,
    frais REAL NOT NULL,
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id),
    FOREIGN KEY (id_client_source) REFERENCES client(id),
    FOREIGN KEY (id_client_dest) REFERENCES client(id)
);

-- Données initiales
INSERT INTO prefixe (code) VALUES ('033'), ('037'),('034'),('038'),('032');

INSERT INTO type_operation (nom) VALUES ('depot'), ('retrait'), ('transfert');

-- Exemple de barème : Retrait
INSERT INTO bareme_frais (id_type_operation, montant_min, montant_max, frais) VALUES
(2, 1000, 10000, 200),
(2, 10001, 50000, 500),
(2, 50001, 100000, 1000);

-- Exemple de barème : Transfert
INSERT INTO bareme_frais (id_type_operation, montant_min, montant_max, frais) VALUES
(3, 1000, 10000, 100),
(3, 10001, 50000, 300),
(3, 50001, 100000, 800);