<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un avis</title>
</head>
<body>

    <h1>Ajouter un avis</h1>

    <form method="POST" action="/api/avis">
        @csrf

        <label>Réservation ID</label><br>
        <input type="number" name="reservation_id"><br><br>

        <label>Auteur ID</label><br>
        <input type="number" name="auteur_id"><br><br>

        <label>Cible ID</label><br>
        <input type="number" name="cible_id"><br><br>

        <label>Note</label><br>
        <input type="number" name="note" min="1" max="5"><br><br>

        <label>Commentaire</label><br>
        <textarea name="commentaire"></textarea><br><br>

        <button type="submit">Envoyer</button>
    </form>

</body>
</html>