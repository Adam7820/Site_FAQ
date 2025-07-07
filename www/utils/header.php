<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isResponsable = false;
$isAdmin = false;

if (isset($_SESSION['userId'])) {
    $connect = mysqli_connect("localhost", "root", "", "coding_faq");
    $userId = $_SESSION['userId'];
    $query = "SELECT role FROM users WHERE id_user = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $role = 'Responsable';

    if ($user && $user['role'] == $role) {
        $isResponsable = true;
    }

    $query1 = "SELECT admin FROM users WHERE id_user = ?";
    $stmt1 = mysqli_prepare($connect, $query1);
    mysqli_stmt_bind_param($stmt1, 'i', $userId);
    mysqli_stmt_execute($stmt1);
    $result1 = mysqli_stmt_get_result($stmt1);
    $user1 = mysqli_fetch_assoc($result1);

    if ($user1 && $user1['admin'] == 1) {
        $isAdmin = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?php echo isset($page_title) ? $page_title : 'ESIEE-IT école d\'ingénieurs et de l\'expertise numérique'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo isset($css_file) ? $css_file : 'index.css'; ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

  <header>
    <h1>ESIEE-IT école d'ingénieurs et de l'expertise numérique</h1>
    <nav>
      <?php if (basename($_SERVER['PHP_SELF']) != 'index.php'): ?>
        <a href="/Site_FAQ/www/dev/index.php">Accueil</a>
      <?php endif; ?>
      
      <?php if (basename($_SERVER['PHP_SELF']) != 'messagerie.php'): ?>
        <a href="/Site_FAQ/www/dev/messagerie.php">Messagerie</a>
      <?php endif; ?>

      <?php if (
        basename($_SERVER['PHP_SELF']) != 'login.php' &&
        basename($_SERVER['PHP_SELF']) != 'signin.php' &&
        basename($_SERVER['PHP_SELF']) != 'profile.php'
      ): ?>
      <a href="/Site_FAQ/www/user/profile.php">Profil</a>
      <?php endif; ?>

      <?php if ((basename($_SERVER['PHP_SELF']) != 'admin.php') && $isResponsable): ?>
          <a href="/Site_FAQ/www/Creat question and validation/page/admin_mots_interdits.php">Mot interdits</a>
      <?php endif; ?>

      <?php if ((basename($_SERVER['PHP_SELF']) != 'admin.php') && $isResponsable): ?>
        <a href="/Site_FAQ/www/user/admin.php">Administration</a>
      <?php endif; ?>

      <?php if ((basename($_SERVER['PHP_SELF']) != 'admin.php') && $isResponsable): ?>
          <a href="/Site_FAQ/www/Creat question and validation/page/admin_signalements.php">Signalements</a>
      <?php endif; ?>

      <?php if ((basename($_SERVER['PHP_SELF']) != 'respondable.php') && $isAdmin || $isResponsable): ?>
          <a href="/Site_FAQ/www/Creat question and validation/page/responsable.php">Questions en attente</a>
      <?php endif; ?>

      <?php if (isset($_SESSION['userId'])): ?>
        <a href="/Site_FAQ/www/user/logout.php">Déconnexion</a>
      <?php endif; ?>
    </nav>
  </header>