<?php
// Connexion à la BDD
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

// Ajouter un mot interdit
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nouveau_mot"])) {
    $mot = trim($_POST["nouveau_mot"]);
    if (!empty($mot)) {
        $stmt = $pdo->prepare("INSERT INTO mots_interdits (mot) VALUES (?)");
        $stmt->execute([$mot]);
    }
    header("Location: admin_mots_interdits.php");
    exit;
}

// Supprimer un mot interdit
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $stmt = $pdo->prepare("DELETE FROM mots_interdits WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_mots_interdits.php");
    exit;
}

// Récupérer la liste des mots interdits
$mots = $pdo->query("SELECT * FROM mots_interdits ORDER BY mot ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des mots interdits</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; }
        th { background-color: #f5f5f5; }
        form { margin-top: 20px; }
    </style>
</head>
<body>

<h2>🛡️ Gestion des mots interdits</h2>

<!-- Formulaire d'ajout -->
<form method="POST">
    <label for="nouveau_mot">Ajouter un mot interdit :</label><br>
    <input type="text" name="nouveau_mot" id="nouveau_mot" required>
    <button type="submit">Ajouter</button>
</form>

<!-- Liste des mots interdits -->
<?php if (count($mots) > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Mot</th>
            <th>Action</th>
        </tr>
        <?php foreach ($mots as $mot): ?>
            <tr>
                <td><?= $mot['id'] ?></td>
                <td><?= htmlspecialchars($mot['mot']) ?></td>
                <td>
                    <a href="?delete=<?= $mot['id'] ?>" onclick="return confirm('Supprimer ce mot ?')">🗑️ Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucun mot interdit pour le moment.</p>
<?php endif; ?>

<p><a href="menu.php">⬅️ Retour au menu</a></p>

</body>
</html>
