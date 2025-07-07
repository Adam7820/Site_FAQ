<?php
    session_start();
    $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8","root","");
    $id_commentaire = intval($_POST['id_commentaire']);
    $id_signaleur = $_SESSION['userId'] ?? die("Not logged");
    $raison = trim($_POST['raison']);

    $pdo->prepare("UPDATE commentaires SET statut='signale' WHERE id=?")->execute([$id_commentaire]);
    $pdo->prepare("INSERT INTO signalements (id_commentaire,id_signaleur,raison) VALUES(?,?,?)")
        ->execute([$id_commentaire,$id_signaleur,$raison]);

    echo "✅ Signalement enregistré.";
