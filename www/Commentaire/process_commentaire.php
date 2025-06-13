<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO("mysql:host=localhost;dbname=coding_faq", "root", "root");

// Sécurisation
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["commentaire"])) {
    $commentaire = htmlspecialchars(trim($_POST["commentaire"]));
    $stmt = $pdo->prepare("INSERT INTO questions (contenu) VALUES (?)");
    $stmt->execute([$commentaire]);
}

// Redirection vers la page de remerciement
header("Location: merci.php");
exit;
