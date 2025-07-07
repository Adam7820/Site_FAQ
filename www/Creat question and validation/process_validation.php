<?php
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Responsable') {
        echo "⛔ Accès refusé.";
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
        $id = intval($_POST['id']);

        if ($_POST['action'] === 'valider') {
            $stmt = $pdo->prepare("UPDATE questions SET statut = 'valide' WHERE id = ?");
            $stmt->execute([$id]);
        } elseif ($_POST['action'] === 'supprimer') {
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->execute([$id]);
        }
    }

    header("Location: page/responsable.php");
    exit;
