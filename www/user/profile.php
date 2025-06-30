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
$connect = mysqli_connect("localhost", "root", "", "coding_faq");

// Récupération des données de l'utilisateur connecté
$query = "SELECT * FROM users WHERE id_user = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userId']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$userRole = $row['role'];
$userMail = $row['email'];
$userPassword = $row['password'];
$userFName = $row['first_name'];
$userLName = $row['last_name'];
$userPromotion = $row['promotion'];

if (isset($_POST['passwordform'])) {
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];

    $errors = [];

    // Comparaison du mot de passe actuel avec celui stocké dans la BDD
    if (!empty($password)) {
        if (!password_verify($password, $userPassword)) {
            $errors[] = "L'ancien mot de passe est incorrect.";
        }
    }
    else {
        $errors[] = "L'ancien mot de passe est requis.";
    }

    // Vérification du nouveau mot de passe
    if (strlen($new_password) < 8) {
        $errors[] = 'Le nouveau mot de passe doit faire au moins 8 caractères.';
    }
    if (!preg_match("#[0-9]+#", $new_password)) {
        $errors[] = 'Le nouveau mot de passe doit contenir au moins un chiffre.';
    }
    if (!preg_match("#[\W]+#", $new_password)) {
        $errors[] = 'Le nouveau mot de passe doit contenir au moins un caractère spécial.';
    }

    // Si aucune erreur, modification du mot de passe dans la BDD
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stm = $connect->prepare('UPDATE users SET password = ? WHERE id_user = ?');
        $stm->bind_param('ss', $hashed_password, $_SESSION['userId']);
        $stm->execute();

        header("Location: profile.php");
        exit;
    } else {
        foreach ($errors as $error) {
            echo '<p>' . $error . '</p>' . '<br>';
        }
    }
}

?>
<body>
<h1>Mon Profil</h1>
<div class="container">
    <p> <strong>ROLE :</strong> <?php echo htmlspecialchars($userRole); ?> </p>
    <p> <strong>EMAIL :</strong> <?php echo htmlspecialchars($userMail); ?> </p>
    <p> <strong>NOM :</strong> <?php echo htmlspecialchars($userLName); ?> </p>
    <p> <strong>PRENOM :</strong> <?php echo htmlspecialchars($userFName); ?> </p>
    <?php if (!is_null($userPromotion)): ?>
    <p> <strong>PROMO :</strong> <?php echo htmlspecialchars($userPromotion); ?> </p>
    <?php endif; ?>

    <form method="post">
    <input type="password" name="password" placeholder="Ancien mot de passe"> <br>
    <input type="password" name="new_password" placeholder="Nouveau mot de passe"> <br>
    <button type="submit" name="passwordform">Modifier</button>
    </form>
</div>
</body>
</html>