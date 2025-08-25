<?php
session_start();
require_once 'db_connect.php';
$pdo = getPDO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $duree = $_POST['duree'];
    $utilisateur_id = $_SESSION['utilisateur_id'];
    $imagePath = null;

    // Vérification des champs
    if (empty($nom) || empty($description) || empty($prix) || empty($duree)) {
        echo "<p>Tous les champs doivent être remplis.</p>";
        exit();
    } elseif (!preg_match("/^[a-zA-Z\s]+$/u", $nom)) {
        echo "<p>Le nom de la formule est requis et ne doit contenir que des lettres et des espaces.</p>";
        exit();
    } elseif (!is_numeric($prix) || $prix <= 0) {
        echo "<p>Le prix doit être un nombre positif.</p>";
        exit();
    } elseif (!in_array($duree, ['1 semaine', '2 semaines', '1 mois'])) {
        echo "<p>La durée doit être sélectionnée.</p>";
        exit();
    }

    // Traitement de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $imagePath = 'uploads/' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $imagePath);
    }

    // Insertion dans la base de données
    $stmt = $pdo->prepare("INSERT INTO formule (utilisateur_id, nom, description, prix, duree, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$utilisateur_id, $nom, $description, $prix, $duree, $imagePath]);

    $_SESSION['message'] = "Formule ajoutée avec succès !";
    header("Location: gestion_formule_restaurateur.php");
    exit();
} else {
    echo "<p>Méthode de requête non supportée.</p>";
}
