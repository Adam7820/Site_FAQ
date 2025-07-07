<?php
    $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8","root","root");
    $id_question = intval($_POST['id_question']);
    $niveau = $_POST['niveau'];
    $id_user = crc32($_SERVER['REMOTE_ADDR']);

    $pdo->prepare("DELETE FROM reactions WHERE id_question=? AND id_user=?")->execute([$id_question,$id_user]);
    $pdo->prepare("INSERT INTO reactions (id_question,id_user,niveau) VALUES(?,?,?)")->execute([$id_question,$id_user,$niveau]);

    $r = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question=? AND niveau = 'rouge'");
    $r->execute([$id_question]);
    $nbR = $r->fetchColumn();
    $o = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question=? AND niveau = 'orange'");
    $o->execute([$id_question]);
    $nbO = $o->fetchColumn();

    if ($nbR >=20 || ($nbR>=10 && $nbO>=10)) {
        $pdo->prepare("DELETE FROM questions WHERE id=?")->execute([$id_question]);
        $pdo->prepare("DELETE FROM reactions WHERE id_question=?")->execute([$id_question]);
        $pdo->prepare("DELETE FROM commentaires WHERE id_question=?")->execute([$id_question]);
        echo "🗑️ Question supprimée suite aux votes négatifs.";
        exit;
    }
    echo "✅ Réaction enregistrée !";
