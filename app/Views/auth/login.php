<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client - YAS Mobile Money</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fichier CSS personnalisé YAS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <div class="welcome-card text-center" style="max-width: 450px;">
        <!-- Logo / Badge Brand -->
        <div class="mb-3">
            <span class="yas-badge text-uppercase"><i class="fa-solid fa-user me-1"></i> Espace Client</span>
        </div>

        <h2 class="yas-brand h3 mb-4">Connexion</h2>

        <!-- Notification d'erreur -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show text-start small mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('login') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-4 text-start">
                <label for="telephone" class="form-label fw-bold text-secondary small">Numéro de téléphone YAS :</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-phone"></i>
                    </span>
                    <input type="text" 
                           name="telephone" 
                           id="telephone" 
                           class="form-control border-start-0 ps-0" 
                           placeholder="" 
                           maxlength="10" 
                           required>
                </div>
                <div class="form-text text-muted small mt-1">
                    
                </div>
            </div>

            <button type="submit" class="btn-yas-client w-100 justify-content-center shadow-sm">
                Se connecter <i class="fa-solid fa-right-to-bracket ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <a href="<?= site_url('/') ?>" class="text-decoration-none text-secondary small fw-medium">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour à l'accueil
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.bundle.min.js"></script>
</body>
</html>