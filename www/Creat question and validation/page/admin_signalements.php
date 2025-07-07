<?php
session_start();
if (!($_SESSION['userId'] ?? null)) die("Non autorisé");
$pdo = new PDO("mysql:host=localhost;dbname=coding_faq;charset=utf8","root","");

$sigs = $pdo->query("
  SELECT s.id AS sid, s.raison, s.date_signalement, c.id AS cid, c.id_user, c.contenu, u.first_name, u.last_name
  FROM signalements s
  JOIN commentaires c ON s.id_commentaire = c.id
  JOIN users u ON c.id_user = u.id_user
  WHERE c.statut='signale'
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Modération</title>
        <link rel="stylesheet" href="../../css/admin_signalements.css">
    </head>

    <body>
        <h2>Modération des signalements</h2>
        <?php foreach($sigs as $s): ?>
            <div style="border:1px solid #ccc; padding:10px; margin:10px;">
                <p><strong><?=htmlspecialchars($s['first_name'].' '.$s['last_name'])?></strong> : <?=htmlspecialchars($s['contenu'])?></p>
                <p>Signalé pour : <?=htmlspecialchars($s['raison'])?></p>
                <form method="POST" action="../traitement_signalement.php">
                    <input type="hidden" name="cid" value="<?=$s['cid']?>">
                    <input type="hidden" name="uid" value="<?=$s['id_user']?>">
                    <button name="action" value="suppr_commentaire">🗑️ Supprimer commentaire</button>
                    <button name="action" value="bloquer">⏳ Bloquer 3 jours</button>
                    <button name="action" value="suppr_user">🚫 Supprimer utilisateur</button>
                </form>
            </div>
        <?php endforeach; ?>
        <p><a href="../../dev/index.php" class="button">⬅️ Retour au menu</a></p>
    </body>
</html>
