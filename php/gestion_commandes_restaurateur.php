<div class="controls">
    <label>Filtrer par statut :
        <select id="filtre-statut">
            <option value="">Tous</option>
            <option value="en cours">En cours</option>
            <option value="validée">Validée</option>
            <option value="livrée">Livrée</option>
            <option value="annulée">Annulée</option>
        </select>
    </label>
    <p>Nombre de commandes : <span id="nb-cmd"><?= count($commandes) ?></span></p>
    <p>Revenu estimé : <strong><?= number_format($prix_total, 2) ?> $</strong></p>
</div>
<div id="liste-commandes">
    <?php foreach ($commandes as $c): ?>
        <div class="commande-card" data-id="<?= $c['commande_id'] ?>" data-statut="<?= $c['statut'] ?>">
            <p><strong>#<?= $c['commande_id'] ?></strong> – <?= htmlspecialchars($c['prenom'] . " " . $c['nom']) ?> – <?= htmlspecialchars($c['date_commande']) ?></p>
            <p><em><?= htmlspecialchars($c['details_formules']) ?></em></p>
            <p>Statut :
                <select class="select-statut">
                    <?php foreach (['en cours', 'validée', 'livrée', 'annulée'] as $s): ?>
                        <option value="<?= $s ?>" <?= $c['statut'] == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <button class="btn-maj-statut">Mettre à jour</button>
        </div>
    <?php endforeach; ?>
</div>


<script>
    window.role = "restaurateur";
    const autoRefreshInterval = 30000;

    function applyFiltre() {
        const val = document.getElementById('filtre-statut').value;
        document.querySelectorAll('.commande-card').forEach(card => {
            card.style.display = (!val || card.dataset.statut === val) ? '' : 'none';
        });
    }
    document.getElementById('filtre-statut').addEventListener('change', applyFiltre);

    function attachUpdateButtons() {
        document.querySelectorAll('.btn-maj-statut').forEach(btn => {
            btn.onclick = async () => {
                const card = btn.closest('.commande-card');
                const id = card.dataset.id,
                    statut = card.querySelector('.select-statut').value;
                const resp = await fetch('/Projet_restoEtudiant/php/maj_statut_commande.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        commande_id: id,
                        statut
                    })
                });
                const data = await resp.json();
                if (data.success) {
                    card.dataset.statut = statut;
                    alert('Statut mis à jour');
                    applyFiltre();
                } else {
                    alert('Erreur : ' + (data.message || ''));
                }
            };
        });
    }
    attachUpdateButtons();
    // auto-refresh des commandes
    setInterval(() => {
        fetch('/Projet_restoEtudiant/api/get_commandes.php')
            .then(r => r.json())
            .then(data => {
                if (data.commandes) {
                    const list = document.getElementById('liste-commandes');
                    list.innerHTML = '';
                    data.commandes.forEach(c => {
                        const div = document.createElement('div');
                        div.className = 'commande-card';
                        div.dataset.id = c.id;
                        div.dataset.statut = c.statut;
                        div.innerHTML = `<p><strong>#${c.id}</strong> – ${c.nomEtudiant} – ${c.date_commande}</p>
            <p><em>${c.plats.map(p=>`${p.nom} (x${p.quantite})`).join(', ')}</em></p>
            <p>Statut :
              <select class="select-statut">
                ${['en cours','validée','livrée','annulée'].map(s=>
                            `<option value="${s}" ${c.statut===s?'selected':''}>${s.charAt(0).toUpperCase()+s.slice(1)}</option>`).join('')}
              </select>
            </p>
            <button class="btn-maj-statut">Mettre à jour</button>`;
                        list.appendChild(div);
                    });
                    document.getElementById('nb-cmd').textContent = data.commandes.length;
                    attachUpdateButtons();
                    applyFiltre();
                }
            });
    }, autoRefreshInterval);
</script>