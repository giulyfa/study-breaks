let attivitaCorrente = { id: 0, nome: '', tipo: '', durata: 0 };
let tempoInizioAttivita = 0;

function apriAttivita(id, slug, titolo, tipo, durata) {
    const modal = document.getElementById('activity-modal');
    const iframe = document.getElementById('game-frame');
    
    attivitaCorrente.id = id;
    attivitaCorrente.nome = titolo;
    attivitaCorrente.tipo = tipo;
    attivitaCorrente.durata = durata;
    
    iframe.src = 'activity_player.php?name=' + slug;
    
    modal.style.display = 'block';
    tempoInizioAttivita = Date.now();
}

function chiudiAttivita() {
    const modal = document.getElementById('activity-modal');
    const iframe = document.getElementById('game-frame');

    modal.style.display = 'none';
    iframe.src = 'about:blank';
}

function togglePlaylist() {
    const overlay = document.getElementById('playlist-selector-overlay');
    overlay.style.display = (overlay.style.display === 'none' || overlay.style.display === '') ? 'block' : 'none';
}

function registraAscolto(event, id) {
    const url = `salva_dati.php?azione=log_playlist&id_p=${id}`;
    
    if (navigator.sendBeacon) {
        navigator.sendBeacon(url);
    } else {
        fetch(url);
    }
    
    return true; 
}

window.onclick = function(event) {
    const activityModal = document.getElementById('activity-modal');
    const playlistOverlay = document.getElementById('playlist-overlay');

    if (event.target == activityModal) {
        chiudiAttivita();
    }
    
    if (event.target == playlistOverlay) {
        togglePlaylist();
    }
}
