<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur YAS Mobile Money</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fichier CSS personnalisé YAS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <div class="welcome-card text-center">
        <!-- Logo / Badge Brand -->
        <div class="mb-3">
            <span class="yas-badge fs-6 text-uppercase">Mobile Money</span>
        </div>

        <h1 class="yas-brand display-6 mb-2">
            Bienvenue sur <span style="color: #d89f00;">YAS</span>
        </h1>
        
        <p class="text-muted mb-4 fs-6">
            Veuillez choisir votre mode de connexion pour continuer :
        </p>
        
        <!-- Boutons de choix -->
        <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
            <a href="<?= site_url('login/client') ?>" class="btn-yas-client shadow-sm w-100">
                <i class="fa-solid fa-user"></i> Client
            </a>
            <a href="<?= site_url('login/admin') ?>" class="btn-yas-operator shadow-sm w-100">
                <i class="fa-solid fa-user-gear"></i> Admin
            </a>
        </div>
    </div>

</body>
</html>s