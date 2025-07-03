<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Traitement du formulaire
$rapport_envoye = false;
$erreur = '';
$reports = [];

// Charger les rapports existants (simulation d'une base de données)
if (file_exists('reports_data.json')) {
    $reports_data = file_get_contents('reports_data.json');
    $reports = json_decode($reports_data, true) ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $type_probleme = htmlspecialchars(trim($_POST['type_probleme']));
    $urgence = htmlspecialchars(trim($_POST['urgence']));
    $description = htmlspecialchars(trim($_POST['description']));
    
    // Validation
    if (empty($nom) || empty($email) || empty($type_probleme) || empty($urgence) || empty($description)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (strlen($description) < 20) {
        $erreur = "La description doit contenir au moins 20 caractères.";
    } else {
        // Créer un nouveau rapport
        $nouveau_rapport = [
            'id' => uniqid(),
            'nom' => $nom,
            'email' => $email,
            'type_probleme' => $type_probleme,
            'urgence' => $urgence,
            'description' => $description,
            'date' => date('Y-m-d H:i:s'),
            'status' => 'En attente'
        ];
        
        // Ajouter au début du tableau
        array_unshift($reports, $nouveau_rapport);
        
        // Sauvegarder dans le fichier JSON
        file_put_contents('reports_data.json', json_encode($reports, JSON_PRETTY_PRINT));
        
        $rapport_envoye = true;
        
        // Réinitialiser le formulaire
        $_POST = [];
    }
}

$page_title = "Signaler un problème - ESIEE-IT";
$css_file = "report.css";
include '../utils/header.php';
?>

<link rel="stylesheet" href="../css/index.css">
<link rel="stylesheet" href="../css/report.css">

<main>
    <div class="report-container">
        <div class="report-header">
            <h1>🚨 Signaler un problème</h1>
            <p>Utilisez ce formulaire pour signaler tout problème technique, sécuritaire ou organisationnel</p>
        </div>

        <div class="form-container">
            <?php if ($rapport_envoye): ?>
                <div class="alert alert-success">
                    ✅ Votre rapport a été envoyé avec succès !
                    <br><small>L'équipe technique a été notifiée et prendra en charge le problème selon son niveau d'urgence.</small>
                </div>
            <?php endif; ?>

            <?php if ($erreur): ?>
                <div class="alert alert-error">
                    ❌ <?php echo $erreur; ?>
                </div>
            <?php endif; ?>

            <form id="reportForm" method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom complet *</label>
                        <input type="text" id="nom" name="nom" placeholder="Votre nom et prénom" 
                               value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse e-mail *</label>
                        <input type="email" id="email" name="email" placeholder="votre.email@esiee-it.fr" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        <small>Pour vous recontacter si nécessaire</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type_probleme">Type de problème *</label>
                        <select id="type_probleme" name="type_probleme" required>
                            <option value="">Sélectionnez le type</option>
                            <option value="Technique - Informatique" <?php echo (isset($_POST['type_probleme']) && $_POST['type_probleme'] === 'Harcèlement') ? 'selected' : ''; ?>>
                                Harcèlement
                            </option>
                            <option value="Technique - Équipement" <?php echo (isset($_POST['type_probleme']) && $_POST['type_probleme'] === 'Problème avec un PO') ? 'selected' : ''; ?>>
                                Problème avec un PO
                            </option>
                            <option value="Sécurité" <?php echo (isset($_POST['type_probleme']) && $_POST['type_probleme'] === 'Sécurité') ? 'selected' : ''; ?>>
                                Sécurité
                            </option>
                            <option value="Infrastructure" <?php echo (isset($_POST['type_probleme']) && $_POST['type_probleme'] === 'emplois du temps') ? 'selected' : ''; ?>>
                                emplois du temps
                            </option>
                            <option value="Organisation" <?php echo (isset($_POST['type_probleme']) && $_POST['type_probleme'] === 'Organisation') ? 'selected' : ''; ?>>
                                Organisation
                            </option>
                            <option value="Autre" <?php echo (isset($_POST['type_probleme']) && $_POST['type_probleme'] === 'Autre') ? 'selected' : ''; ?>>
                                Autre
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="urgence">Niveau d'urgence *</label>
                        <select id="urgence" name="urgence" required>
                            <option value="">Sélectionnez l'urgence</option>
                            <option value="Critique" <?php echo (isset($_POST['urgence']) && $_POST['urgence'] === 'Critique') ? 'selected' : ''; ?>>
                                🔴 Critique
                            </option>
                            <option value="Haute" <?php echo (isset($_POST['urgence']) && $_POST['urgence'] === 'Haute') ? 'selected' : ''; ?>>
                                🟠 Haute
                            </option>
                            <option value="Normale" <?php echo (isset($_POST['urgence']) && $_POST['urgence'] === 'Normale') ? 'selected' : ''; ?>>
                                🟡 Normale
                            </option>
                            <option value="Basse" <?php echo (isset($_POST['urgence']) && $_POST['urgence'] === 'Basse') ? 'selected' : ''; ?>>
                                🟢 Basse
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description détaillée du problème *</label>
                    <textarea id="description" name="description" placeholder="Décrivez le problème en détail : que s'est-il passé ? quand ? dans quelles circonstances ?" 
                              rows="6" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <div class="char-counter">
                        <span id="charCount">0</span> caractères (minimum 20)
                    </div>
                </div>

                <div class="button-group">
                    <a href="index.php" class="button back-button">Retour à l'accueil</a>
                    <button type="submit" class="button submit-button" id="submitBtn">
                        🚨 Envoyer le rapport
                    </button>
                </div>
            </form>
        </div>

        <!-- Affichage des rapports existants -->
        <div class="reports-list">
            <h2>📋 Rapports récents</h2>
            <?php if (empty($reports)): ?>
                <div class="no-reports">
                    <p>Aucun rapport pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="reports-grid">
                    <?php foreach (array_slice($reports, 0, 5) as $report): ?>
                        <div class="report-card urgence-<?php echo strtolower($report['urgence']); ?>">
                            <div class="report-header-card">
                                <div class="report-type"><?php echo htmlspecialchars($report['type_probleme']); ?></div>
                                <div class="report-urgence"><?php echo htmlspecialchars($report['urgence']); ?></div>
                            </div>
                            <div class="report-content">
                                <p class="report-description"><?php echo htmlspecialchars(substr($report['description'], 0, 100)); ?>...</p>
                                <div class="report-meta">
                                    <span class="report-author">Par <?php echo htmlspecialchars($report['nom']); ?></span>
                                    <span class="report-date"><?php echo date('d/m/Y à H:i', strtotime($report['date'])); ?></span>
                                </div>
                                <div class="report-status status-<?php echo strtolower(str_replace(' ', '-', $report['status'])); ?>">
                                    <?php echo htmlspecialchars($report['status']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../utils/footer.php'; ?>

<script>
    // Compteur de caractères
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');

    descriptionTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length < 20) {
            charCount.style.color = '#dc3545';
            submitBtn.disabled = true;
        } else {
            charCount.style.color = '#28a745';
            submitBtn.disabled = false;
        }
    });

    // Validation du formulaire
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const nom = document.getElementById('nom').value.trim();
        const email = document.getElementById('email').value.trim();
        const type_probleme = document.getElementById('type_probleme').value;
        const urgence = document.getElementById('urgence').value;
        const description = document.getElementById('description').value.trim();

        if (!nom || !email || !type_probleme || !urgence || description.length < 20) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires correctement.');
            return;
        }

        // Confirmation selon l'urgence
        if (urgence === 'Critique') {
            if (!confirm('⚠️ ATTENTION : Vous avez sélectionné "CRITIQUE".\nCeci nécessite une intervention immédiate.\nÊtes-vous certain que c\'est justifié ?')) {
                e.preventDefault();
                return;
            }
        }
    });

    // Initialisation du compteur
    descriptionTextarea.dispatchEvent(new Event('input'));
</script>

</body>
</html>