<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Opérateur - YAS Mobile Money</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fichier CSS personnalisé YAS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <style>
        /* Conteneur défilant pour fixer la hauteur du tableau des barèmes */
        .table-scrollable {
            max-height: 280px;
            overflow-y: auto;
        }
        .table-scrollable thead th {
            position: sticky;
            top: 0;
            background-color: #ffffff;
            z-index: 2;
        }
    </style>
</head>
<body class="bg-light">

    <!-- NAVBAR YAS OPÉRATEUR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-yas mb-3 py-2">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-6 d-flex align-items-center mb-0">
                <span class="badge-yas me-2">YAS</span> 
                Espace Administration & Interopérabilité
            </span>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="fa-solid fa-power-off me-1"></i> Déconnexion
            </a>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-4">

        <!-- MESSAGES FLASH -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 mb-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 mb-3" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ==================== TABLEAU DE BORD FINANCIER ==================== -->
<h5 class="mb-3 fw-bold text-yas-navy">
    <i class="fa-solid fa-chart-pie me-2 text-warning"></i>Bilan Financier & Interopérabilité
</h5>

<div class="row g-3 mb-4">
    <!-- 1. PARC CLIENTS YAS -->
    <div class="col-md-3">
        <div class="card card-solde p-3 text-center h-100 d-flex flex-column justify-content-between shadow-sm">
            <div class="solde-label mb-1 small text-uppercase fw-semibold"><i class="fa-solid fa-users me-1"></i> Clients Réseau Local</div>
            <div class="solde-amount fs-3 my-2 fw-bold">
                <?= $nbClientsLocaux ?> <small class="fs-6 text-warning">/ <?= $totalClients ?></small>
            </div>
            <small class="text-white-50" style="font-size: 0.75rem;">Abonnés actifs YAS</small>
        </div>
    </div>

    <!-- 2. GAINS YAS -->
    <div class="col-md-3">
        <div class="card card-yas h-100 p-3 shadow-sm border-start border-4 border-success overflow-hidden">
            <div class="text-muted small text-uppercase fw-bold mb-1 d-flex align-items-center">
                <i class="fa-solid fa-building-columns me-1.5 text-success"></i> Mon Opérateur (YAS)
            </div>
            <div class="fs-3 fw-bold text-success my-2 lh-1">
                <?= number_format($caMonOperateur, 2, ',', ' ') ?> <span class="fs-6 text-dark fw-normal">Ar</span>
            </div>
            <small class="text-muted d-block mt-auto" style="font-size: 0.75rem;">Part conservée (Traitement)</small>
        </div>
    </div>

    <!-- 3. COMMISSIONS D'INTEROPÉRABILITÉ -->
    <div class="col-md-3">
        <div class="card card-yas h-100 p-3 shadow-sm border-start border-4 border-warning overflow-hidden">
            <div class="text-muted small text-uppercase fw-bold mb-1 d-flex align-items-center">
                <i class="fa-solid fa-handshake me-1.5 text-warning"></i> Autres Opérateurs
            </div>
            <div class="fs-3 fw-bold text-yas-navy my-2 lh-1">
                <?= number_format($caAutresOperateurs, 2, ',', ' ') ?> <span class="fs-6 text-warning fw-normal">Ar</span>
            </div>
            <small class="text-muted d-block mt-auto" style="font-size: 0.75rem;">Commissions inter-réseaux</small>
        </div>
    </div>

    <!-- 4. TOTAL DES FRAIS COLLECTÉS -->
    <div class="col-md-3">
        <div class="card card-yas bg-yas-navy text-white h-100 p-3 shadow-sm overflow-hidden">
            <div class="solde-label mb-1 small text-warning text-uppercase fw-bold d-flex align-items-center">
                <i class="fa-solid fa-vault me-1.5"></i> Grand Total Collecté
            </div>
            <div class="fs-3 fw-bold text-white my-2 lh-1">
                <?= number_format($caMonOperateur + $caAutresOperateurs, 2, ',', ' ') ?> <span class="fs-6 text-warning fw-normal">Ar</span>
            </div>
            <small class="text-white-50 d-block mt-auto" style="font-size: 0.75rem;">Frais totaux prélevés</small>
        </div>
    </div>
</div>
        <!-- ==================== CONFIGURATION & PARAMÈTRES ==================== -->
        <div class="row g-3 mb-4">
            <!-- GESTION DES PRÉFIXES -->
            <div class="col-lg-4">
                <div class="card card-yas shadow-sm h-100">
                    <div class="card-header card-yas-header py-2 px-3 fw-bold">
                        <i class="fa-solid fa-sim-card me-2 text-warning"></i>Préfixes autorisés
                    </div>
                    <div class="card-body p-3">
                        <form action="<?= site_url('operator/prefixe/add') ?>" method="post" class="d-flex gap-2 mb-3">
                            <?= csrf_field() ?>
                            <input type="text" name="code" class="form-control form-control-sm" placeholder="Ex: 034, 038" maxlength="3" required>
                            <button type="submit" class="btn btn-yas-primary btn-sm text-nowrap">
                                <i class="fa-solid fa-plus me-1"></i> Ajouter
                            </button>
                        </form>

                        <div class="table-scrollable" style="max-height: 230px;">
                            <ul class="list-group list-group-flush border rounded-3">
                                <?php foreach ($prefixes as $p): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-1.5 px-3">
                                        <span class="fw-bold text-yas-navy small"><i class="fa-solid fa-phone me-2 text-muted"></i><?= esc($p['code']) ?></span>
                                        <a href="<?= site_url('operator/prefixe/delete/' . $p['id']) ?>" 
                                           class="btn btn-outline-danger btn-sm py-0 px-2 rounded-pill" style="font-size: 0.75rem;"
                                           onclick="return confirm('Supprimer ce préfixe ?');">
                                            <i class="fa-solid fa-trash me-1"></i> Supprimer
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GESTION DU BARÈME DES FRAIS (TABLEAU SCROLLABLE + MODALE) -->
            <div class="col-lg-8">
                <div class="card card-yas shadow-sm h-100">
                    <div class="card-header card-yas-header d-flex justify-content-between align-items-center py-2 px-3 fw-bold">
                        <span><i class="fa-solid fa-sliders me-2 text-warning"></i>Barème des Frais de Base</span>
                        
                        <!-- Bouton ouvrant la modale -->
                        <button type="button" class="btn btn-yas-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBareme">
                            <i class="fa-solid fa-plus me-1"></i> Ajouter / Modifier
                        </button>
                    </div>

                    <div class="card-body p-2">
                        <!-- Zone de tableau compacte avec Scrollbar interne -->
                        <div class="table-scrollable">
                            <table class="table table-hover table-sm table-yas align-middle text-center mb-0" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th class="py-2">TYPE</th>
                                        <th class="py-2">TRANCHE (AR)</th>
                                        <th class="py-2">FRAIS (AR)</th>
                                        <th class="py-2">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($baremes as $b): ?>
                                        <tr>
                                            <td class="py-1"><span class="badge badge-yas"><?= esc($b['type_nom']) ?></span></td>
                                            <td class="fw-semibold py-1"><?= number_format($b['montant_min'], 0, ',', ' ') ?> - <?= number_format($b['montant_max'], 0, ',', ' ') ?></td>
                                            <td class="fw-bold text-success py-1"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                                            <td class="py-1">
                                                <a href="<?= site_url('operator/bareme/delete/' . $b['id']) ?>" 
                                                   class="btn btn-outline-danger btn-sm py-0 px-2 rounded-circle"
                                                   onclick="return confirm('Supprimer cette tranche ?');">
                                                    <i class="fa-solid fa-trash" style="font-size: 0.75rem;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== HISTORIQUE GÉNÉRAL DES TRANSACTIONS ==================== -->
        <div class="card card-yas shadow-sm">
            <div class="card-header card-yas-header py-2 px-3 fw-bold">
                <i class="fa-solid fa-list-check me-2 text-warning"></i>Historique des flux & transactions interopérables
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover table-sm table-yas align-middle text-center mb-0" style="font-size: 0.85rem;">
                        <thead style="position: sticky; top: 0; background-color: #fff; z-index: 2;">
                            <tr>
                                <th class="py-2">#ID</th>
                                <th class="py-2">Date</th>
                                <th class="py-2">Type</th>
                                <th class="py-2">Expéditeur (Source)</th>
                                <th class="py-2">Bénéficiaire (Destinataire)</th>
                                <th class="py-2">Montant Envoyé</th>
                                <th class="py-2">Frais Totaux</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td class="fw-bold text-muted py-1">#<?= $tx['id'] ?></td>
                                    <td class="small text-secondary py-1"><?= $tx['date_transaction'] ?></td>
                                    <td class="py-1"><span class="badge badge-yas"><?= strtoupper($tx['type_nom']) ?></span></td>
                                    <td class="fw-bold text-yas-navy py-1"><?= $tx['client_source'] ?></td>
                                    <td class="fw-bold text-yas-navy py-1"><?= $tx['client_dest_tel'] ?? $tx['telephone_dest'] ?? '-' ?></td>
                                    <td class="fw-bold text-success py-1"><?= number_format($tx['montant'], 2, ',', ' ') ?> Ar</td>
                                    <td class="fw-bold text-danger py-1"><?= number_format($tx['frais'], 2, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ==================== MODALE D'AJOUT / MODIFICATION BARÈME ==================== -->
    <div class="modal fade" id="modalBareme" tabindex="-1" aria-labelledby="modalBaremeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-yas-navy text-white py-2.5">
                    <h6 class="modal-title fw-bold text-warning" id="modalBaremeLabel">
                        <i class="fa-solid fa-sliders me-2"></i>Nouveau Barème de Frais
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= site_url('operator/bareme/save') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-yas-navy">Type d'opération</label>
                            <select name="id_type_operation" class="form-select" required>
                                <option value="">Choisir une opération...</option>
                                <?php foreach ($types as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= ucfirst($t['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-yas-navy">Montant Min (Ar)</label>
                                <input type="number" step="0.01" name="montant_min" class="form-control" placeholder="1000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-yas-navy">Montant Max (Ar)</label>
                                <input type="number" step="0.01" name="montant_max" class="form-control" placeholder="10000" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-yas-navy">Frais à appliquer (Ar)</label>
                            <input type="number" step="0.01" name="frais" class="form-control" placeholder="200" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-yas-primary btn-sm rounded-pill px-3">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>