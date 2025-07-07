<?php
    session_start();
    if (!($_SESSION['id_user'] ?? null)) die("Non autorisé");
    $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8","root","root");
    $cid = intval($_POST['cid']);
    $uid = intval($_POST['uid']);
    $act = $_POST['action'];

    if ($act === 'suppr_commentaire') {
        $pdo->prepare("UPDATE commentaires SET statut='supprime' WHERE id=?")->execute([$cid]);
    }
    if ($act === 'bloquer') {
        $fin = date('Y-m-d H:i:s', strtotime('+3 days'));
        $pdo->prepare("INSERT INTO bannissements (id_user,date_fin) VALUES(?,?)")
            ->execute([$uid, $fin]);
        $pdo->prepare("UPDATE commentaires SET statut='supprime' WHERE id=?")->execute([$cid]);
    }
    if ($act === 'suppr_user') {
        $pdo->prepare("DELETE FROM users WHERE id_user=?")->execute([$uid]);
        $pdo->prepare("DELETE FROM commentaires WHERE id_user=?")->execute([$uid]);
        $pdo->prepare("DELETE FROM bannissements WHERE id_user=?")->execute([$uid]);
    }

    header("Location: admin_signalements.php");
    exit;
