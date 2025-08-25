<?php
// Démarrage de la session et vérification du rôle utilisateur
session_start();
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'Restaurateur') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

// Connexion à la base de données
require_once './db_connect.php';
$pdo = getPDO();

// Récupération des formules du restaurateur connecté
$stmt = $pdo->prepare("SELECT * FROM formules WHERE utilisateur_id = ?");
$stmt->execute([$_SESSION['utilisateur_id']]);
$formules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des Formules</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <header>
        <h2>Gestion des Formules </h2>
        <!-- Lien pour ajouter une nouvelle formule -->
        <a href="ajouter_formule.php">Ajouter une nouvelle formule</a>
    </header>
    <main>
        <!-- Zone d'affichage des messages dynamiques (succes/erreur AJAX) -->
        <div id="message-flash" style="display:none;color:#00796b;background:#e0ffe0;padding:10px 15px;border-radius:5px;margin-bottom:20px;"></div>

        <div class="cartes-formules">
            <div class="cartes-formules-ajoute">
                <?php
                // Affichage des formules existantes ou message si aucune
                if (empty($formules)) {
                    echo '<p>Aucune formule ajoutée.</p>';
                } else {
                    echo '<h2>Vos formules ajoutées :</h2>';
                    foreach ($formules as $formule_ajoute) {
                        $id = (int)$formule_ajoute['id'];
                ?>
                        <div class="carte-formule" id="carte-formule-<?= $id ?>">
                            <!-- Formulaire de modification AJAX pour chaque formule -->
                            <form onsubmit="event.preventDefault(); modifierFormule(<?= $id ?>);">
                                <input type="text" id="nom-<?= $id ?>" value="<?= htmlspecialchars($formule_ajoute['nom']) ?>" required>
                                <input type="text" id="description-<?= $id ?>" value="<?= htmlspecialchars($formule_ajoute['description']) ?>" required>
                                <input type="number" id="prix-<?= $id ?>" value="<?= htmlspecialchars($formule_ajoute['prix']) ?>" required>
                                <input type="text" id="duree-<?= $id ?>" value="<?= htmlspecialchars($formule_ajoute['duree']) ?>" required>
                                <input type="file" id="image-<?= $id ?>" name="image">
                                <button type="submit">Enregistrer</button>
                            </form>
                            <!-- Affichage de l'image actuelle si elle existe -->
                            <?php if (!empty($formule_ajoute['image'])): ?>
                                <img src="../images/<?= htmlspecialchars($formule_ajoute['image']) ?>" alt="Image formule" style="max-width:100px;">
                            <?php endif; ?>
                            <!-- Boutons d'action -->
                            <button onclick="supprimerFormule(<?= $id ?>)">Supprimer</button>
                            <button onclick="modifierFormule(<?= $id ?>)">Modifier</button>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Inclusion du script JS pour la gestion AJAX des formules -->
    <script src="../controller/gestion-formules.js"></script>
</body>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Resto Etudiant</p>
</footer>

</html>