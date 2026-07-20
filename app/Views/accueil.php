<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container text-center mt-5">
        <h2 class="mb-3">Bienvenue sur Mobile Money</h2>
        <p class="text-muted">Veuillez choisir votre mode de connexion :</p>
        
        <div class="d-flex justify-content-center gap-3 mt-4">
            <!-- Option Client -->
            <a href="<?= site_url('login/client') ?>" class="btn btn-primary btn-lg px-4">
                Espace Client
            </a>
            
            <!-- Option Admin / Opérateur -->
            <a href="<?= site_url('login/admin') ?>" class="btn btn-dark btn-lg px-4">
                Espace Opérateur
            </a>
        </div>
    </div>

</body>
</html>