function supprimerFormule(id) {
    if (!confirm('Supprimer cette formule ?')) return;
    fetch('supprimer_formule.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: id
        })
    })
        .then(response => response.json())
        .then(data => {
            let message = document.getElementById('message-flash');
            message.textContent = data.message;
            message.style.display = 'block';
            if (data.success) {
                let carte = document.getElementById('carte-formule-' + id);
                if (carte) carte.remove();
            }
        });
}

function modifierFormule(id) {
    // Récupère les nouvelles valeurs depuis le formulaire ou la modale
    const nom = document.getElementById('nom-' + id).value;
    const description = document.getElementById('description-' + id).value;
    const prix = document.getElementById('prix-' + id).value;
    const duree = document.getElementById('duree-' + id).value;
    // Ajoute image si besoin

    fetch('modifier_formule.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            id: id,
            nom: nom,
            description: description,
            prix: prix,
            duree: duree
            // Ajoute image si besoin
        })
    })
    .then(response => response.json())
    .then(data => {
        // Affiche le message de succès/erreur
        document.getElementById('message-flash').textContent = data.message;
        document.getElementById('message-flash').style.display = 'block';
        if (data.success) {
            // Mets à jour l'affichage de la carte ou ferme la modale
        }
    });
}