<?php
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_question = intval($_POST['id_question']);
    $niveau = $_POST['niveau'];
    $ip = $_SERVER['REMOTE_ADDR']; // On simule l'utilisateur par IP

    $check = $pdo->prepare("SELECT * FROM reactions WHERE id_question = ? AND id_user = ?");
    $check->execute([$id_question, crc32($ip)]);
    if ($check->rowCount() > 0) {
        $pdo->prepare("DELETE FROM reactions WHERE id_question = ? AND id_user = ?")
            ->execute([$id_question, crc32($ip)]);
    }

    $insert = $pdo->prepare("INSERT INTO reactions (id_question, id_user, niveau) VALUES (?, ?, ?)");
    $insert->execute([$id_question, crc32($ip), $niveau]);

    $count_red = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question = ? AND niveau = 'rouge'");
    $count_red->execute([$id_question]);
    $nb_rouges = $count_red->fetchColumn();

    if ($nb_rouges >= 20) {
        $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$id_question]);
        $pdo->prepare("DELETE FROM reactions WHERE id_question = ?")->execute([$id_question]);
        $pdo->prepare("DELETE FROM commentaires WHERE id_question = ?")->execute([$id_question]);
        echo "🗑️ Question supprimée suite à trop de votes rouges.";
    } else {
        echo "✅ Réaction enregistrée !";
    }
}
?>
