<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Responsable</title>
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commentaire"])) {
    $commentaire = htmlspecialchars(trim($_POST["commentaire"]));

    if (!empty($commentaire)) {
        echo "<p><strong>Commentaire :</strong></p>";
        echo "<div style='border:1px solid #ccc; padding:10px;'>$commentaire</div>";
    } else {
        echo "<p>Aucun commentaire saisi.</p>";
    }
} else {
    echo "<p>Erreur : formulaire non soumis correctement.</p>";
}
?>
</body>
</html>

