<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Messagerie - ESIEE-IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="messagerie.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

  <header>
    <h1>ESIEE-IT école d'ingénieurs et de l'expertise numérique</h1>
    <nav>
      <a href="index.php">Accueil</a>
      <a href="connexion.php">Profil</a>
    </nav>
  </header>

  <main>
    <div class="messagerie-header">
      <h1>Messagerie</h1>
      <p>Un endroit pour pouvoir contacter un PO ou un résponsable</p>
    </div>

    <div class="form-container">
      <form id="messageForm">
        <div class="form-group">
          <label for="email">Adresse e-mail :</label>
          <input type="email" id="email" name="email" placeholder="votre.email@esiee-it.fr" required>
        </div>

        <div class="form-group">
          <label for="objet">Objet :</label>
          <input type="text" id="objet" name="objet" placeholder="Sujet de votre message" required>
        </div>

        <div class="form-group">
          <label for="contenu">Contenu du message :</label>
          <textarea id="contenu" name="contenu" placeholder="Décrivez votre demande, question ou problème en détail..." required></textarea>
        </div>

        <div class="button-group">
          <a href="index.php" class="button back-button">Retour à l'accueil</a>
          <button type="submit" class="button">Envoyer le message</button>
        </div>
      </form>
    </div>
  </main>
 
  <footer>
    <p>&copy; 2025 ESIEE-IT - L'école d'ingénieurs et de l'expertise numérique</p>
    <p><a href="#">Mentions légales</a> | <a href="#">Politique de confidentialité</a> | <a href="#">Crédits photos</a></p>
  </footer>

  <script>
    document.getElementById('messageForm').addEventListener('submit', function(e) {
      e.preventDefault();
      alert('Fonctionnalité d\'envoi en cours de développement');
    });
  </script>

</body>
</html>