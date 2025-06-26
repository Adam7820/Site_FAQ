<?php
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_question'], $_POST['niveau'])) {
    $id = intval($_POST['id_question']);
    $niv = $_POST['niveau'];

    $stmt = $pdo->prepare("INSERT INTO reactions (id_question, niveau) VALUES (?, ?)");
    $stmt->execute([$id, $niv]);

    // Si rouge ≥ 20 => suppression
    $count = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question = ? AND niveau = 'rouge'");
    $count->execute([$id]);
    $rouges = $count->fetchColumn();

    if ($rouges >= 20) {
        $del = $pdo->prepare("DELETE FROM questions WHERE id = ?");
        $del->execute([$id]);
    }
}

header("Location: detail_question.php?id=$id");
exit;
