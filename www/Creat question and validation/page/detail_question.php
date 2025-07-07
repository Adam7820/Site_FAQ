<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");
$_SESSION['id_user'] = 1; // Pour test uniquement

$id_question = intval($_GET['id'] ?? 0);

// Vérification et récupération de la question
$qstmt = $pdo->prepare("SELECT * FROM questions WHERE id=?");
$qstmt->execute([$id_question]);
$question = $qstmt->fetch();

if (!$question) {
    echo "❌ Question introuvable.";
    exit;
}

// Récupération des commentaires principaux
$coms = $pdo->prepare("SELECT c.*, u.first_name, u.last_name FROM commentaires c
                       JOIN users u ON c.id_user = u.id_user
                       WHERE c.id_question = ? AND c.id_parent IS NULL AND c.statut != 'supprime'
                       ORDER BY c.date_post");
$coms->execute([$id_question]);
$commentaires = $coms->fetchAll();

// Fonction récursive
function afficherReponses($pdo, $id_parent, $niveau = 1) {
    $stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name FROM commentaires c
                           JOIN users u ON c.id_user = u.id_user
                           WHERE c.id_parent = ? AND c.statut != 'supprime'
                           ORDER BY c.date_post");
    $stmt->execute([$id_parent]);
    $reponses = $stmt->fetchAll();

    foreach ($reponses as $r) {
        echo '<div class="commentaire" style="margin-left:' . (20 * $niveau) . 'px;">';
        echo '<p><strong>' . htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) . ' :</strong> ' . htmlspecialchars($r['contenu']) . '</p>';

        // Répondre
        echo '<form method="POST" action="../add_commentaire.php">';
        echo '<input type="hidden" name="id_question" value="' . $r['id_question'] . '">';
        echo '<input type="hidden" name="id_parent" value="' . $r['id'] . '">';
        echo '<input type="text" name="contenu" placeholder="Répondre..." required>';
        echo '<button type="submit">Répondre</button>';
        echo '</form>';

        // Signaler
        echo '<form method="POST" action="../signaler.php" style="display:inline-block;margin-top:5px;">';
        echo '<input type="hidden" name="id_commentaire" value="' . $r['id'] . '">';
        echo '<input type="text" name="raison" placeholder="Raison du signalement" required>';
        echo '<button type="submit">Signaler</button>';
        echo '</form>';

        afficherReponses($pdo, $r['id'], $niveau + 1);

        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Détail de la question</title>
    <link rel="stylesheet" href="../../css/detail_question.css">
</head>
<body>


<h2>📌 Question</h2>
<p class="question-texte"><?= htmlspecialchars($question['contenu']) ?></p>

<h4>📊 Merci de juger le niveau de pertinence :</h4>

<div class="reactions" id="reaction-buttons">
    <?php foreach (['#e74c3c', 'orange', '#f1c40f', '#2ecc71', '#3498db'] as $couleur): ?>
        <button type="button" class="niv-pertinence" data-niveau="<?= $couleur ?>" style="background-color:<?= $couleur ?>;"></button>
    <?php endforeach; ?>
    <input type="hidden" id="id_question" value="<?= $id_question ?>">
</div>
<div id="reaction-message" style="text-align:center; margin-top:10px;"></div>

<hr>

<h3>💬 Commentaires</h3>

<?php foreach ($commentaires as $c): ?>
    <div class="commentaire">
        <p><strong><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?> :</strong> <?= htmlspecialchars($c['contenu']) ?></p>

        <form method="POST" action="../add_commentaire.php">
            <input type="hidden" name="id_question" value="<?= $id_question ?>">
            <input type="hidden" name="id_parent" value="<?= $c['id'] ?>">
            <input type="text" name="contenu" placeholder="Répondre..." required>
            <button type="submit">Répondre</button>
        </form>

        <form method="POST" action="../signaler.php">
            <input type="hidden" name="id_commentaire" value="<?= $c['id'] ?>">
            <input type="text" name="raison" placeholder="Raison du signalement" required>
            <button type="submit">Signaler</button>
        </form>

        <?php afficherReponses($pdo, $c['id']); ?>
    </div>
<?php endforeach; ?>

<form method="POST" action="../add_commentaire.php" class="form-commentaire">
    <input type="hidden" name="id_question" value="<?= $id_question ?>">
    <textarea name="contenu" rows="3" required></textarea>
    <button type="submit">Envoyer</button>
</form>

<script>
    document.querySelectorAll('.niv-pertinence').forEach(button => {
        button.addEventListener('click', () => {
            const niveau = button.dataset.niveau;
            const id_question = document.getElementById('id_question').value;

            fetch('../reaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id_question=${id_question}&niveau=${encodeURIComponent(niveau)}`
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('reaction-message').innerText = data;

                    // Mise à jour visuelle
                    document.querySelectorAll('.niv-pertinence').forEach(btn => {
                        btn.style.outline = 'none';
                        btn.style.boxShadow = 'none';
                    });
                    button.style.outline = '2px solid #fff';
                    button.style.boxShadow = '0 0 10px white';
                });
        });
    });
</script>

<a href="../../dev/index.php" class="button">⬅️ Retour au menu</a>

</body>
</html>
