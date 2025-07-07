<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8","root","");
$id_user = $_SESSION['userId'] ?? null;
if (!$id_user) {
    die("⛔ Vous devez être connecté pour commenter.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Message</title>
    <style>
        .message-erreur {
            background: rgba(255, 0, 0, 0.1);
            color: #ff4d4f;
            padding: 1rem 1.5rem;
            margin: 2rem auto;
            text-align: center;
            border: 1px solid #ff4d4f;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            max-width: 600px;
            box-shadow: 0 0 15px rgba(255, 77, 79, 0.2);
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
<?php

$check = $pdo->prepare("SELECT * FROM bannissements WHERE id_user = ? AND date_fin > NOW()");
$check->execute([$id_user]);
if ($check->rowCount()) {
    die("⛔ Vous êtes temporairement banni.");
}

$id_question = intval($_POST['id_question'] ?? 0);
$id_parent = isset($_POST['id_parent']) ? intval($_POST['id_parent']) : null;
$contenu = trim($_POST['contenu'] ?? '');

if (!$id_question || !$contenu) {
    die("❌ Données manquantes.");
}

foreach ($pdo->query("SELECT mot FROM mots_interdits") as $m) {
    if (stripos($contenu, $m['mot']) !== false) {
        echo '<div class="message-erreur">❌ Mot interdit.</div>';
        exit;
    }
}

$stmt = $pdo->prepare("INSERT INTO commentaires (id_question, id_parent, id_user, contenu) VALUES(?,?,?,?)");
$stmt->execute([$id_question, $id_parent, $id_user, $contenu]);

header("Location: page/detail_question.php?id=$id_question");
exit;
?>
</body>
</html>
