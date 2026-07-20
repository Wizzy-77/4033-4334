<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration Opérateur - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Espace Administrateur / Opérateur</span>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm">Déconnexion</a>
        </div>
    </nav>

    <div class="container">
        <!-- Messages de notification -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <!-- ==================== NOUVELLE SECTION STATISTIQUES & CA ==================== -->
        <h4 class="mb-3 text-secondary">Tableau de bord financier</h4>
        <div class="row mb-4">
            <!-- Clients Locaux -->
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase">Clients Opérateur Local</h6>
                        <h2 class="card-text fw-bold mb-0"><?= $nbClientsLocaux ?> <small class="fs-6 fw-normal">/ <?= $totalClients ?> clients</small></h2>
                    </div>
                </div>
            </div>

            <!-- Gains Mon Opérateur -->
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase">Mon Opérateur (Gains)</h6>
                        <h2 class="card-text fw-bold mb-0"><?= number_format($caMonOperateur, 2, ',', ' ') ?> Ar</h2>
                        <small class="opacity-75">Frais perçus sur transactions</small>
                    </div>
                </div>
            </div>

            <!-- Commissions Autres Opérateurs -->
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-warning shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase text-dark">Autres Opérateurs</h6>
                        <h2 class="card-text fw-bold text-dark mb-0"><?= number_format($caAutresOperateurs, 2, ',', ' ') ?> Ar</h2>
                        <small class="text-dark opacity-75">Commissions de 10% inter-opérateurs</small>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================================ -->

        <div class="row">
            <!-- GESTION DES PRÉFIXES -->
            <div class="col-md-5 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Préfixes autorisés (Opérateur Local)</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('operator/prefixe/add') ?>" method="post" class="d-flex gap-2 mb-3">
                            <?= csrf_field() ?>
                            <input type="text" name="code" class="form-control" placeholder="Ex: 034" maxlength="3" required>
                            <button type="submit" class="btn btn-success">Ajouter</button>
                        </form>

                        <ul class="list-group">
                            <?php foreach ($prefixes as $p): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><?= esc($p['code']) ?></strong>
                                    <a href="<?= site_url('operator/prefixe/delete/' . $p['id']) ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Supprimer ce préfixe ?');">Supprimer</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- GESTION DU BARÈME DES FRAIS -->
            <div class="col-md-7 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">Barème des Frais</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('operator/bareme/save') ?>" method="post" class="row g-2 mb-4">
                            <?= csrf_field() ?>
                            <div class="col-md-4">
                                <select name="id_type_operation" class="form-select" required>
                                    <option value="">Opération...</option>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['id'] ?>"><?= ucfirst($t['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="montant_min" class="form-control" placeholder="Min" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="montant_max" class="form-control" placeholder="Max" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" name="frais" class="form-control" placeholder="Frais" required>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Ajouter / Modifier la tranche</button>
                            </div>
                        </form>

                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Type</th>
                                    <th>Tranche (Ar)</th>
                                    <th>Frais (Ar)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($baremes as $b): ?>
                                    <tr>
                                        <td><span class="badge bg-info text-dark"><?= esc($b['type_nom']) ?></span></td>
                                        <td><?= number_format($b['montant_min'], 0, ',', ' ') ?> - <?= number_format($b['montant_max'], 0, ',', ' ') ?></td>
                                        <td><strong><?= number_format($b['frais'], 0, ',', ' ') ?></strong></td>
                                        <td>
                                            <a href="<?= site_url('operator/bareme/delete/' . $b['id']) ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Supprimer cette tranche ?');">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIQUE DES TRANSACTIONS -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Historique général des transactions</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Destinataire</th>
                            <th>Montant</th>
                            <th>Frais</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td>#<?= $tx['id'] ?></td>
                                <td><?= $tx['date_transaction'] ?></td>
                                <td><?= strtoupper($tx['type_nom']) ?></td>
                                <td><?= $tx['client_source'] ?></td>
                                <td><?= $tx['client_dest'] ?? '-' ?></td>
                                <td><?= number_format($tx['montant'], 2, ',', ' ') ?> Ar</td>
                                <td><?= number_format($tx['frais'], 2, ',', ' ') ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>