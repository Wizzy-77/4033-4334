<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de l'équipe des livreurs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/liste.css">
</head>
<body>
<div class="page-wrapper">
    <header class="page-header">
        <div class="header-left">
            <h1>Équipe Livrants</h1>
            <span class="employee-count"><?= count($livreurs) ?> livreurs enregistrés</span>
        </div>
    </header>

    <div class="edit-layout" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 20px;">
        <div class="employee-card" style="padding: 25px; height: fit-content;">
            <h2 style="font-family:'Sora'; font-size: 18px; margin-bottom: 20px; color:#fff;">➕ Ajouter un livreur</h2>
            <form method="post" action="<?= base_url('livreurs/store') ?>">
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; color:#a0aec0; font-size:13px;">Nom complet</label>
                    <input type="text" name="nom" required style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; color:#a0aec0; font-size:13px;">Téléphone</label>
                    <input type="text" name="telephone" required style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; color:#a0aec0; font-size:13px;">Véhicule / Moyen</label>
                    <input type="text" name="vehicule" placeholder="ex: Moto Scooter, Camionnette" required style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff;">
                </div>
                <button type="submit" class="btn-add" style="width:100%; justify-content:center;">Enregistrer le profil</button>
            </form>
        </div>

        <div class="cards-grid" style="grid-template-columns: repeat(2, 1fr); gap: 15px;">
            <?php foreach($livreurs as $liv): ?>
                <div class="employee-card">
                    <div class="card-top">
                        <span class="emp-matricule">ID: #<?= (int) ($liv['id'] ?? 0) ?></span>
                        <span class="status-badge <?= !empty($liv['disponible']) ? 'actif' : 'inactif' ?>">
                            <?= !empty($liv['disponible']) ? 'DISPONIBLE' : 'EN COURSE/ABSENT' ?>
                        </span>
                    </div>
                    <div class="card-body" style="margin-top:15px;">
                        <h2 class="emp-name" style="font-size:18px;"><?= esc($liv['nom'] ?? '—') ?></h2>
                        <p class="emp-poste" style="color:#63b3ed;">🛵 Véhicule : <?= esc($liv['vehicule'] ?? '—') ?></p>
                        <p style="font-size: 13px; margin-top:5px; color:#cbd5e0;">📞 <?= esc($liv['telephone'] ?? '—') ?></p>
                    </div>
                    <div class="card-actions" style="margin-top: 15px;">
                        <a href="/livreurs/edit/<?= (int) ($liv['id'] ?? 0) ?>" class="btn-action btn-edit" style="width: 100%; text-align:center;">Modifier les infos</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>