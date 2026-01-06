// --- 1. CONFIGURAZIONE E VARIABILI ---
let studioMinutes = 25; 
let pausaMinutes = 5;
let timeLeft;
let timerId = null;
let isRunning = false;
let currentMode = 'studio'; 

// Elementi del DOM - Timer
const timerDisplay = document.getElementById('timer-time');
const startBtn = document.getElementById('start-btn');
const stopBtn = document.getElementById('stop-btn');
const restartBtn = document.getElementById('restart-btn');
const btnStudio = document.getElementById('mode-studio');
const btnPausa = document.getElementById('mode-pausa');

// Elementi del DOM - Navigazione
const openSidebar = document.getElementById('open-sidebar');
const closeSidebar = document.querySelector('.close-btn');
const sidebar = document.getElementById('sidebar-nav');

// Elementi del DOM - Modal Impostazioni
const settingsBtn = document.getElementById('settings-trigger');
const modal = document.getElementById('custom-modal');
const inputMins = document.getElementById('new-minutes');
const saveModalBtn = document.getElementById('save-modal');
const closeModalBtn = document.getElementById('close-modal');

// --- 2. FUNZIONI DI GESTIONE TIMER ---

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

// --- 3. SELEZIONE MODALITÀ ---

btnStudio.addEventListener('click', () => {
    currentMode = 'studio';
    setTimer(studioMinutes);
    btnStudio.classList.add('active');
    btnPausa.classList.remove('active');
});

btnPausa.addEventListener('click', () => {
    currentMode = 'pausa';
    setTimer(pausaMinutes);
    btnPausa.classList.add('active');
    btnStudio.classList.remove('active');
});

// --- 4. CONTROLLI TIMER ---

startBtn.addEventListener('click', () => {
    if (isRunning) return;
    isRunning = true;
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
});

function handleTimerComplete() {
    alert("Sessione completata! Ottimo lavoro.");
    const sessionsCountSpan = document.getElementById('sessions-count'); 
    if (currentMode === 'studio') {
        let currentSessions = parseInt(sessionsCountSpan.textContent) || 0;
        currentSessions++;
        sessionsCountSpan.textContent = currentSessions;
    }
}

// --- 5. NAVIGAZIONE SIDEBAR ---

if (openSidebar) {
    openSidebar.addEventListener('click', () => {
        sidebar.classList.add('open');
    });
}

if (closeSidebar) {
    closeSidebar.addEventListener('click', () => {
        sidebar.classList.remove('open');
    });
}

// --- 6. MODAL IMPOSTAZIONI (SOSTITUISCE IL PROMPT) ---

if (settingsBtn) {
    settingsBtn.addEventListener('click', () => {
        modal.style.display = "block"; // Apre la modal
        inputMins.value = (currentMode === 'studio') ? studioMinutes : pausaMinutes;
    });
}

if (saveModalBtn) {
    saveModalBtn.addEventListener('click', () => {
        const val = parseInt(inputMins.value);
        if (val > 0) {
            if (currentMode === 'studio') {
                studioMinutes = val;
            } else {
                pausaMinutes = val;
            }
            if (!isRunning) setTimer(val);
            modal.style.display = "none";
        } else {
            alert("Inserisci un numero valido!");
        }
    });
}

if (closeModalBtn) {
    closeModalBtn.addEventListener('click', () => {
        modal.style.display = "none";
    });
}

// Chiude la modal cliccando fuori dal contenuto
window.addEventListener('click', (event) => {
    if (event.target == modal) {
        modal.style.display = "none";
    }
});

// --- 7. AVVIO INIZIALE ---
document.addEventListener('DOMContentLoaded', () => {
    setTimer(25);
    if (btnStudio) btnStudio.classList.add('active');
});