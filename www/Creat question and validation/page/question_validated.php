<?php
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

$stmt = $pdo->query("SELECT * FROM questions WHERE statut = 'valide' ORDER BY date_envoi DESC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Questions validées</title>
        <link rel="stylesheet" href="../../css/question_validated.css">
    </head>

    <body>
        <h2>Liste des questions</h2>

        <?php foreach ($questions as $q): ?>
            <div class="question">
                <a class="acess" href="detail_question.php?id=<?= $q['id'] ?>">
                    <?= html_entity_decode(htmlspecialchars($q['contenu'])) ?>
                </a>
            </div>
        <?php endforeach; ?>

        <p><a href="../../dev/index.php" class="button">⬅️ Retour au menu</a></p>
    </body>
</html>
