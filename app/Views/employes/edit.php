<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier — <?= $employe['prenom'] ?> <?= $employe['nom'] ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/edit.css">
</head>
<body>

<div class="page-wrapper">

    <!-- NAV -->
    <nav class="breadcrumb">
        <a href="/employes/show/<?= $employe['id'] ?>" class="back-link">
            <svg viewBox="0 0 20 20" fill="none">
                <path d="M12 15l-5-5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Retour au profil
        </a>
    </nav>

    <div class="edit-layout">

        <!-- ══ PANNEAU GAUCHE : aperçu ══ -->
        <aside class="preview-panel">
            <div class="preview-avatar-wrap">
                <div class="preview-avatar" id="previewAvatar">
                    <?php if (!empty($employe['photo'])): ?>
                        <img src="<?= $employe['photo'] ?>" id="previewImg" alt="Photo" class="preview-img">
                    <?php else: ?>
                        <div class="preview-initials" id="previewInitials">
                            <?= strtoupper(substr($employe['prenom'],0,1).substr($employe['nom'],0,1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <label for="photo" class="avatar-change-btn" title="Changer la photo">
                    <svg viewBox="0 0 20 20" fill="none">
                        <path d="M3 15.5V17h1.5l8.8-8.8-1.5-1.5L3 15.5zM16.7 5.3a1 1 0 000-1.4l-1.6-1.6a1 1 0 00-1.4 0l-1.3 1.3 3 3 1.3-1.3z" fill="currentColor"/>
                    </svg>
                </label>
            </div>

            <div class="preview-info">
                <p class="preview-matricule" id="previewMatricule"><?= $employe['matricule'] ?></p>
                <h2 class="preview-name" id="previewName"><?= $employe['prenom'] ?> <?= $employe['nom'] ?></h2>
                <p class="preview-poste" id="previewPoste"><?= $employe['poste'] ?></p>
                <span class="preview-status <?= strtolower($employe['statut']) ?>" id="previewStatus">
                    <?= $employe['statut'] ?>
                </span>
            </div>

            <div class="progress-wrap">
                <div class="progress-header">
                    <span>Complétion du profil</span>
                    <span class="progress-pct" id="progressPct">0%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>

            <div class="preview-meta">
                <div class="meta-row">
                    <svg viewBox="0 0 20 20" fill="none">
                        <rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                        <path d="M7 2v4M13 2v4M3 9h14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    <span>Employé depuis <?= !empty($employe['date_embauche']) ? date('Y', strtotime($employe['date_embauche'])) : '—' ?></span>
                </div>
                <div class="meta-row">
                    <svg viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.4"/>
                        <path d="M10 6v4l2.5 2.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    <span id="previewSalaire"><?= number_format($employe['salaire_base'], 0, ',', ' ') ?> Ar / mois</span>
                </div>
            </div>
        </aside>

        <!-- ══ FORMULAIRE ══ -->
        <div class="form-area">
            <div class="form-header">
                <h1>Modifier le profil</h1>
                <p class="form-subtitle">Les champs marqués <span class="required-star">*</span> sont obligatoires</p>
            </div>

            <form method="post"
                  action="<?= base_url('employes/update/'.$employe['id']) ?>"
                  enctype="multipart/form-data"
                  id="editForm"
                  novalidate>

                <!-- ── Photo (champ caché lié au bouton du panneau) ── -->
                <input type="file" id="photo" name="photo" accept="image/*" class="input-file-hidden">

                <!-- ── SECTION : Identité ── -->
                <fieldset class="form-section">
                    <legend class="section-legend">
                        <svg viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="7" r="3.5" stroke="currentColor" stroke-width="1.4"/>
                            <path d="M3 17c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        </svg>
                        Identité
                    </legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom">Prénom <span class="required-star">*</span></label>
                            <input type="text" id="prenom" name="prenom"
                                   value="<?= htmlspecialchars($employe['prenom']) ?>"
                                   required autocomplete="given-name">
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom <span class="required-star">*</span></label>
                            <input type="text" id="nom" name="nom"
                                   value="<?= htmlspecialchars($employe['nom']) ?>"
                                   required autocomplete="family-name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance"
                                   value="<?= $employe['date_naissance'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre</label>
                            <select id="genre" name="genre">
                                <option value="">— Sélectionner —</option>
                                <option value="M" <?= ($employe['genre'] ?? '') === 'M' ? 'selected' : '' ?>>Masculin</option>
                                <option value="F" <?= ($employe['genre'] ?? '') === 'F' ? 'selected' : '' ?>>Féminin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cin">Numéro CIN</label>
                        <input type="text" id="cin" name="cin"
                               value="<?= htmlspecialchars($employe['cin'] ?? '') ?>"
                               placeholder="ex : 101 234 567 890">
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea id="adresse" name="adresse" rows="2"
                                  placeholder="Rue, ville, région..."><?= htmlspecialchars($employe['adresse'] ?? '') ?></textarea>
                    </div>
                </fieldset>

                <!-- ── SECTION : Coordonnées ── -->
                <fieldset class="form-section">
                    <legend class="section-legend">
                        <svg viewBox="0 0 20 20" fill="none">
                            <path d="M14.5 13.5l-2.5 2.5a11 11 0 01-8-8l2.5-2.5L5 2H2a1 1 0 00-1 1 15 15 0 0016 16 1 1 0 001-1v-3l-3.5-1.5z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                        </svg>
                        Coordonnées
                    </legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telephone">Téléphone <span class="required-star">*</span></label>
                            <input type="tel" id="telephone" name="telephone"
                                   value="<?= htmlspecialchars($employe['telephone']) ?>"
                                   required placeholder="ex : 034 00 000 00">
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="required-star">*</span></label>
                            <input type="email" id="email" name="email"
                                   value="<?= htmlspecialchars($employe['email']) ?>"
                                   required autocomplete="email">
                        </div>
                    </div>
                </fieldset>

                <!-- ── SECTION : Poste & contrat ── -->
                <fieldset class="form-section">
                    <legend class="section-legend">
                        <svg viewBox="0 0 20 20" fill="none">
                            <rect x="3" y="6" width="14" height="11" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                            <path d="M7 6V4.5A1.5 1.5 0 018.5 3h3A1.5 1.5 0 0113 4.5V6" stroke="currentColor" stroke-width="1.4"/>
                        </svg>
                        Poste & Contrat
                    </legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="matricule">Matricule</label>
                            <input type="text" id="matricule" name="matricule"
                                   value="<?= htmlspecialchars($employe['matricule']) ?>"
                                   readonly class="input-readonly">
                            <span class="field-hint">Non modifiable</span>
                        </div>
                        <div class="form-group">
                            <label for="poste">Poste <span class="required-star">*</span></label>
                            <input type="text" id="poste" name="poste"
                                   value="<?= htmlspecialchars($employe['poste']) ?>"
                                   required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="departement">Département / Service</label>
                            <input type="text" id="departement" name="departement"
                                   value="<?= htmlspecialchars($employe['departement'] ?? '') ?>"
                                   placeholder="ex : Ressources Humaines">
                        </div>
                        <div class="form-group">
                            <label for="type_contrat">Type de contrat</label>
                            <select id="type_contrat" name="type_contrat">
                                <option value="">— Sélectionner —</option>
                                <?php foreach (['CDI','CDD','Stage','Freelance','Intérim'] as $tc): ?>
                                <option value="<?= $tc ?>" <?= ($employe['type_contrat'] ?? '') === $tc ? 'selected' : '' ?>><?= $tc ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_embauche">Date d'embauche</label>
                            <input type="date" id="date_embauche" name="date_embauche"
                                   value="<?= $employe['date_embauche'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="statut">Statut <span class="required-star">*</span></label>
                            <select id="statut" name="statut" required>
                                <option value="ACTIF"  <?= $employe['statut'] === 'ACTIF'  ? 'selected' : '' ?>>Actif</option>
                                <option value="INACTIF"<?= $employe['statut'] === 'INACTIF'? 'selected' : '' ?>>Inactif</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <!-- ── SECTION : Rémunération ── -->
                <fieldset class="form-section">
                    <legend class="section-legend">
                        <svg viewBox="0 0 20 20" fill="none">
                            <rect x="2" y="5" width="16" height="11" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                            <path d="M2 9h16" stroke="currentColor" stroke-width="1.4"/>
                            <circle cx="6" cy="13" r="1" fill="currentColor"/>
                        </svg>
                        Rémunération
                    </legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="salaire_base">Salaire de base (Ar) <span class="required-star">*</span></label>
                            <div class="input-currency-wrap">
                                <input type="number" id="salaire_base" name="salaire_base"
                                       value="<?= $employe['salaire_base'] ?>"
                                       required min="0" step="500"
                                       placeholder="0">
                                <span class="currency-badge">Ar</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="rib">RIB / Compte bancaire</label>
                            <input type="text" id="rib" name="rib"
                                   value="<?= htmlspecialchars($employe['rib'] ?? '') ?>"
                                   placeholder="ex : BFV-SG 00000-00000">
                        </div>
                    </div>
                </fieldset>

                <!-- ── ACTIONS ── -->
                <div class="form-actions">
                    <a href="/employes/show/<?= $employe['id'] ?>" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-save">
                        <svg viewBox="0 0 20 20" fill="none">
                            <path d="M4 10l4.5 4.5L16 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Enregistrer les modifications
                    </button>
                </div>

            </form>
        </div><!-- /form-area -->

    </div><!-- /edit-layout -->

</div><!-- /page-wrapper -->

<script>
/* ─── Aperçu live ─── */
const previewName     = document.getElementById('previewName');
const previewPoste    = document.getElementById('previewPoste');
const previewStatus   = document.getElementById('previewStatus');
const previewSalaire  = document.getElementById('previewSalaire');
const previewInitials = document.getElementById('previewInitials');
const previewImg      = document.getElementById('previewImg');
const progressFill    = document.getElementById('progressFill');
const progressPct     = document.getElementById('progressPct');

function updatePreview() {
    const prenom = document.getElementById('prenom').value.trim();
    const nom    = document.getElementById('nom').value.trim();
    const poste  = document.getElementById('poste').value.trim();
    const statut = document.getElementById('statut').value;
    const sal    = parseInt(document.getElementById('salaire_base').value) || 0;

    if (previewName)    previewName.textContent = (prenom + ' ' + nom).trim() || '—';
    if (previewPoste)   previewPoste.textContent = poste || '—';
    if (previewSalaire) previewSalaire.textContent = sal.toLocaleString('fr-FR') + ' Ar / mois';

    if (previewStatus) {
        previewStatus.textContent = statut;
        previewStatus.className = 'preview-status ' + statut.toLowerCase();
    }

    if (previewInitials && prenom && nom) {
        previewInitials.textContent = (prenom[0] + nom[0]).toUpperCase();
    }
}

/* Progression */
function updateProgress() {
    const fields = ['prenom','nom','telephone','email','poste',
                    'date_naissance','cin','adresse','departement',
                    'type_contrat','date_embauche','salaire_base'];
    const filled = fields.filter(id => {
        const el = document.getElementById(id);
        return el && el.value.trim() !== '';
    }).length;
    const pct = Math.round((filled / fields.length) * 100);
    progressFill.style.width = pct + '%';
    progressPct.textContent  = pct + '%';
}

/* Photo preview */
document.getElementById('photo').addEventListener('change', function() {
    if (!this.files[0]) return;
    const url = URL.createObjectURL(this.files[0]);
    const wrap = document.getElementById('previewAvatar');
    // Remplacer initiales par image
    const existing = wrap.querySelector('.preview-initials, .preview-img');
    if (existing) existing.remove();
    const img = document.createElement('img');
    img.src = url; img.className = 'preview-img'; img.alt = 'Aperçu';
    wrap.insertBefore(img, wrap.firstChild);
});

/* Écouteurs */
['prenom','nom','poste','statut','salaire_base'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updatePreview);
    document.getElementById(id)?.addEventListener('change', updatePreview);
});

document.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('input',  updateProgress);
    el.addEventListener('change', updateProgress);
});

updateProgress();
</script>
</body>
</html>
