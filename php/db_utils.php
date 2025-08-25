<?php
function supprimerParId(PDO $pdo, string $table, $id, string $id_field = 'id')
{
    // Liste blanche des tables autorisées
    $tables_autorisees = ['formules', 'utilisateur', 'etudiants', 'restaurateurs', 'commandes', 'commandes_formules', 'commandes_plats'];
    if (!in_array($table, $tables_autorisees) || empty($id)) {
        return false;
    }

    // Si $id est un tableau, suppression multiple
    if (is_array($id)) {
        $placeholders = implode(',', array_fill(0, count($id), '?'));
        $sql = "DELETE FROM `$table` WHERE `$id_field` IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($id);
    } else {
        // Suppression simple
        $sql = "DELETE FROM `$table` WHERE `$id_field` = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}

// methode pour modifier une entrée dans une table
function modifierParId(PDO $pdo, string $table, array $data, $id, string $id_field = 'id')
{
    // Liste blanche des tables autorisées
    $tables_autorisees = ['formules', 'utilisateur', 'etudiants', 'restaurateurs', 'commandes', 'commandes_formules', 'commandes_plats'];
    if (!in_array($table, $tables_autorisees) || empty($data)) {
        return false;
    }
    // Retirer les champs vides du tableau de données
    //$val = valeurs de la table
    $data = array_filter($data, fn($val) => $val !== null);

    if (empty($data)) {
        return false;
    }

    $champs = array_keys($data);
    $set_clause = implode(", ", array_map(fn($champ) => "`$champ` = ?", $champs));
    $sql = "UPDATE `$table` SET $set_clause WHERE `$id_field` = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([...array_values($data), $id]);
}
