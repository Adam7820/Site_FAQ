<?php
session_start();

$_SESSION['user'] = [
    'id_user' => 1,
    'role' => 'Responsable',
    'email' => 'responsable@example.com'
];

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Responsable') {
    echo "⛔ Accès refusé. Seuls les responsables peuvent accéder à cette page.";
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$questions = $pdo->query("SELECT * FROM questions WHERE statut = 'en_attente' ORDER BY date_envoi DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Interface Responsable</title>
        <link rel="stylesheet" href="../../css/responsable.css">
    </head>
    <body>
        <h2>Questions en attente</h2>

        <?php if (empty($questions)): ?>
            <p>Aucune question en attente.</p>
        <?php else: ?>
            <?php foreach ($questions as $q): ?>
                <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                    <p><?= html_entity_decode(htmlspecialchars($q['contenu'])) ?></p>
                    <form method="POST" action="/Site_FAQ/www/Creat question and validation/process_validation.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $q['id'] ?>">
                        <button name="action" value="valider">✅ Ajouter</button>
                        <button name="action" value="supprimer" onclick="return confirm('Supprimer cette question ?')">🗑️
                            Supprimer
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <p><a href="/Site_FAQ/www/dev/index.php" class="button">⬅️ Retour au menu</a></p>
    </body>
</html>
