// --- 1. CONFIGURAZIONE E VARIABILI ---
let studioMinutes = 25; 
let pausaMinutes = 5;
let timeLeft;
let timerId = null;
let isRunning = false;
let currentMode = 'studio'; 

// Elementi DOM - Timer
const timerDisplay = document.getElementById('timer-time');
const startBtn = document.getElementById('start-btn');
const stopBtn = document.getElementById('stop-btn');
const restartBtn = document.getElementById('restart-btn');
const btnStudio = document.getElementById('mode-studio');
const btnPausa = document.getElementById('mode-pausa');

// Elementi DOM - Navigazione & Banner Persistente
const openSidebar = document.getElementById('open-sidebar');
const closeSidebar = document.querySelector('.close-btn');
const sidebar = document.getElementById('sidebar-nav');
const customAlert = document.getElementById('custom-alert');
const alertMessage = document.getElementById('alert-message');

// Elementi DOM - Modal & Suggerimenti
const settingsBtn = document.getElementById('settings-trigger');
const modal = document.getElementById('custom-modal');
const inputMins = document.getElementById('new-minutes');
const saveModalBtn = document.getElementById('save-modal');
const closeModalBtn = document.getElementById('close-modal');
const suggestionBox = document.getElementById('suggestion-message');

// --- 2. FUNZIONI DI SERVIZIO ---

// Mostra il banner sotto l'header (rimane visibile finché non viene rimosso)
function showCustomAlert(message) {
    alertMessage.textContent = message;
    customAlert.classList.add('show');
}

// Nasconde il banner
function hideCustomAlert() {
    customAlert.classList.remove('show');
}

function setTimer(minutes) {
    clearInterval(timerId);
    isRunning = false;
    timeLeft = minutes * 60;
    updateDisplay();
}

function updateDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// --- 3. LOGICA DI FINE SESSIONE (CICLO AUTOMATICO) ---

function handleTimerComplete() {
    if (currentMode === 'studio') {
        // Aggiorna contatore sessioni
        hideCustomAlert();
        let sessionsCountSpan = document.getElementById('sessions-count'); 
        let currentSessions = parseInt(sessionsCountSpan.textContent) || 0;
        sessionsCountSpan.textContent = currentSessions + 1;

        // Sessioni Totali (nella stat-box in alto)
        let totalSessionsSpan = document.getElementById('total-sessions-count');
        if (totalSessionsSpan) {
            let totalSessions = parseInt(totalSessionsSpan.textContent) || 0;
            totalSessionsSpan.textContent = totalSessions + 1;
        }

        // --- 2. SALVATAGGIO SU SERVER (PHP SESSION) ---
        fetch('salva_dati.php?azione=studio');

        // Banner persistente per la pausa
        showCustomAlert("SESSIONE COMPLETATA! Prenditi una pausa");
        
        // Suggerimento sopra i giochini
        suggestionBox.textContent = "Ottimo lavoro! Che ne pensi di un giochino per svagarti?";
        suggestionBox.style.display = "block";

        // Switch automatico a Pausa
        currentMode = 'pausa';
        setTimer(pausaMinutes);
        btnPausa.classList.add('active');
        btnStudio.classList.remove('active');
    } else {
        // Fine Pausa: pulizia e ritorno allo studio
        let pauseCountSpan = document.getElementById('pause-count'); 
        if (pauseCountSpan) {
            let currentPause = parseInt(pauseCountSpan.textContent) || 0;
            pauseCountSpan.textContent = currentPause + 1;
        }

        fetch('salva_dati.php?azione=pausa');
        
        hideCustomAlert();
        suggestionBox.style.display = "none";
        showCustomAlert("LA PAUSA È FINITA! Si ricomincia con lo studio.");
        
        currentMode = 'studio';
        setTimer(studioMinutes);
        btnStudio.classList.add('active');
        btnPausa.classList.remove('active');
    }
}

// --- 4. GESTIONE EVENTI (TIMER & MODALITÀ) ---

btnStudio.addEventListener('click', () => {
    currentMode = 'studio';
    setTimer(studioMinutes);
    btnStudio.classList.add('active');
    btnPausa.classList.remove('active');
    suggestionBox.style.display = "none";
    hideCustomAlert();
});

btnPausa.addEventListener('click', () => {
    currentMode = 'pausa';
    setTimer(pausaMinutes);
    btnPausa.classList.add('active');
    btnStudio.classList.remove('active');
    hideCustomAlert();
});

startBtn.addEventListener('click', () => {
    if (isRunning) return;
    isRunning = true;
    hideCustomAlert(); // Rimuove eventuali avvisi quando il timer parte
    timerId = setInterval(() => {
        timeLeft--;
        updateDisplay();
        if (timeLeft <= 0) {
            clearInterval(timerId);
            isRunning = false;
            handleTimerComplete();
        }
    }, 1000);
});

stopBtn.addEventListener('click', () => {
    clearInterval(timerId);
    isRunning = false;
});

restartBtn.addEventListener('click', () => {
    setTimer(currentMode === 'studio' ? studioMinutes : pausaMinutes);
    hideCustomAlert();
});

// --- 5. SIDEBAR & MODAL ---

if (openSidebar) {
    openSidebar.addEventListener('click', () => sidebar.classList.add('open'));
}
if (closeSidebar) {
    closeSidebar.addEventListener('click', () => sidebar.classList.remove('open'));
}

if (settingsBtn) {
    settingsBtn.addEventListener('click', () => {
        modal.style.display = "block";
        inputMins.value = (currentMode === 'studio') ? studioMinutes : pausaMinutes;
    });
}

if (saveModalBtn) {
    saveModalBtn.addEventListener('click', () => {
        const val = parseInt(inputMins.value);
        if (val > 0) {
            if (currentMode === 'studio') studioMinutes = val;
            else pausaMinutes = val;
            if (!isRunning) setTimer(val);
            modal.style.display = "none";
        } else {
            showCustomAlert("Inserisci un numero valido!");
            setTimeout(hideCustomAlert, 3000);
        }
    });
}

if (closeModalBtn) {
    closeModalBtn.addEventListener('click', () => modal.style.display = "none");
}

window.addEventListener('click', (e) => {
    if (e.target == modal) modal.style.display = "none";
});

// --- 6. AVVIO AGGIORNATO ---
document.addEventListener('DOMContentLoaded', () => {
    // 1. Leggi i valori attuali dalle span (stampate dal PHP)
    const savedSessions = parseInt(document.getElementById('sessions-count').textContent) || 0;
    const savedPauses = parseInt(document.getElementById('pause-count').textContent) || 0;

    console.log("Dati recuperati dalla sessione PHP:", { savedSessions, savedPauses });

    // 2. Imposta il timer iniziale
    setTimer(studioMinutes); 
    
    // 3. Attiva il bottone grafico dello studio
    if (btnStudio) btnStudio.classList.add('active');
    
    // 4. Se c'è un messaggio di suggerimento residuo, nascondilo all'avvio
    if (suggestionBox) suggestionBox.style.display = "none";
});