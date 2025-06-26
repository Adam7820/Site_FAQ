<?php
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

$id_question = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérifie existence
$qstmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$qstmt->execute([$id_question]);
$question = $qstmt->fetch();

if (!$question) {
    echo "Question introuvable.";
    exit;
}

// Récupération des commentaires
$cstmt = $pdo->prepare("SELECT * FROM commentaires WHERE id_question = ? AND id_parent IS NULL ORDER BY date_post DESC");
$cstmt->execute([$id_question]);
$commentaires = $cstmt->fetchAll();

// Réactions
$reaction_counts = $pdo->prepare("SELECT niveau, COUNT(*) as total FROM reactions WHERE id_question = ? GROUP BY niveau");
$reaction_counts->execute([$id_question]);
$reactions = $reaction_counts->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Détail question</title>
</head>
<body>
<h2>Question :</h2>
<p><?= htmlspecialchars($question['contenu']) ?></p>

<h4>Merci de juger le niveau de pertinence :</h4>
<form method="POST" action="reaction.php">
    <input type="hidden" name="id_question" value="<?= $id_question ?>">
    <?php foreach (['rouge', 'orange', 'jaune', 'vert', 'bleu'] as $niv): ?>
        <button name="niveau" value="<?= $niv ?>"><?= ucfirst($niv) ?> (<?= $reactions[$niv] ?? 0 ?>)</button>
    <?php endforeach; ?>
</form>

<hr>
<h3>Commentaires :</h3>
<?php foreach ($commentaires as $com): ?>
    <div style="border:1px solid #ccc;padding:8px;margin:5px;">
        <p><?= htmlspecialchars($com['contenu']) ?></p>

        <!-- Formulaire de réponse -->
        <form method="POST" action="add_commentaire.php">
            <input type="hidden" name="id_question" value="<?= $id_question ?>">
            <input type="hidden" name="id_parent" value="<?= $com['id'] ?>">
            <input type="text" name="contenu" placeholder="Répondre..." required>
            <button type="submit">Répondre</button>
        </form>

        <!-- Bouton de signalement -->
        <form method="POST" action="signaler.php" style="display:inline;">
            <input type="hidden" name="id_commentaire" value="<?= $com['id'] ?>">
            <input type="text" name="raison" placeholder="Raison du signalement" required>
            <button type="submit">Signaler</button>
        </form>

        <!-- Affichage des réponses -->
        <?php
        $repstmt = $pdo->prepare("SELECT * FROM commentaires WHERE id_parent = ?");
        $repstmt->execute([$com['id']]);
        $reponses = $repstmt->fetchAll();
        foreach ($reponses as $rep): ?>
            <div style="margin-left:20px; border-left: 2px solid #aaa; padding-left: 10px;">
                <p><?= htmlspecialchars($rep['contenu']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<h4>Ajouter un commentaire :</h4>
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
