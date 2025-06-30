<?php
require 'sign in verification.php';
session_start();
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
    <title>Inscription</title>
    <link rel="stylesheet" href="/www/user/login.cssn.css">
</head>
<?php
include "../sql/database.php";
$db = db_connect();

if (isset($_POST['signinform'])) {
    // Récupération des données du formulaire
    $role = $_POST['role'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmed_password = $_POST['confirmed_password'];

    // Stockage des erreurs
    $errors = [];

    // En cas d'email incorrect
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Le format de votre email est incorrect";
    } else if (!preg_match("/@(edu\.)?esiee-it\.fr$/", $email)) {
        $errors[] = "Le domaine de votre email n'est pas valide";
    }

    // En cas d'email déjà existant
    $req = $db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $req->execute([$email]);
    if ($req->fetchColumn() > 0) {
        $errors[] = 'Cet email est déjà utilisé.';
    }

    // Vérification du mot de passe
    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit faire au moins 8 caractères.';
    }
    if (!preg_match("#[0-9]+#", $password)) {
        $errors[] = 'Le mot de passe doit contenir au moins un chiffre.';
    }
    if (!preg_match("#[\W]+#", $password)) {
        $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
    }
    if ($password !== $confirmed_password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    // Si aucune erreur, sauvegarde des données de l'utilisateur + envoie mail
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_code = rand(100000, 999999);

        $_SESSION['pending_user'] = [
                'role' => $role,
                'last_name' => $last_name,
                'first_name' => $first_name,
                'email' => $email,
                'password' => $hashed_password,
                'code' => $verification_code
        ];

        $result = sendVerification($email, $verification_code);
        if ($result !== true) {
            echo $result;
        }
        $step = "verify";
    } else {
        foreach ($errors as $error) {
            echo '<p>' . $error . '</p>' . '<br>';
        }
    }
}

if (isset($_POST['verify_form'])) {
    $entered_code = $_POST['code'];
    $pending = $_SESSION['pending_user'] ?? null;

    if ($pending && $entered_code == $pending['code']) {
        $req = $db->prepare('INSERT INTO users (role, last_name, first_name, email, password) VALUES (?, ?, ?, ?, ?)');
        $req->execute([
                $pending['role'],
                $pending['last_name'],
                $pending['first_name'],
                $pending['email'],
                $pending['password'],
        ]);

        unset($_SESSION['pending_user']);
        header("Location: login.php");
        exit;
    }
    else {
        $errors[] = "Code incorrect.";
        echo 'Le code est incorrect';
        $step = "verify";
    }
}
?>
<body>
<div class="container">
    <h1>Inscription</h1>
    <?php if (!isset($step) || $step !== 'verify'): ?>
    <form method="post">
        <select name="role" required>
            <option value="" disabled selected>Qui suis-je ?</option>
            <option value="Élève">Élève</option>
            <option value="Product Owner">Product Owner</option>
            <option value="Responsable">Responsable</option>
        </select> <br>
        <input type="text" name="last_name" placeholder="Nom" required> <br>
        <input type="text" name="first_name" placeholder="Prénom" required> <br>
        <input type="email" name="email" placeholder="Email" required> <br>
        <input type="password" name="password" placeholder="Mot de passe" required> <br>
        <input type="password" name="confirmed_password" placeholder="Confirmer mot de passe" required> <br>
        <button type="submit" name="signinform">S'inscrire</button>
    </form>
    <a href="login.php">J'ai déjà un compte</a>
    <?php else: ?>
    <form method="post">
        <label for="code">Entrez le code de vérification qui vous a été envoyé par mail :</label><br>
        <input type="text" name="code" required><br>
        <button type="submit" name="verify_form">Vérifier</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
