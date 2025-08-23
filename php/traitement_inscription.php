<?php
session_start();
require_once 'db_connect.php';
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../inscription.php');
    exit();
}

// Vérifier si le rôle est choisi
if (empty($_POST['role'])) {
    $_SESSION['old'] = $_POST;
    $_SESSION['old']['role'] = ''; // force la valeur vide si rien n'est choisi
    erreur_et_redirection("Vous devez choisir un rôle.", 'role');
}

$role = $_POST['role'];
$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$motdepasse = $_POST['motdepasse'] ?? '';
$motdepasse_confirm = $_POST['motdepasse_confirm'] ?? '';
$universite = $_POST['universite'] ?? '';
$annee_academique = $_POST['annee_academique'] ?? '';
$numeroEtudiant = $_POST['numeroEtudiant'] ?? '';
$code_etudiant = 'Etud' . $numeroEtudiant; // Génère un code étudiant unique
$cuisine = $_POST['cuisine'] ?? '';
$code_restaurateur = 'Rest' . $cuisine; // Génère un code restaurateur unique
$adresse = trim($_POST['adresse'] ?? '');

// Fonction centralisée d’erreur
function erreur_et_redirection($message, $focus)
{
    $_SESSION['erreurs'][$focus] = $message;
    $_SESSION['focus'] = $focus;
    $_SESSION['old'] = $_POST;
    header('Location: ../inscription.php');
    exit();
}

// Champs communs
$champs_communs = [
    'nom' => 'Le nom est requis.',
    'email' => "L'email est requis.",
    'telephone' => "Le numéro de téléphone est requis.",
    'motdepasse' => "Le mot de passe est requis.",
    'motdepasse_confirm' => "La confirmation du mot de passe est requise."
];
foreach ($champs_communs as $champ => $message) {
    if (empty($$champ)) {
        erreur_et_redirection($message, $champ);
    }
}

// Champs spécifiques étudiant
if (strtolower($role) === 'etudiant') {
    if (empty($universite)) {
        erreur_et_redirection("L'université est requise.", 'universite');
    }
    if (empty($annee_academique)) {
        erreur_et_redirection("L'année académique est requise.", 'annee_academique');
    }
    if (empty($numeroEtudiant)) {
        erreur_et_redirection("Le numéro étudiant est requis.", 'numeroEtudiant');
    }
}
// Champs spécifiques restaurateur
else if (strtolower($role) === 'restaurateur') {
    if (empty($cuisine)) {
        erreur_et_redirection("La cuisine est requise.", 'cuisine');
    }
    if (empty($adresse)) {
        erreur_et_redirection("L'adresse du restaurant est requise.", 'adresse');
    }
}

// Validation du nom
if (!empty($nom) && !preg_match('/^[a-zA-Z\s]+$/', $nom)) {
    erreur_et_redirection("Le nom ne doit contenir que des lettres et des espaces.", "nom");
}

// Validation de l'email
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    erreur_et_redirection("L'email est invalide.", "email");
}

// Validation du téléphone
if (!empty($telephone) && !preg_match('/^[0-9]{10}$/', $telephone)) {
    erreur_et_redirection("Le numéro de téléphone doit contenir 10 chiffres.", "telephone");
}

// Validation du mot de passe
if (!empty($motdepasse) && !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/', $motdepasse)) {
    erreur_et_redirection("Le mot de passe doit contenir au moins 8 caractères, dont une lettre majuscule, une lettre minuscule et un chiffre.", "motdepasse");
}
if (!empty($motdepasse_confirm) && !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/', $motdepasse_confirm)) {
    erreur_et_redirection("La confirmation du mot de passe doit contenir au moins 8 caractères, dont une lettre majuscule, une lettre minuscule et un chiffre.", "motdepasse_confirm");
}
// Validation de la confirmation du mot de passe
if (!empty($motdepasse) && !empty($motdepasse_confirm) && $motdepasse !== $motdepasse_confirm) {
    erreur_et_redirection("Les mots de passe ne correspondent pas.", "motdepasse");
}

// Vérifier que l'email n'existe pas
$stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    erreur_et_redirection("Email déjà utilisé", "email");
}

// Insertion dans utilisateur
$hash = password_hash($motdepasse, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO utilisateur (nom, email, telephone, motdepasse, role) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$nom, $email, $telephone, $hash, $role]);
$utilisateur_id = $pdo->lastInsertId();

// Insertion dans la table spécifique
if ($role === 'Etudiant') {
    $stmt = $pdo->prepare("INSERT INTO etudiants (utilisateur_id, universite, annee_academique, numeroEtudiant, code_etudiant) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$utilisateur_id, $universite, $annee_academique, $numeroEtudiant, $code_etudiant]);
} elseif ($role === 'Restaurateur') {
    $stmt = $pdo->prepare("INSERT INTO restaurateurs (utilisateur_id, adresse, cuisine, code_restaurateur) VALUES (?, ?, ?, ?)");
    $stmt->execute([$utilisateur_id, $adresse, $cuisine, $code_restaurateur]);
}

header('Location: /Projet_restoEtudiant/php/connexion.php');
exit();
