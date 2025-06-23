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
    <link rel="stylesheet" href="/www/profile.css">
</head>
<?php
include "../sql/database.php";
$connect = mysqli_connect("localhost", "root", "", "coding_faq");


$query = "SELECT * FROM users WHERE id_user = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userId']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$userRole = $row['role'];
$userMail = $row['email'];
$userFName = $row['first_name'];
$userLName = $row['last_name'];
$userPromotion = $row['promotion'];

?>
<body>
<h1>Mon Profil</h1>
<div class="container">

</div>
</body>
</html>