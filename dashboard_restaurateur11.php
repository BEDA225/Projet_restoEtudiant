<?php
session_start();
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'Restaurateur') {
  header("Location: /Projet_restoEtudiant/php/connexion.php");
  exit();
}
require_once __DIR__ . '/php/db_connect.php';
$pdo = getPDO();

// Récupérer les commandes du jour pour ce restaurateur
$stmt = $pdo->prepare("
    SELECT c.id AS commande_id, c.date_commande, c.statut,
           u.prenom, u.nom, u.email,
           GROUP_CONCAT(CONCAT(f.titre, ' (x', cf.quantite, ')') SEPARATOR ', ') AS details_formules,
           SUM(cf.quantite * f.prix) AS total_prix
    FROM commande c
    JOIN utilisateur u ON c.utilisateur_id = u.id
    JOIN commande_formule cf ON cf.commande_id = c.id
    JOIN formule f ON f.id = cf.formule_id
    WHERE f.utilisateur_id = :restaurateur_id
      AND DATE(c.date_commande) = CURDATE()
    GROUP BY c.id
    ORDER BY c.date_commande DESC
");
$stmt->execute(['restaurateur_id' => $_SESSION['user_id']]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$prix_total = array_sum(array_column($commandes, 'total_prix'));
