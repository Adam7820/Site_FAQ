<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connexion échouée : " . $e->getMessage());
}

$id_user = 1;

if (isset($_POST['id_question'], $_POST['niveau'])) {
    $id_question = intval($_POST['id_question']);
    $niveau = $_POST['niveau'];

    $niveaux_valides = ['rouge', 'orange', 'jaune', 'vert', 'bleu'];
    if (!in_array($niveau, $niveaux_valides)) {
        die("⚠️ Niveau invalide.");
    }

    $check = $pdo->prepare("SELECT id FROM reactions WHERE id_question = ? AND id_user = ?");
    $check->execute([$id_question, $id_user]);

    if ($check->rowCount() > 0) {
        // Mise à jour
        $update = $pdo->prepare("UPDATE reactions SET niveau = ?, date_reaction = NOW() WHERE id_question = ? AND id_user = ?");
        $update->execute([$niveau, $id_question, $id_user]);
    } else {
        $insert = $pdo->prepare("INSERT INTO reactions (id_question, id_user, niveau) VALUES (?, ?, ?)");
        $insert->execute([$id_question, $id_user, $niveau]);
    }

    $count = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question = ? AND niveau = 'rouge'");
    $count->execute([$id_question]);
    $nbRouge = $count->fetchColumn();

    if ($nbRouge >= 20) {
        $del = $pdo->prepare("DELETE FROM questions WHERE id = ?");
        $del->execute([$id_question]);

        $pdo->prepare("DELETE FROM reactions WHERE id_question = ?")->execute([$id_question]);
        $pdo->prepare("DELETE FROM commentaires WHERE id_question = ?")->execute([$id_question]);

        echo "🗑️ Question supprimée (trop de réactions rouges : $nbRouge)";
    } else {
        echo "✅ Réaction enregistrée. Nombre de rouges : $nbRouge";
    }

} else {
    echo "⛔ Données manquantes.";
}
