<?php
session_start(); // Démarre la session utilisateur
header('Content-Type: application/json'); // Définit le type de réponse en JSON

// Vérifie que l'utilisateur est connecté et a le rôle restaurateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurateur') {
    http_response_code(403); // Renvoie un code interdit
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once 'db_connect.php'; // Inclut le fichier de connexion à la base
$pdo = getPDO(); // Récupère l'objet PDO

// Vérifie la présence des champs requis dans le formulaire
if (!isset($_POST['titre'], $_POST['description'], $_POST['prix'], $_POST['cuisine'])) {
    echo json_encode(['success' => false, 'message' => 'Champs requis manquants']);
    exit;
}

// Gestion de l'image uploadée
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/'; // Dossier de destination des images
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // Crée le dossier si inexistant
    $filename = uniqid() . '-' . basename($_FILES['image']['name']); // Nom unique du fichier
    $imagePath = $uploadDir . $filename; // Chemin complet du fichier
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath); // Déplace le fichier uploadé
}

// Prépare et exécute la requête d'insertion en base
$stmt = $pdo->prepare("INSERT INTO formule (titre, description, prix, cuisine, image, date_ajout) VALUES (?, ?, ?, ?, ?, NOW())");
$success = $stmt->execute([
    $_POST['titre'],
    $_POST['description'],
    (float) $_POST['prix'],
    $_POST['cuisine'],
    $imagePath
]);

echo json_encode(['success' => $success]); // Renvoie le résultat en JSON