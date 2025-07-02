<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Proposez une question</title>
    <link rel="stylesheet" href="../css/create_question.css">
</head>

<body>
<h2>💬 Proposez une question</h2>
<form action="process_commentaire.php" method="POST">
    <label for="commentaire">Votre question :</label><br>
    <textarea id="commentaire" name="commentaire" rows="5" cols="40" required></textarea><br><br>
    <button type="submit">Valider</button>
</form>
<p><a href="../dev/index.php" class="button">⬅️ Retour au menu</a></p>

</body>
</html>
