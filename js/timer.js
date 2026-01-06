// --- 1. CONFIGURAZIONE E VARIABILI ---
let studioMinutes = 25; 
let pausaMinutes = 5;
let timeLeft;
let timerId = null;
let isRunning = false;
let currentMode = 'studio'; 

// Elementi del DOM
const timerDisplay = document.getElementById('timer-time');
const startBtn = document.getElementById('start-btn');
const stopBtn = document.getElementById('stop-btn');
const restartBtn = document.getElementById('restart-btn');
const settingsBtn = document.getElementById('settings-trigger');
const btnStudio = document.getElementById('mode-studio');
const btnPausa = document.getElementById('mode-pausa');
const openSidebar = document.getElementById('open-sidebar');
const closeSidebar = document.querySelector('.close-btn');
const sidebar = document.getElementById('sidebar-nav');

if (openSidebar) {
    openSidebar.addEventListener('click', () => {
        sidebar.classList.add('open'); // Aggiunge la classe che la sposta a sinistra: 0
    });
}

if (closeSidebar) {
    closeSidebar.addEventListener('click', () => {
        sidebar.classList.remove('open'); // La riporta a sinistra: -250px
    });
}

// Avvia la funzione quando la pagina è pronta
document.addEventListener('DOMContentLoaded', initNavigation);

// --- 2. FUNZIONI DI GESTIONE ---

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

// --- 3. SELEZIONE MODALITÀ (CON EVIDENZIAZIONE) ---

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
    alert("Sessione completata!");
    const sessionsCountSpan = document.getElementById('sessions-count'); 
    if (currentMode === 'studio') {
        let currentSessions = parseInt(sessionsCountSpan.textContent) || 0;
        currentSessions++;
        sessionsCountSpan.textContent = currentSessions;
    }
}

// --- 5. IMPOSTAZIONI INTELLIGENTI ---

if (settingsBtn) {
    settingsBtn.addEventListener('click', () => {
        let modeName = (currentMode === 'studio') ? "STUDIO" : "PAUSA";
        let currentVal = (currentMode === 'studio') ? studioMinutes : pausaMinutes;

        const userMinutes = prompt(`Quanti minuti vuoi per la ${modeName}?`, currentVal);
        
        if (userMinutes !== null && !isNaN(userMinutes) && userMinutes > 0) {
            let newMins = parseInt(userMinutes);
            if (currentMode === 'studio') studioMinutes = newMins;
            else pausaMinutes = newMins;

            if (!isRunning) setTimer(newMins);
            alert(`Timer ${modeName} aggiornato!`);
        }
    });
}

// --- 6. AVVIO INIZIALE ---
setTimer(25);
btnStudio.classList.add('active');