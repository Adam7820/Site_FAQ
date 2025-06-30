<?php
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

$id_question = isset($_GET['id']) ? intval($_GET['id']) : 0;

$qstmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$qstmt->execute([$id_question]);
$question = $qstmt->fetch();

if (!$question) {
    echo "❌ Question introuvable.";
    exit;
}

$reaction_counts = $pdo->prepare("SELECT niveau, COUNT(*) as total FROM reactions WHERE id_question = ? GROUP BY niveau");
$reaction_counts->execute([$id_question]);
$reactions = $reaction_counts->fetchAll(PDO::FETCH_KEY_PAIR);

function afficherCommentaires($pdo, $id_question, $id_parent = null, $niveau = 0) {
    $stmt = $pdo->prepare("SELECT * FROM commentaires WHERE id_question = ? AND id_parent " . ($id_parent ? "= ?" : "IS NULL") . " ORDER BY date_post ASC");
    $stmt->execute($id_parent ? [$id_question, $id_parent] : [$id_question]);
    $commentaires = $stmt->fetchAll();

    foreach ($commentaires as $com) {
        echo '<div style="margin-left:' . (20 * $niveau) . 'px; border:1px solid #ccc; padding:10px; margin-top:5px;">';
        echo '<p>' . htmlspecialchars($com['contenu']) . '</p>';

        echo '<form method="POST" action="add_commentaire.php">';
        echo '<input type="hidden" name="id_question" value="' . $id_question . '">';
        echo '<input type="hidden" name="id_parent" value="' . $com['id'] . '">';
        echo '<input type="text" name="contenu" placeholder="Répondre..." required>';
        echo '<button type="submit">Répondre</button>';
        echo '</form>';

        echo '<form method="POST" action="signaler.php" style="display:inline-block;margin-top:5px;">';
        echo '<input type="hidden" name="id_commentaire" value="' . $com['id'] . '">';
        echo '<input type="text" name="raison" placeholder="Raison du signalement" required>';
        echo '<button type="submit">Signaler</button>';
        echo '</form>';

        afficherCommentaires($pdo, $id_question, $com['id'], $niveau + 1);

        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Détail de la question</title>
    <style>
        .footer-fixed {
            position: fixed;
            bottom: 10px;
            left: 10px;
            background-color: white;
            padding: 5px 10px;
            border: 1px solid #ccc;
        }
        .reactions button {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<h2>📌 Question :</h2>
<p><?= htmlspecialchars($question['contenu']) ?></p>

<h4>📊 Merci de juger le niveau de pertinence :</h4>
<form method="POST" action="reaction.php" class="reactions">
    <input type="hidden" name="id_question" value="<?= $id_question ?>">
    <?php foreach (['rouge', 'orange', 'jaune', 'vert', 'bleu'] as $niv): ?>
        <button name="niveau" value="<?= $niv ?>" style="background-color:<?= $niv ?>;color:white;padding:5px;">
            <?= ucfirst($niv) ?> (<?= $reactions[$niv] ?? 0 ?>)
        </button>
    <?php endforeach; ?>
</form>

<hr>

<h3>💬 Commentaires :</h3>
<?php afficherCommentaires($pdo, $id_question); ?>

<h4>✍️ Ajouter un commentaire :</h4>
<form method="POST" action="add_commentaire.php">
    <input type="hidden" name="id_question" value="<?= $id_question ?>">
    <textarea name="contenu" rows="3" cols="50" required></textarea><br>
    <button type="submit">Envoyer</button>
</form>

<div class="footer-fixed">
    <a href="questions.php">⬅️ Retour aux questions</a>
</div>

</body>
</html>
