<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inscription</title>
    <link rel="stylesheet" href="/www/login.css">
</head>
<body>
<?php
if (isset($_POST['signinform'])) {
    // Récupération des données du formumaire
    $email = $_POST['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (preg_match("/@(edu\.)?esiee-it\.fr$/", $email)) {

        }
    }
}
?>

<div class="container">
    <h1>Inscription</h1>
    <form action="login.php">
        <input type="email" name="email" placeholder="Email"> <br>
        <button type="submit" name="signinform">S'inscrire</button>
    </form>
    <a href="login.php">J'ai déjà un compte</a>
</div>
</body>
</html>
