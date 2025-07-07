<?php
    $est_responsable = true;

    if (!$est_responsable) {
        echo "⛔ Accès refusé. Cette page est réservée aux responsables.";
        exit;
    }

    $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nouveau_mot"])) {
        $mot = trim($_POST["nouveau_mot"]);
        if (!empty($mot)) {
            $stmt = $pdo->prepare("INSERT INTO mots_interdits (mot) VALUES (?)");
            $stmt->execute([$mot]);
        }
        header("Location: admin_mots_interdits.php");
        exit;
    }

    if (isset($_GET["delete"])) {
        $id = intval($_GET["delete"]);
        $stmt = $pdo->prepare("DELETE FROM mots_interdits WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: admin_mots_interdits.php");
        exit;
    }

    $mots = $pdo->query("SELECT * FROM mots_interdits ORDER BY mot ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Gestion des mots interdits</title>
        <link rel="stylesheet" href="../../css/mots_interdit.css">
    </head>

    <body>
        <h2>🛡️ Gestion des mots interdits</h2>

        <form method="POST">
            <label for="nouveau_mot">Ajouter un mot interdit :</label><br>
            <input type="text" name="nouveau_mot" id="nouveau_mot" required>
            <button type="submit">Ajouter</button>
        </form>

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
                            <a class="supprimer" href="?delete=<?= $mot['id'] ?>" onclick="return confirm('Supprimer ce mot ?')">🗑️ Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php else: ?>
            <p>Aucun mot interdit pour le moment.</p>
        <?php endif; ?>

        <p><a href="../../dev/index.php" class="button">⬅️ Retour au menu</a></p>
    </body>
</html>
