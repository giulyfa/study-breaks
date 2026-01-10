// --- 1. CONFIGURAZIONE E VARIABILI ---

// AGGIUNTA: Legge il valore impostato nel PHP, se non esiste usa 25
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

// Elementi DOM - Modal & Suggerimenti
const settingsBtn = document.getElementById('settings-trigger');
const modal = document.getElementById('custom-modal');
const inputMins = document.getElementById('new-minutes');
const saveModalBtn = document.getElementById('save-modal');
const closeModalBtn = document.getElementById('close-modal');
const suggestionBox = document.getElementById('suggestion-message');

// --- 2. FUNZIONI DI SERVIZIO ---

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

// --- 3. LOGICA DI FINE SESSIONE ---

function handleTimerComplete() {
    if (currentMode === 'studio') {
        hideCustomAlert();
        
        // Sessioni Oggi
        let sessionsCountSpan = document.getElementById('sessions-count'); 
        let currentSessions = parseInt(sessionsCountSpan.textContent) || 0;
        sessionsCountSpan.textContent = currentSessions + 1;

        // AGGIUNTA: Aggiornamento Streak in tempo reale
        let streakCountSpan = document.getElementById('streak-count');
        if (streakCountSpan) {
            let currentStreak = parseInt(streakCountSpan.textContent) || 0;
            
            // Logica: se era 0 diventa 1. Se era già più di 0, aumenta di 1.
            // Nota: visivamente aumenterà ogni volta che finisci una sessione in questo test.
            streakCountSpan.textContent = currentStreak + 1;
        }

        // Usiamo studioMinutes che è la variabile che contiene il tempo impostato
        // 2. SALVATAGGIO E AGGIORNAMENTO STREAK REALE
        fetch(`salva_dati.php?azione=studio&durata=${studioMinutes}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Prendi il quadratino della streak
                    let streakCountSpan = document.getElementById('streak-count');
                    if (streakCountSpan) {
                        // USA IL DATO CHE ARRIVA DAL PHP!
                        // Se il PHP dice che la streak è 5, scriviamo 5.
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
        // Aggiornamento pause in tempo reale
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

// --- 4. GESTIONE EVENTI ---

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

// --- 5. MODAL ---

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
                // AGGIUNTA: Salva la scelta nel PHP così non si resetta al refresh
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

// --- 6. AVVIO ---
document.addEventListener('DOMContentLoaded', () => {
    // Aggiorna il timer basandosi su studioMinutes (che ora può essere quello salvato)
    setTimer(studioMinutes); 
    
    if (btnStudio) btnStudio.classList.add('active');
    if (suggestionBox) suggestionBox.style.display = "none";
});