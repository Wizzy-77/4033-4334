<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Client - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5" style="max-width: 450px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Espace Client</h3>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('login') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3 text-start">
                        <label for="telephone" class="form-label">Numéro de téléphone :</label>
                        <input type="text" 
                               name="telephone" 
                               id="telephone" 
                               class="form-control" 
                               placeholder="" 
                               maxlength="10" 
                               required>
                        
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>

                <div class="text-center mt-3">
                    <a href="<?= site_url('/') ?>" class="text-decoration-none">← Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>