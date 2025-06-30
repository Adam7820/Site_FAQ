<?php
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8", "root", "root");

$id_commentaire = intval($_POST['id_commentaire']);
$raison = trim($_POST['raison']);

$stmt = $pdo->prepare("INSERT INTO signalements (id_commentaire, raison) VALUES (?, ?)");
$stmt->execute([$id_commentaire, $raison]);

echo "Commentaire signalé. Merci !";
