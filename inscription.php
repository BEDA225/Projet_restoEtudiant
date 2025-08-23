<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - RestoEtudiant</title>
    <link rel="stylesheet" href="./Styles/styleInscription.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="controller/inscription.js"></script>
</head>

<body>
    <main class="inscription-section">
        <h2>Inscription</h2>

        <!-- Affichage des messages d'erreur sous chaque champ -->

        <p class="msg"><strong>Créer votre compte sur RestoEtudiant</strong></p>
        <form id="inscription-form" method="POST" action="php/traitement_inscription.php">
            <span id="message">Veuillez sélectionner un rôle. Avant de continuer, assurez-vous que tous les champs sont remplis correctement.</span>
            <div class="form-elt">
                <label for="role">Rôle :</label>
                <select name="role" id="role" onchange="afficherChampsParRole()">
                    <option value="" disabled <?php if (!isset($_SESSION['old']['role']) || $_SESSION['old']['role'] == '') echo 'selected'; ?>>--Choisir un rôle--</option>
                    <option value="Etudiant" <?php if (isset($_SESSION['old']['role']) && $_SESSION['old']['role'] == 'Etudiant') echo 'selected'; ?>>Etudiant</option>
                    <option value="Restaurateur" <?php if (isset($_SESSION['old']['role']) && $_SESSION['old']['role'] == 'Restaurateur') echo 'selected'; ?>>Restaurateur</option>
                </select>

            </div>

            <!-- Champs communs -->
            <div class="form-elt">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" autocomplete="name" <?php if (isset($_SESSION['old']['nom'])) echo 'value="' . htmlspecialchars($_SESSION['old']['nom']) . '"'; ?>>
                <?php if (isset($_SESSION['erreurs']['nom'])): ?>
                    <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['nom']; ?> </div>
                <?php endif; ?>
            </div>
            <div class="form-elt">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" autocomplete="email" onkeyup="verifierEmail()" <?php if (isset($_SESSION['old']['email'])) echo 'value="' . htmlspecialchars($_SESSION['old']['email']) . '"'; ?>>
                <?php if (isset($_SESSION['erreurs']['email'])): ?>
                    <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['email']; ?> </div>
                <?php endif; ?>
            </div>
            <div class="form-elt">
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone" autocomplete="tel" <?php if (isset($_SESSION['old']['telephone'])) echo 'value="' . htmlspecialchars($_SESSION['old']['telephone']) . '"'; ?>>
                <?php if (isset($_SESSION['erreurs']['telephone'])): ?>
                    <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['telephone']; ?> </div>
                <?php endif; ?>
            </div>

            <!-- Spécifique étudiant -->
            <div id="ChampsEtudiant" style="display: none;">
                <div class="form-elt">
                    <label for="universite">Université :</label>
                    <input type="text" id="universite" name="universite" <?php if (isset($_SESSION['old']['universite'])) echo 'value="' . htmlspecialchars($_SESSION['old']['universite']) . '"'; ?>>
                    <?php if (isset($_SESSION['erreurs']['universite'])): ?>
                        <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['universite']; ?> </div>
                    <?php endif; ?>
                </div>
                <div class="form-elt">
                    <label for="annee_academique">Année académique :</label>
                    <input type="text" id="annee_academique" name="annee_academique" placeholder="Exemple : 2024-2025" <?php if (isset($_SESSION['old']['annee_academique'])) echo 'value="' . htmlspecialchars($_SESSION['old']['annee_academique']) . '"'; ?>>
                    <?php if (isset($_SESSION['erreurs']['annee_academique'])): ?>
                        <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['annee_academique']; ?> </div>
                    <?php endif; ?>
                </div>
                <div class="form-elt">
                    <label for="numeroEtudiant">Numéro étudiant :</label>
                    <input type="text" id="numeroEtudiant" name="numeroEtudiant" <?php if (isset($_SESSION['old']['numeroEtudiant'])) echo 'value="' . htmlspecialchars($_SESSION['old']['numeroEtudiant']) . '"'; ?>>
                    <?php if (isset($_SESSION['erreurs']['numeroEtudiant'])): ?>
                        <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['numeroEtudiant']; ?> </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Spécifique restaurateur -->
            <div id="ChampsRestaurateur" style="display: none;">
                <div class="form-elt">
                    <label for="adresse">Adresse :</label>
                    <input type="text" id="adresse" name="adresse" <?php if (isset($_SESSION['old']['adresse'])) echo 'value="' . htmlspecialchars($_SESSION['old']['adresse']) . '"'; ?>>
                    <?php if (isset($_SESSION['erreurs']['adresse'])): ?>
                        <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['adresse']; ?> </div>
                    <?php endif; ?>
                </div>
                <div class="form-elt">
                    <label for="cuisine">Cuisine :</label>
                    <input type="text" id="cuisine" name="cuisine" <?php if (isset($_SESSION['old']['cuisine'])) echo 'value="' . htmlspecialchars($_SESSION['old']['cuisine']) . '"'; ?>>
                    <?php if (isset($_SESSION['erreurs']['cuisine'])): ?>
                        <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['cuisine']; ?> </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-elt">
                <label for="motdepasse">Mot de passe :</label>
                <input type="password" id="motdepasse" name="motdepasse" <?php if (isset($_SESSION['old']['motdepasse'])) echo 'value="' . htmlspecialchars($_SESSION['old']['motdepasse']) . '"'; ?>>
                <?php if (isset($_SESSION['erreurs']['motdepasse'])): ?>
                    <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['motdepasse']; ?> </div>
                <?php endif; ?>
            </div>
            <div class="form-elt">
                <label for="motdepasse_confirm">Confirmer le mot de passe :</label>
                <input type="password" id="motdepasse_confirm" name="motdepasse_confirm" <?php if (isset($_SESSION['old']['motdepasse_confirm'])) echo 'value="' . htmlspecialchars($_SESSION['old']['motdepasse_confirm']) . '"'; ?>>
                <?php if (isset($_SESSION['erreurs']['motdepasse_confirm'])): ?>
                    <div style="color:red; font-size:0.9em; margin-bottom:4px;"> <?php echo $_SESSION['erreurs']['motdepasse_confirm']; ?> </div>
                <?php endif; ?>
            </div>

            <div class="btn-container">
                <button type="submit" id="btn-inscription">
                    <span class="material-icons">person_add</span>S'inscrire
                </button>
            </div>
        </form>

        <?php unset($_SESSION['old'], $_SESSION['erreurs']); ?>

        <?php if (isset($_SESSION['focus'])): ?>
            <script>
                window.onload = function() {
                    var elt = document.getElementsByName('<?php echo $_SESSION['focus']; ?>')[0];
                    if (elt) elt.focus();
                };
            </script>
            <?php unset($_SESSION['focus']); ?>
        <?php endif; ?>

        <div id="message" style="color: red; margin-top: 10px;"></div>

        <div class="form-links">
            <a href="/Projet_restoEtudiant/php/connexion.php">Déjà inscrit ? Se connecter</a>
            <!-- Gerer la redirection vers la page de connexion avec php  a supprimer la basile <a>-->
        </div>
    </main>

</body>

</html>