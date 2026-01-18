function eliminaElemento(id, tipo) {
    if (confirm("Sei sicuro di voler eliminare definitivamente questo elemento?")) {
        fetch(`dati_admin.php?azione=elimina&id=${id}&tipo=${tipo}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload(); 
                } else {
                    alert("Errore durante l'eliminazione.");
                }
            });
    }
}

function apriModifica(dati) {
    document.getElementById('edit-id').value = dati.id;
    document.getElementById('edit-titolo').value = dati.titolo;
    document.getElementById('edit-tipo').value = dati.tipo;
    document.getElementById('edit-durata').value = dati.durata;
    document.getElementById('edit-stato').value = dati.stato;

    const campiDaBloccare = ['edit-titolo', 'edit-tipo', 'edit-durata'];
    campiDaBloccare.forEach(id => {
        const el = document.getElementById(id);
        el.readOnly = true;
        if(el.tagName === 'SELECT') el.style.pointerEvents = 'none'; 
        el.style.background = "#f0f0f0";
    });

    document.getElementById('modal-modifica').style.display = 'block';
}

function apriNuovaAttivita() {
    document.getElementById('edit-id').value = '';
    document.getElementById('edit-titolo').value = '';
    document.getElementById('edit-tipo').value = 'gioco';
    document.getElementById('edit-durata').value = '';
    document.getElementById('edit-stato').value = 'disattivata';

    const campiDaSbloccare = ['edit-titolo', 'edit-tipo', 'edit-durata'];
    campiDaSbloccare.forEach(id => {
        const el = document.getElementById(id);
        el.readOnly = false;
        if(el.tagName === 'SELECT') el.style.pointerEvents = 'auto';
        el.style.background = "#fff";
    });

    document.querySelector('#modal-modifica h3').innerText = "Aggiungi Nuova Attività";
    document.getElementById('modal-modifica').style.display = 'block';
}

function apriModificaPlaylist(dati) {
    document.getElementById('edit-pl-id').value = dati.id;
    const titoloInput = document.getElementById('edit-pl-titolo');
    titoloInput.value = dati.titolo;
    const urlInput = document.getElementById('edit-pl-url');
    urlInput.value = dati.url_spotify || '';
    
    urlInput.readOnly = true; 
    titoloInput.readOnly = true;
    titoloInput.style.background = "#f0f0f0";
    urlInput.style.background = "#f0f0f0";

    document.getElementById('edit-pl-attiva').value = dati.attiva;
    document.getElementById('playlist-overlay').style.display = 'block';
}

function apriNuovaPlaylist() {
    document.getElementById('edit-pl-id').value = ''; 
    const titoloInput = document.getElementById('edit-pl-titolo');
    titoloInput.value = '';
    titoloInput.readOnly = false;
    document.getElementById('edit-pl-attiva').value = '1'; 

    const urlInput = document.getElementById('edit-pl-url');
    urlInput.value = '';
    urlInput.readOnly = false;
    urlInput.style.background = "#fff";
    
    document.querySelector('#playlist-overlay h3').innerText = "Aggiungi Nuova Playlist";
    document.getElementById('playlist-overlay').style.display = 'block';
}

function chiudiModale(id) {
    document.getElementById(id).style.display = 'none';
}
