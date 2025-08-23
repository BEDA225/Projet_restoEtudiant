<?php
// confirmation.php : activation du compte utilisateur via un lien reçu par email
require_once 'php/db_connect.php';
$pdo = getPDO();

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    // Chercher l'utilisateur avec ce token
    $stmt = $pdo->prepare("SELECT id, email, statut FROM utilisateur WHERE token_confirmation = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) {
        if ($user['statut'] === 'actif') {
            echo "Votre compte est déjà activé.";
        } else {
            // Activer le compte
            $stmt = $pdo->prepare("UPDATE utilisateur SET statut = 'actif', token_confirmation = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);
            echo "Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.";
        }
    } else {
        echo "Lien de confirmation invalide ou expiré.";
    }
} else {
    echo "Aucun token fourni.";
}
