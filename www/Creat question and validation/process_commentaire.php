<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base : " . $e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["commentaire"])) {
        $commentaire = htmlspecialchars(trim($_POST["commentaire"]));

        $stmt = $pdo->prepare("INSERT INTO questions (contenu, statut, date_envoi) VALUES (?, 'en_attente', NOW())");
        $stmt->execute([$commentaire]);

        header("Location: page/merci.php");
        exit;
    } else {
        echo "Le commentaire est vide ou invalide.";
    }
