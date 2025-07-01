<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>ESIEE-IT école d'ingénieurs et de l'expertise numérique</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/Site_FAQ/www/css/index.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<?php
$page_title = "ESIEE-IT école d'ingénieurs et de l'expertise numérique";
$css_file = "index.css";
include '../utils/header.php';
?>

  <main>
    <section class="intro">
      <h2>Bienvenue sur l'espace F.A.Q. de l'ESIEE-IT</h2>
      <p>
        description a remplir
      </p>
      <p>
        Sur cette page vous pourrez:
        <ul>
          <li>Poser vos questions</li>
          <li>Avoir réponses à vos questions</li>
          <li>Remonter des problèmes interne</li>
          <li>Contacter un PO ou un responsable</li>
        </ul>
      </p>
      <div class="button-group">
        <a href="/Site_FAQ/www/dev/messagerie.php" class="button">Poser une question</a>
      </div>
    </section>
  </main>
<?php include '../utils/footer.php'; ?>

</body>
</html>