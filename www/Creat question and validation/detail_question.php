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
        echo '<div class="commentaire" style="margin-left:' . (20 * $niveau) . 'px;">';
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
    <link rel="stylesheet" href="../css/detail_question.css">
</head>
<script>
    document.querySelectorAll('.niv-pertinence').forEach(button => {
        button.addEventListener('click', () => {
            const niveau = button.dataset.niveau;
            const id_question = document.getElementById('id_question').value;

            fetch('reaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id_question=${encodeURIComponent(id_question)}&niveau=${encodeURIComponent(niveau)}`
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('reaction-message').innerText = data;

                    // Désactive tous les boutons visuellement
                    document.querySelectorAll('.niv-pertinence').forEach(btn => {
                        btn.style.outline = 'none';
                        btn.style.boxShadow = 'none';
                    });

                    // Mettez en évidence le bouton sélectionné
                    button.style.outline = '2px solid #fff';
                    button.style.boxShadow = '0 0 10px white';
                });
        });
    });
</script>

<body>

<h2>📌 Question :</h2>
<p class="question-texte"><?= html_entity_decode(htmlspecialchars($question['contenu'])) ?></p>

<h4>📊 Merci de juger le niveau de pertinence :</h4>


<div class="reactions" id="reaction-buttons">
    <?php foreach (['#e74c3c', 'orange', '#f1c40f', '#2ecc71', '#3498db'] as $niv): ?>
        <button type="button" class="niv-pertinence" data-niveau="<?= $niv ?>" title="<?= ucfirst($niv) ?>" style="background-color:<?= $niv ?>;"></button>
    <?php endforeach; ?>
    <input type="hidden" id="id_question" value="<?= $id_question ?>">
</div>
<div id="reaction-message" style="text-align:center; margin-top:10px;"></div>


<hr>

<h3>💬 Commentaires :</h3>
<?php afficherCommentaires($pdo, $id_question); ?>

<h4>✍️ Ajouter un commentaire :</h4>
<form method="POST" action="add_commentaire.php" class="form-commentaire">
    <input type="hidden" name="id_question" value="<?= $id_question ?>">
    <textarea name="contenu" rows="3" cols="50" required></textarea>
    <button type="submit">Envoyer</button>
</form>


<p><a href="../dev/index.php" class="button">⬅️ Retour au menu</a></p>


</body>
</html>
