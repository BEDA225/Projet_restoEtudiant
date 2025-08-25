<?php

session_start();
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'Restaurateur') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

require_once './db_connect.php';
$pdo = getPDO();

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Formule</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h2>Ajouter une Formule</h2>
        <p>Veuillez remplir le formulaire ci-dessous pour ajouter un package de repas.</p>
    </header>
    <main>
        <form method="post" action="traitement_ajout_formule.php" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required>

            <label for="description">Description :</label>
            <input type="text" name="description" id="description" required>

            <label for="prix">Prix :</label>
            <input type="number" name="prix" id="prix" step="0.01" required>

            <label for="duree">Durée :</label>
            <select name="duree" id="duree" required>
                <option value="" disabled selected>--Sélectionner une durée--</option>
                <option value="1 semaine">1 semaine</option>
                <option value="2 semaines">2 semaines</option>
                <option value="1 mois">1 mois</option>
            </select>

            <label for="image">Ajouter une image :</label>
            <input type="file" name="image" id="image" accept="image/*">

            <button type="submit">Ajouter</button>
            <button type="reset">Réinitialiser</button>
        </form>
    </main>
</body>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Projet Resto Etudiant</p>
</footer>

</html>