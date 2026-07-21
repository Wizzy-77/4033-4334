<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client - YAS Mobile Money</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fichier CSS personnalisé YAS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <!-- NAVBAR YAS -->
    <nav class="navbar navbar-dark bg-yas-navy navbar-yas mb-4 shadow-sm">
        <div class="container">
            <span class="navbar-brand fw-bold d-flex align-items-center">
                <span class="yas-badge me-2 fs-6">YAS</span> 
                Client : <strong class="ms-1 text-yas-yellow"><?= esc($client['telephone']) ?></strong>
            </span>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm rounded-pill">
                <i class="fa-solid fa-power-off me-1"></i> Déconnexion
            </a>
        </div>
    </nav>

    <div class="container pb-5">
        
        <!-- MESSAGES FLASH (Succès / Erreur) -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- CARTE SOLDE ACTUEL -->
            <div class="col-lg-4 mb-4">
                <div class="card card-yas h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                        <div class="text-muted small text-uppercase fw-bold mb-2">Solde Actuel</div>
                        <h2 class="display-6 fw-bold text-yas-navy m-0">
                            <?= number_format($client['solde'], 2, ',', ' ') ?> <span class="fs-5 text-warning">Ar</span>
                        </h2>
                    </div>
                </div>
            </div>




            <!-- BLOC EPARGNE  AUTOMATIQUE -->
            <div class="col-lg-4 mb-4">
                <div class="card card-yas h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                        <div class="text-muted small text-uppercase fw-bold mb-2">Solde Actuel</div>
                        <h2 class="display-6 fw-bold text-yas-navy m-0">
                            <?= number_format($client['solde'], 2, ',', ' ') ?> <span class="fs-5 text-warning">Ar</span>
                        </h2>
                    </div>
                </div>
            </div>

            <!-- FORMULAIRE TRANSACTION -->
            <div class="col-lg-8 mb-4">
                <div class="card card-yas shadow-sm">
                    <div class="card-header card-yas-header py-3">
                        <h5 class="card-title m-0 fw-bold fs-6 text-uppercase">
                            <i class="fa-solid fa-money-bill-transfer me-2"></i>Effectuer une Opération
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?= site_url('client/transaction') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-yas-navy">Type d'opération :</label>
                                <select name="id_type_operation" id="type_op" class="form-select" required onchange="toggleTransfertOptions()">
                                    <option value="">-- Choisir une opération --</option>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['id'] ?>" data-nom="<?= strtolower($t['nom']) ?>">
                                            <?= ucfirst($t['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- CHAMP DESTINATAIRE -->
                            <div class="mb-3" id="field_destinataire" style="display: none;">
                                <label class="form-label fw-semibold text-yas-navy">Numéro(s) du/des destinataire(s) :</label>
                                <input type="text" name="destinataire" id="destinataire_input" class="form-control" placeholder="Ex: 0341234567, 0389876543">
                                <div class="form-text small mt-1">
                                    <i class="fa-solid fa-circle-info text-warning me-1"></i>
                                    <strong>Note : L'envoi multiple est réservé uniquement aux numéros YAS (034, 038).</strong>
                                </div>
                            </div>

                            <!-- OPTION FRAIS DE RETRAIT -->
                            <div class="mb-3 form-check" id="field_frais_retrait" style="display: none;">
                                <input class="form-check-input" type="checkbox" name="inclure_frais_retrait" value="1" id="inclure_frais_retrait">
                                <label class="form-check-label fw-semibold text-yas-navy" for="inclure_frais_retrait">
                                    Inclure les frais de retrait pour le destinataire
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-yas-navy">Montant (Ar) :</label>
                                <input type="number" step="0.01" name="montant" class="form-control" placeholder="Entrez le montant" required>
                            </div>

                            <button type="submit" class="btn btn-yas-primary w-100 py-2.5 shadow-sm">
                                Valider l'opération <i class="fa-solid fa-check ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIQUE DES TRANSACTIONS -->
        <div class="card card-yas shadow-sm mb-5">
            <div class="card-header card-yas-header py-3">
                <h5 class="card-title m-0 fw-bold fs-6 text-uppercase">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Mon Historique
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="py-3">Date</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">Expéditeur</th>
                                <th class="py-3">Destinataire</th>
                                <th class="py-3">Montant</th>
                                <th class="py-3">Frais</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transactions)): ?>
                                <?php foreach ($transactions as $tx): ?>
                                    <tr>
                                        <td class="small text-muted"><?= $tx['date_transaction'] ?></td>
                                        <td>
                                            <span class="badge badge-yas">
                                                <?= esc($tx['type_nom']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-yas-navy"><?= $tx['source_tel'] ?></td>
                                        <td class="fw-semibold text-yas-navy"><?= $tx['dest_tel'] ?? '-' ?></td>
                                        <td class="fw-bold text-success"><?= number_format($tx['montant'], 2, ',', ' ') ?> Ar</td>
                                        <td class="small text-danger"><?= number_format($tx['frais'], 2, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-muted py-4">
                                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                        Aucune transaction enregistrée.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>






    <!-- SCRIPT DE GESTION DES CHAMPS DYNAMIQUES -->
    <script>
        function toggleTransfertOptions() {
            var select = document.getElementById('type_op');
            var selectedOption = select.options[select.selectedIndex];
            var nom = (selectedOption.getAttribute('data-nom') || '').toLowerCase();
            
            var destField = document.getElementById('field_destinataire');
            var retraitField = document.getElementById('field_frais_retrait');
            var destInput = document.getElementById('destinataire_input');

            // On vérifie si l'opération est un transfert ou un envoi
            if (nom.includes('transfert') || nom.includes('envoi')) {
                destField.style.display = 'block';
                retraitField.style.display = 'block';
                destInput.setAttribute('required', 'required');
            } else {
                destField.style.display = 'none';
                retraitField.style.display = 'none';
                destInput.removeAttribute('required');
                destInput.value = '';
                document.getElementById('inclure_frais_retrait').checked = false;
            }
        }
    </script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>