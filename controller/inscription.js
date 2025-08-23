// Vérification AJAX de l'email
function verifierEmail() {
    const emailInput = document.getElementById('email');
    const messageDiv = document.getElementById('message');
    const email = emailInput.value.trim();
    if (email.length < 5) {
        messageDiv.textContent = '';
        return;
    }
    fetch('api/verifier_email.php?email=' + encodeURIComponent(email))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                messageDiv.textContent = "Cet email est déjà utilisé.";
                messageDiv.style.color = 'red';
            } else {
                messageDiv.textContent = "Email disponible.";
                messageDiv.style.color = 'green';
            }
        })
        .catch(() => {
            messageDiv.textContent = '';
        });
}
function afficherChampsParRole() {
    const role = document.getElementById("role").value;
    const champsEtudiant = document.getElementById("ChampsEtudiant");
    const champsRestaurateur = document.getElementById("ChampsRestaurateur");

    // Desactive tous les champs requis
    //champsEtudiant.querySelectorAll("input, select").forEach(etl => etl.required = false);
    //champsRestaurateur.querySelectorAll("input, select").forEach(etl => etl.required = false);
    // placeholder style none au debut
    const nomInput = document.getElementById("nom");
    nomInput.placeholder = "";

    if (role === "Etudiant") {
        champsEtudiant.style.display = "block";
        champsRestaurateur.style.display = "none";
        // afficher un placeholder pour le nom
        const nomInput = document.getElementById("nom");
        nomInput.placeholder = "Votre nom complet";
        // Active les champs requis pour les étudiants
        //champsEtudiant.querySelectorAll("input, select").forEach(etl => etl.required = true);
    } else if (role === "Restaurateur") {
        champsEtudiant.style.display = "none";
        champsRestaurateur.style.display = "block";
        // affichier un placeholder pour l'adresse
        const nomInput = document.getElementById("nom");
        nomInput.placeholder = "Le nom du restaurant";
        // Active les champs requis pour les restaurateurs
        //champsRestaurateur.querySelectorAll("input, select").forEach(etl => etl.required = true);
    } else {
        champsEtudiant.style.display = "none";
        champsRestaurateur.style.display = "none";
    }
}

function verifierEmail() {
    const emailInput = document.getElementById('email');
    const messageDiv = document.getElementById('message');
    const email = emailInput.value.trim();
    if (email.length < 5) {
        messageDiv.textContent = '';
        return;
    }
    fetch('api/verifier_email.php?email=' + encodeURIComponent(email))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                messageDiv.textContent = "Cet email est déjà utilisé.";
                messageDiv.style.color = 'red';
            } else {
                messageDiv.textContent = "";

            }
        })
        .catch(() => {
            messageDiv.textContent = '';
        });
}


window.afficherChampsParRole = afficherChampsParRole;
// Initialisation unique au chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
    afficherChampsParRole();
    // Activation/désactivation des champs communs selon le rôle
    const champsCommuns = ['nom', 'email', 'telephone', 'motdepasse', 'motdepasse_confirm'];
    const roleSelect = document.getElementById('role');
    function activerChampsCommuns() {
        const activer = roleSelect.value !== '';
        champsCommuns.forEach(function (id) {
            document.getElementById(id).disabled = !activer;
        });
    }
    roleSelect.addEventListener('change', activerChampsCommuns);
    activerChampsCommuns();
});


document.addEventListener('DOMContentLoaded', function () {
    var roleSelect = document.getElementById('role');
    var errorDiv = document.querySelector('div[style*="color:red"]');
    if (roleSelect && errorDiv) {
        roleSelect.addEventListener('change', function () {
            if (roleSelect.value) {
                errorDiv.style.display = 'none';
            }
        });
    }
});

