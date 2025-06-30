<?php
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq", "root", "root");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"], $_POST["action"])) {
    $id = intval($_POST["id"]);

    if ($_POST["action"] === "valider") {
        $stmt = $pdo->prepare("UPDATE questions SET statut = 'publie' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_POST["action"] === "supprimer") {
        $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header("Location: responsable.php");
exit;


