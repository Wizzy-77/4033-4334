<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Client - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">
                Espace Client (N° <strong><?= esc($client['telephone']) ?></strong>)
            </span>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm">Déconnexion</a>
        </div>
    </nav>

    <div class="container">
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- SOLDE ACTUEL -->
            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">Solde Actuel</h5>
                        <h2 class="display-6 fw-bold"><?= number_format($client['solde'], 2, ',', ' ') ?> Ar</h2>
                    </div>
                </div>
            </div>

            <!-- FORMULAIRE TRANSACTION -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">Effectuer une Opération</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('client/transaction') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label">Type d'opération :</label>
                                <select name="id_type_operation" id="type_op" class="form-select" required onchange="toggleDestinataire()">
                                    <option value="">-- Choisir une opération --</option>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['id'] ?>" data-nom="<?= strtolower($t['nom']) ?>">
                                            <?= ucfirst($t['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3" id="field_destinataire" style="display: none;">
                                <label class="form-label">Numéro du destinataire :</label>
                                <input type="text" name="destinataire" class="form-control" placeholder="Ex: 0341234567" maxlength="10">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Montant (Ar) :</label>
                                <input type="number" step="0.01" name="montant" class="form-control" placeholder="Entrez le montant" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Valider l'opération</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIQUE DES TRANSACTIONS -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">Mon Historique</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Expéditeur</th>
                            <th>Destinataire</th>
                            <th>Montant</th>
                            <th>Frais</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td><?= $tx['date_transaction'] ?></td>
                                    <td><span class="badge bg-info text-dark"><?= esc($tx['type_nom']) ?></span></td>
                                    <td><?= $tx['source_tel'] ?></td>
                                    <td><?= $tx['dest_tel'] ?? '-' ?></td>
                                    <td><?= number_format($tx['montant'], 2, ',', ' ') ?> Ar</td>
                                    <td><?= number_format($tx['frais'], 2, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-muted">Aucune transaction enregistrée.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function toggleDestinataire() {
            var select = document.getElementById('type_op');
            var selectedOption = select.options[select.selectedIndex];
            var nom = selectedOption.getAttribute('data-nom') || '';
            var destField = document.getElementById('field_destinataire');

            if (nom.includes('transfert')) {
                destField.style.display = 'block';
            } else {
                destField.style.display = 'none';
            }
        }
    </script>
</body>
</html>