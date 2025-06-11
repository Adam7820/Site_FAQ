<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connexion</title>
    <link rel="stylesheet" href="/www/login.css">
</head>
<body>
<?php
include "../sql/database.php";
$db = db_connect();

if (isset($_POST['loginform'])) {
    // Récupération des données du formumaire
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Récupération 
    $req = $db->prepare('SELECT id_user, password FROM users WHERE email = :email');
    $req->execute([':email' => $email]);
    $user = $req->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['id_user'];
        
        if (isset($_SESSION['userId'])) {
            header("Location: /");
        }
    }
}
?>

<div class="container">
    <h1>Connexion</h1>
    <form action="">
    <input type="email" name="email" placeholder="Email"> <br>
    <input type="password" name="password" placeholder="Mot de passe"> <br>
    <button type="submit" name="loginform">Se connecter</button>
    </form>
    <a href="sign%20in.php">Je n'ai pas de compte</a>
</div>
</body>
</html>