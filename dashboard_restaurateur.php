<?php
session_start();
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'Restaurateur') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

require_once  './php/db_connect.php';
$pdo = getPDO();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial‑scale=1.0" />
    <title>Tableau de Bord Restaurateur</title>
    <link rel="stylesheet" href="./styles.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>

<body>
    <header>
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?></h2>
    </header>
    <main>
        <div class="leftbar">
            <h1 class="logo">RestoEtudiant</h1>
            <div class="leftbar-menu">
                <a href="#dashboard-stat">Accueil</a>
                <a href="php/gestion_plats_restaurateur.php">Gestion des plats</a>
                <a href="php/gestion_formule_restaurateur.php">Gestion des Formules</a>
                <a href="php/gestion_commandes_restaurateur.php">Gestion des Commandes</a>
                <a href="php/statistiques_restaurateur.php">Statistiques</a>
                <a href="php/profil_restaurateur.php">Profil</a>
                <a href="php/deconnexion.php">Déconnexion</a>
            </div>
        </div>

        <div class="main">
            <section class="dashboard-stat" id="dashboard-stat">
                <h2>Vue d'ensemble de votre activité</h2>

                <div class="stat-card">
                    <h3>Statistiques</h3>
                    <span class="material-icons">shopping_cart</span>
                    <div class="stat-titre">Commandes du jour</div>
                    <div class="stat-valeur">
                        <?php
                        $stmt = $pdo->prepare("
                                SELECT COUNT(*) FROM commande
                                WHERE utilisateur_id = :utilisateur_id
                                AND DATE(date_commande) = CURDATE()
                            ");
                        $stmt->execute(['utilisateur_id' => $_SESSION['utilisateur_id']]);
                        $commandes = $stmt->fetchColumn();
                        echo $commandes;
                        ?>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Chiffres d'affaires du jour</h3>
                    <span class="material-icons">attach_money</span>
                    <div class="stat-titre">Revenu du jour</div>
                    <div class="stat-valeur">
                        <?php
                        $prix_total = 0.00;
                        $stmt = $pdo->prepare("
                            SELECT SUM(cf.quantite * f.prix) AS total
                            FROM commande c
                            JOIN commande_formule cf ON c.id = cf.commande_id
                            JOIN formule f ON cf.formule_id = f.id
                            WHERE f.utilisateur_id = :restaurateur_id
                            AND DATE(c.date_commande) = CURDATE()
                        ");
                        $stmt->execute(['restaurateur_id' => $_SESSION['utilisateur_id']]);
                        $prix_total = $stmt->fetchColumn() ?: 0;
                        echo number_format($prix_total, 2) . " $";
                        ?>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Les plats les plus vendus </h3>
                    <span class="material-icons">restaurant_menu</span>
                    <div class="stat-titre">Plats populaires</div>
                    <div class="stat-valeur">

                        <!-- Liste des plats populaires gerer avec php pour que sa soit uniquement
                     les plats du restaurautareur connect et non tous les plats -->
                        <?php
                        // doit prendre en compte tous les plats du restaurateur depuis son inscription
                        // a aujourd'hui 

                        ?>

                    </div>
                </div>

            </section>

            <section class="tous-les-plats">
                <h2>Tous les plats</h2>
                <div class="plat-list">
                    <?php
                    // récupérer tous les plats du restaurateur
                    $stmt = $pdo->prepare("
                        SELECT f.id, f.titre, f.prix
                        FROM formule f
                        JOIN utilisateur u ON f.utilisateur_id = u.id
                        WHERE u.id = :utilisateur_id
                    ");
                    $stmt->execute(['utilisateur_id' => $_SESSION['utilisateur_id']]);
                    $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($plats as $plat) {
                        echo "<div class='plat-item'>";
                        echo "<h3>" . htmlspecialchars($plat['titre']) . "</h3>";
                        echo "<p>Prix: " . number_format($plat['prix'], 2) . " $</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </section>

        </div>

    </main>

</body>

</html>