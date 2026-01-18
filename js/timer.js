let studioMinutes = typeof minutiSalvati !== 'undefined' ? minutiSalvati : 25; 
let pausaMinutes = typeof pausaSalvata !== 'undefined' ? pausaSalvata : 5; 
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
const customAlert = document.getElementById('custom-alert');
const alertMessage = document.getElementById('alert-message');

// Elementi - Modal & Suggerimenti
const settingsBtn = document.getElementById('settings-trigger');
const modal = document.getElementById('custom-modal');
const inputMins = document.getElementById('new-minutes');
const saveModalBtn = document.getElementById('save-modal');
const closeModalBtn = document.getElementById('close-modal');
const suggestionBox = document.getElementById('suggestion-message');

// --- FUNZIONI DI SERVIZIO ---

function showCustomAlert(message) {
    alertMessage.textContent = message;
    customAlert.classList.add('show');
}

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

// --- LOGICA DI FINE SESSIONE ---

function handleTimerComplete() {
    if (currentMode === 'studio') {
        hideCustomAlert();
        
        let sessionsCountSpan = document.getElementById('sessions-count'); 
        let currentSessions = parseInt(sessionsCountSpan.textContent) || 0;
        sessionsCountSpan.textContent = currentSessions + 1;

        let streakCountSpan = document.getElementById('streak-count');
        if (streakCountSpan) {
            let currentStreak = parseInt(streakCountSpan.textContent) || 0;
            streakCountSpan.textContent = currentStreak + 1;
        }

        fetch(`salva_dati.php?azione=studio&durata=${studioMinutes}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let streakCountSpan = document.getElementById('streak-count');
                    if (streakCountSpan) {
                        streakCountSpan.textContent = data.nuova_streak;
                    }
                    console.log("Database sincronizzato. Streak attuale:", data.nuova_streak);
                }
            })
            .catch(error => console.error("Errore nel salvataggio:", error));
            
        
        showCustomAlert("SESSIONE COMPLETATA! Prenditi una pausa");
        suggestionBox.textContent = "Ottimo lavoro! Che ne pensi di un giochino per svagarti?";
        suggestionBox.style.display = "block";

        currentMode = 'pausa';
        setTimer(pausaMinutes);
        btnPausa.classList.add('active');
        btnStudio.classList.remove('active');
    } else {
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

// --- GESTIONE EVENTI ---

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
    hideCustomAlert();
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

// --- MODAL ---

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
            if (currentMode === 'studio') {
                studioMinutes = val;
                fetch('salva_dati.php?azione=set_timer&minuti=' + val + '&tipo=studio');
            } else {
                pausaMinutes = val;
                fetch('salva_dati.php?azione=set_timer&minuti=' + val + '&tipo=pausa');
            }
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

// --- AVVIO ---
document.addEventListener('DOMContentLoaded', () => {
    setTimer(studioMinutes); 
    if (btnStudio) btnStudio.classList.add('active');
    if (suggestionBox) suggestionBox.style.display = "none";
});