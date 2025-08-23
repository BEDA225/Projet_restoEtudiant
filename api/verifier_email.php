<?php
// Script PHP pour vérifier si un email existe déjà (appelé en AJAX)
require_once '../php/db_connect.php';
$pdo = getPDO();

header('Content-Type: application/json');

if (isset($_GET['email'])) {
    $email = trim($_GET['email']);
    $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
} else {
    echo json_encode(['error' => 'Aucun email fourni']);
}
