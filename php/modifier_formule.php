<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'Restaurateur') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

require_once 'db_connect.php';
require_once 'db_utils.php';
$pdo = getPDO();

// Utilisation de $_POST et $_FILES car on reçoit un FormData
$id = $_POST['id'] ?? null;
$nom = $_POST['nom'] ?? '';
$description = $_POST['description'] ?? '';
$prix = $_POST['prix'] ?? '';
$duree = $_POST['duree'] ?? '';

// Vérification des champs obligatoires
if (empty($id) || empty($nom) || empty($description) || empty($prix) || empty($duree)) {
    echo json_encode(['success' => false, 'message' => "Tous les champs sont obligatoires"]);
    exit;
}

// Vérifier que la formule appartient bien au restaurateur connecté
$stmt = $pdo->prepare("SELECT id FROM formules WHERE id = ? AND restaurateur_id = ?");
$stmt->execute([$id, $_SESSION['utilisateur_id']]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => "Formule introuvable ou accès refusé"]);
    exit;
}

// Gestion de l'image
$image_name = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_name = uniqid('formule_', true) . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
}

// Préparer les données à modifier
$donnees = [
    'nom' => $nom,
    'description' => $description,
    'prix' => $prix,
    'duree' => $duree,
    'image' => $image_name
];

if ($image_name) {
    $donnees['image'] = $image_name;
}

// Utiliser la fonction générique
$success = modifierParId($pdo, 'formules', $donnees, $id);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Formule modifiée avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
}