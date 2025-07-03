<?php
    $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

    $id_question = intval($_POST['id_question']);
    $id_parent = isset($_POST['id_parent']) ? intval($_POST['id_parent']) : null;
    $contenu = trim($_POST['contenu']);

    $interdits = $pdo->query("SELECT mot FROM mots_interdits")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($interdits as $mot) {
        if (stripos($contenu, $mot) !== false) {
            echo "Mot interdit détecté dans le commentaire.";
            exit;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO commentaires (id_question, id_parent, contenu) VALUES (?, ?, ?)");
    $stmt->execute([$id_question, $id_parent, $contenu]);

    header("Location: detail_question.php?id=$id_question");
    exit;
