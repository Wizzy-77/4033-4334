<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nouvelle transformation — Miel Arovia</title>
  <link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="assets/css/global.css"/>
</head>
<body>
<?php include 'utils/header.php'; ?>
<?php include 'utils/side_bar.php'; ?>
<main class="main-wrapper">
  <div class="breadcrumb-bar"><a href="/transformations">Gestion de stock</a> <span>›</span> Nouvelle transformation</div>
  <div class="page-header">
    <h1 class="page-title">Nouvelle transformation</h1>
    <a href="/transformations" class="btn-outline-gold"><i class="fa fa-arrow-left"></i> Retour</a>
  </div>

  <div class="content-card" style="max-width: 760px;">
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="mb-3">
      <span class="badge-arovia badge-green">Stock disponible : <?= number_format($stockMP['quantite_litres'] ?? 0, 2) ?> L</span>
    </div>

    <form action="/transformations" method="post">
      <?= csrf_field() ?>
      <?php foreach ($typesBocaux as $type): ?>
        <div class="mb-3">
          <label class="arovia-label" for="quantite_<?= (int) ($type['id'] ?? 0) ?>">
            <?= esc($type['nom'] ?? 'Bocal') ?> (<?= (float) ($type['volume_litres'] ?? 0) ?> L/bocal — <?= esc($type['cible'] ?? '') ?>)
          </label>
          <input id="quantite_<?= (int) ($type['id'] ?? 0) ?>" type="number" min="0" class="arovia-input qte-bocal" name="quantite_<?= (int) ($type['id'] ?? 0) ?>" data-volume="<?= (float) ($type['volume_litres'] ?? 0) ?>" value="0"/>
        </div>
      <?php endforeach; ?>

      <div class="mb-3">
        <strong>Volume total nécessaire : </strong><span id="volume-necessaire">0.00</span> L
      </div>
      <div id="volume-restant-msg" class="text-muted"></div>

      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn-gold">Valider la transformation</button>
      </div>
    </form>
  </div>
</main>
<script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
<script>
const stockDisponible = <?= (float) ($stockMP['quantite_litres'] ?? 0) ?>;
const champs = document.querySelectorAll('.qte-bocal');
const volumeNecessaireEl = document.getElementById('volume-necessaire');
const messageEl = document.getElementById('volume-restant-msg');

function recalculer() {
  let total = 0;
  champs.forEach(champ => {
    const quantite = parseFloat(champ.value) || 0;
    const volume = parseFloat(champ.dataset.volume) || 0;
    total += quantite * volume;
  });

  volumeNecessaireEl.textContent = total.toFixed(2);

  const restant = stockDisponible - total;
  if (restant < 0) {
    messageEl.textContent = 'Stock insuffisant ! Il manque ' + Math.abs(restant).toFixed(2) + ' L.';
    messageEl.className = 'text-danger';
  } else {
    messageEl.textContent = 'Il restera ' + restant.toFixed(2) + ' L après cette transformation.';
    messageEl.className = 'text-success';
  }
}

champs.forEach(champ => champ.addEventListener('input', recalculer));
</script>
</body>
</html>