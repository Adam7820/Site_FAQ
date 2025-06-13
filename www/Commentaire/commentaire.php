<!DOCTYPE html>

    <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Envoyer une question</title>
        </head>

    <body>
        <h2>Proposer une question</h2>
        <form action="process_commentaire.php" method="POST">
            <textarea name="commentaire" rows="5" cols="60" required></textarea><br><br>
            <button type="submit">Valider</button>
        </form>
    </body>

</html>
