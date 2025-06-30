<?php
session_start();
// Vérifie si l'utilisateur n'est pas connecté
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Profil</title>
    <link rel="stylesheet" href="/www/user/profile.csse.css">
</head>
<?php
include "../sql/database.php";
$db = db_connect();

?>
<body>
<div class="container">
    <h1>Mon Profil</h1>
</div>
</body>
</html>