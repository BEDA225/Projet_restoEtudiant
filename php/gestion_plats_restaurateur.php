<?php

session_start();
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'Restaurateur') {
    header("Location: /Projet_restoEtudiant/php/connexion.php");
    exit();
}

require_once  'db_connect.php';
$pdo = getPDO();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Menu - Restaurateur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h2>Gestion du Menu</h2>
        <!-- Navigation pour le restaurateur  qui sera gerer de facon dynamique par php -->
        <nav>
            <ul>
                <li><a href="../dashboard_restaurateur.php">Accueil</a></li>
                <li><a href="gestion_menu_restaurateur.php">Vos </a></li>
                <li><a href="php/deconnexion.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h3>plats disponibles</h3>
        <div id="formules-container">
            <!-- Les formules seront chargées ici via  Php -->
            <?php
                $stmt = $pdo->prepare("SELECT * FROM formule");
                $stmt->execute();
                $formules = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($formules as $plats) {
                    echo '<div class="carte-plat">';
                    if (!empty($plats['image'])) {
                        echo '<img src="uploads/' . htmlspecialchars($plats['image']) . '" alt="' . htmlspecialchars($plats['titre']) . '">';
                 }
                 echo '<div class="carte-contenu">';
                 echo '<h4>' . htmlspecialchars($plats['titre']) . '</h4>';
                 echo '<p>' . htmlspecialchars($plats['description']) . '</p>';
                 echo '<p>Prix : ' . number_format($plats['prix'], 2) . ' $</p>';
                 echo '<p>Date d\'ajout : ' . date('d/m/Y', strtotime($plats['date_ajout'])) . '</p>';
                 echo'</div>'; // fermeture de carte-contenu
                 echo'</div>'; // fermeture de cartee-plat
             }
            ?>
        </div>
        <h3>Gerer vos vos menu  </h3>
      <div class="gestion-menu">
          <form action="php/ajouter_formule.php" method="post" style="display:inline;">
              <button type="submit" id="btn-ajouter-formule">Ajouter</button>
          </form>
          <form action="php/modifier_formule.php" method="post" style="display:inline;">
              <button type="submit" id="btn-actualiser-formules">Modifier</button>
          </form>
          <form action="php/supprimer_formule.php" method="post" style="display:inline;">
              <button type="submit" id="btn-supprimer-formules">Supprimer</button>
          </form>
      </div>

        <!-- Formulaire d'ajout -->
        <div id="formule-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3 id="formule-modal-title">Ajouter un plat</h3>
                <form method="post" enctype="multipart/form-data" id="formule-form">
                    <label for="titre">Titre :</label>
                    <input type="text" id="titre" name="titre" required>

                    <label for="description">Description :</label>
                    <textarea id="description" name="description" cols="3" rows="2"></textarea>

                    <label for="prix">Prix :</label>
                    <input type="number" id="prix" name="prix" step="0.01" required>
                    <label for="image">Image :</label>
                    <input type="file" id="image" name="image" accept="image/*">

                    <button type="submit" id="btn-enregistrer-plat" name="btn-save">Enregistrer</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Resto Etudiant. Tous droits réservés.</p>
    </footer>


<!-- Script pour gérer l'affichage du modal et l'ajout de formules -->
<script>
    document.getElementById('formule-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche le rechargement de la page

        const formData = new FormData(this);
        fetch('php/ajouter_formule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Plat ajouté avec succès !');
                //recharger la liste des plats
                fetch('gestion_menu_restaurateur.php')
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('formules-container').innerHTML = doc.getElementById('formules-container').innerHTML;
                    });
            } else {
                alert('Erreur lors de l\'envoi du formulaire.');
            }
        });
    });
</script>

</body>
</html>
