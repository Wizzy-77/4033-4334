<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle Livraison</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/edit.css">
</head>
<body>
<div class="page-wrapper">
    <nav class="breadcrumb">
        <a href="/livraisons" class="back-link">⬅ Retour au tableau de distribution</a>
    </nav>

    <div class="edit-layout">
        <aside class="preview-panel">
            <div class="preview-info">
                <h2 class="preview-name" style="font-size: 18px; margin-bottom: 15px;">👍 Recommandations livreurs</h2>
                <p class="preview-poste" style="margin-bottom: 20px;">Livreurs actuellement disponibles et prêts à partir :</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px; width: 10px; min-width: 100%;">
                <?php foreach($livreurs_dispo as $ld): ?>
                    <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; border-left: 4px solid #48bb78;">
                        <strong style="color: #fff; font-size:14px;"><?= $ld['nom'] ?></strong>
                        <p style="font-size:12px; color:#a0aec0; margin-top:2px;">🚗 <?= $ld['vehicule'] ?> | 📱 <?= $ld['telephone'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>

        <div class="form-area">
            <div class="form-header">
                <h1>Formulaire de livraison</h1>
                <p class="form-subtitle">Planifier une course client</p>
            </div>

            <form method="post" action="<?= base_url('livraisons/store') ?>">
                <fieldset class="form-section">
                    <legend class="section-legend">Informations de la course</legend>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vente_id">ID Numéro de Vente *</label>
                            <input type="number" id="vente_id" name="vente_id" required placeholder="ex: 4502">
                        </div>
                        <div class="form-group">
                            <label for="livreur_id">Assigner un Livreur *</label>
                            <select id="livreur_id" name="livreur_id" required>
                                <option value="">— Sélectionner —</option>
                                <?php foreach($livreurs_dispo as $ld): ?>
                                    <option value="<?= $ld['id'] ?>"><?= $ld['nom'] ?> (<?= $ld['vehicule'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="date_prevue">Date et Heure prévue *</label>
                        <input type="datetime-local" id="date_prevue" name="date_prevue" required>
                    </div>

                    <div class="form-group">
                        <label for="adresse_livraison">Adresse de destination précise *</label>
                        <textarea id="adresse_livraison" name="adresse_livraison" rows="3" required placeholder="Lot, Rue, Enceinte, Ville..."></textarea>
                    </div>
                </fieldset>

                <div class="form-actions">
                    <a href="/livraisons" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-save">Planifier la livraison</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>