<?php
// Affichage des erreurs pour le debug (à retirer en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration de la session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false, // à mettre true si HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

require_once __DIR__ . '/db_connect.php';
$pdo = getPDO();

// Récupération des données du formulaire
$email = $_POST['email'] ?? '';
$motdepasse = $_POST['motdepasse'] ?? '';
$role = $_POST['role'] ?? 'Etudiant';
// Harmonise la casse du rôle pour correspondre à la base
$role = ucfirst(strtolower($role));

// Recherche de l'utilisateur
$sql = "SELECT * FROM utilisateur WHERE email = :email AND role = :role LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':role', $role);
$stmt->execute();
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    $_SESSION['login_error'] = true;
    $_SESSION['debug'] = "Aucun utilisateur trouvé avec cet email et ce rôle.";
    $role_url = urlencode($role);
    header("Location: /Projet_restoEtudiant/php/connexion.php?role={$role_url}&error=1");
    exit();
}

// Vérification du mot de passe
// Juste avant la redirection, ajoute :
if (password_verify($motdepasse, $utilisateur['motdepasse'])) {
    $_SESSION['utilisateur_id'] = $utilisateur['id'];
    $_SESSION['nom'] = $utilisateur['nom'] ?? '';
    $_SESSION['email'] = $utilisateur['email'];
    $_SESSION['role'] = $utilisateur['role'];

    // Redirection selon rôle
    if ($_SESSION['role'] === 'Etudiant') {
        header("Location: /Projet_restoEtudiant/dashboard_gestion_formules.php");
        exit();
    } elseif ($_SESSION['role'] === 'Restaurateur') {
        header("Location: /Projet_restoEtudiant/dashboard_restaurateur.php");
        exit();
    } else {
        header("Location: /Projet_restoEtudiant/php/connexion.php?error=role_invalide");
        exit();
    }
} else {
    // Échec de connexion (mauvais mot de passe)
    $_SESSION['login_error'] = true;
    $_SESSION['debug'] = "Mot de passe incorrect.";
    $role_url = urlencode($role);
    header("Location: /Projet_restoEtudiant/php/connexion.php?role={$role_url}&error=1");
    exit();
}
