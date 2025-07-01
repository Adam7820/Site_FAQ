<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['userId'])) {
    header("Location: profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connexion - ESIEE-IT</title>
    <link rel="stylesheet" href="/Site_FAQ/www/css/identification.css">
</head>
<?php
include "../../sql/database.php";
$db = db_connect();

if (isset($_POST['loginform'])) {
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Récupération de l'utilisateur dans la BDD
    $req = $db->prepare('SELECT id_user, password FROM users WHERE email = :email');
    $req->execute([':email' => $email]);
    $user = $req->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['id_user'];

        // Connexion réussie
        if (isset($_SESSION['userId'])) {
            header("Location: profile.php");
            exit;
        }
    } else {
        echo("le mot de passe ou l'email est incorrect");
    }
}
?>
<body>
<?php
include '../utils/header.php';
?>
<div class="container">
    <h1>Connexion</h1>
    <form method="post">
    <input type="email" name="email" placeholder="Email" required> <br>
    <input type="password" name="password" placeholder="Mot de passe" required> <br>
    <button type="submit" name="loginform">Se connecter</button>
        <a href="/Site_FAQ/www/user/signin.php">Je n'ai pas de compte</a>
    </form>
</div>
<?php
include '../utils/footer.php';
?>
</body>
</html>