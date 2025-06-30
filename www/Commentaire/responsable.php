<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq", "root", "root");

// Vérifie si l'utilisateur est connecté et responsable
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "Responsable") {
    header("Location: login.php");
    exit;
}

$questions = $pdo->query("SELECT * FROM questions WHERE statut = 'en_attente' ORDER BY date_envoi DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Responsable</title>
</head>
<body>
<h2>Questions en attente de validation</h2>

<?php foreach ($questions as $q): ?>
    <div style="border:1px solid #aaa; padding:10px; margin-bottom:10px;">
        <p><?= htmlspecialchars($q['contenu']) ?></p>
        <form action="process_validation.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $q['id'] ?>">
            <button name="action" value="valider">Ajouter</button>
            <button name="action" value="supprimer" onclick="return confirm('Supprimer cette question ?')">Supprimer</button>
        </form>
    </div>
<?php endforeach; ?>
</body>
</html>
