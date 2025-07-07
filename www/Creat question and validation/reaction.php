<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=svtozq_codingfaq;charset=utf8", "root", "");

$id_user = $_SESSION['userId'] ?? null;

if (!$id_user) {
    echo "⚠️ Vous devez être connecté pour commenter.";
    exit;
}

$id_question = intval($_POST['id_question'] ?? 0);
$niveau = $_POST['niveau'] ?? null;

if (!$id_user) {
    echo "🚫 Vous devez être connecté pour réagir.";
    exit;
}

if (!$id_question || !$niveau) {
    echo "❌ Données invalides.";
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question = ? AND id_user = ?");
$check->execute([$id_question, $id_user]);
if ($check->fetchColumn() > 0) {
    echo "⚠️ Vous avez déjà réagi.";
    exit;
}

$insert = $pdo->prepare("INSERT INTO reactions (id_question, id_user, niveau) VALUES (?, ?, ?)");
$insert->execute([$id_question, $id_user, $niveau]);

$r = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question=? AND niveau = 'rouge'");
$r->execute([$id_question]);
$nbR = $r->fetchColumn();

$o = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE id_question=? AND niveau = 'orange'");
$o->execute([$id_question]);
$nbO = $o->fetchColumn();

if ($nbR >= 20 || ($nbR >= 10 && $nbO >= 10)) {
    $pdo->prepare("DELETE FROM questions WHERE id=?")->execute([$id_question]);
    $pdo->prepare("DELETE FROM reactions WHERE id_question=?")->execute([$id_question]);
    $pdo->prepare("DELETE FROM commentaires WHERE id_question=?")->execute([$id_question]);
    echo "🗑️ Question supprimée suite aux votes négatifs.";
    exit;
}

echo "✅ Réaction enregistrée !";