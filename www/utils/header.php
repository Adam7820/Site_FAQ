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
        basename($_SERVER['PHP_SELF']) != 'signin.php'
      ): ?>
      <a href="/Site_FAQ/www/user/profile.php">Profil</a>
      <?php endif; ?>
    </nav>
  </header>