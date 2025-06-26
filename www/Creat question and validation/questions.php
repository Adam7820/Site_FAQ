<?php
// Connexion
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

// Récupérer toutes les questions validées
$stmt = $pdo->query("SELECT * FROM questions WHERE statut = 'valide' ORDER BY date_envoi DESC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Questions validées</title>
    <style>
        .question { margin: 10px 0; border: 1px solid #ccc; padding: 10px; }
        .footer-fixed { position: fixed; bottom: 0; left: 0; right: 0; background: #eee; padding: 10px; text-align: center; }
    </style>
</head>
<body>
<h2>Liste des questions</h2>

<?php foreach ($questions as $q): ?>
    <div class="question">
        <a href="detail_question.php?id=<?= $q['id'] ?>">
            <?= htmlspecialchars($q['contenu']) ?>
        </a>
    </div>
<?php endforeach; ?>

<div class="footer-fixed">
    <a href="menu.php">🔙 Retour au menu</a>
</div>
</body>
</html>
