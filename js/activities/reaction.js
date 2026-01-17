// Recuperiamo il canvas
const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

// Stati del gioco
const STATE_START = 0;      // Schermata iniziale
const STATE_WAITING = 1;    // In attesa del verde (sfondo arancione)
const STATE_READY = 2;      // È verde! Clicca ora!
const STATE_RESULT = 3;     // Mostra il risultato
const STATE_TOO_SOON = 4;   // Cliccato troppo presto

// Variabili di stato
let gameState = STATE_START;
let startTime = 0;
let timeoutId = null;
let reactionTime = 0;

// Colori del tema (coerenti con style.css e snake.js)
const COLOR_BG_DARK = "#274c43";   // Verde scuro (sfondo base)
const COLOR_BG_WAIT = "#E49A7D";   // Arancione (Aspetta)
const COLOR_BG_GO = "#69A297";     // Verde chiaro (VAI!)
const COLOR_TEXT = "#FFFFFF";      // Testo bianco

// Funzione principale di disegno
function draw() {
    // Pulisci tutto
    ctx.clearRect(0, 0, cvs.width, cvs.height);
    
    // Impostazioni testo comuni
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillStyle = COLOR_TEXT;
    
    let centerX = cvs.width / 2;
    let centerY = cvs.height / 2;

    if (gameState === STATE_START) {
        // Schermata Iniziale
        ctx.fillStyle = COLOR_BG_DARK;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 30px Quicksand";
        ctx.fillText("Test dei Riflessi", centerX, centerY - 30);
        
        ctx.font = "20px Quicksand";
        ctx.fillText("Clicca per iniziare", centerX, centerY + 20);
        
    } else if (gameState === STATE_WAITING) {
        // Fase di attesa (Arancione)
        ctx.fillStyle = COLOR_BG_WAIT;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 35px Quicksand";
        ctx.fillText("Aspetta il verde...", centerX, centerY);
        
    } else if (gameState === STATE_READY) {
        // Fase di reazione (Verde)
        ctx.fillStyle = COLOR_BG_GO;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 50px Quicksand";
        ctx.fillText("CLICCA!", centerX, centerY);
        
    } else if (gameState === STATE_RESULT) {
        // Risultato
        ctx.fillStyle = COLOR_BG_DARK;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 50px Quicksand";
        ctx.fillText(reactionTime + " ms", centerX, centerY - 20);
        
        ctx.font = "20px Quicksand";
        
        // Messaggio personalizzato in base al tempo
        let comment = "";
        if(reactionTime < 200) comment = "Incredibile!";
        else if(reactionTime < 300) comment = "Ottimo lavoro!";
        else comment = "Puoi fare di meglio!";
        
        ctx.fillText(comment, centerX, centerY + 30);
        
        ctx.font = "16px Quicksand";
        ctx.fillStyle = "#E49A7D"; // Colore accent per l'istruzione
        ctx.fillText("Clicca per riprovare", centerX, centerY + 70);

    } else if (gameState === STATE_TOO_SOON) {
        // Errore: troppo presto
        ctx.fillStyle = COLOR_BG_WAIT; // Manteniamo l'arancione/rosso dell'errore
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 35px Quicksand";
        ctx.fillText("Troppo presto!", centerX, centerY - 20);
        
        ctx.font = "20px Quicksand";
        ctx.fillText("Clicca per riprovare", centerX, centerY + 30);
    }
}

// Gestione del click (Input)
function handleInput() {
    if (gameState === STATE_START || gameState === STATE_RESULT || gameState === STATE_TOO_SOON) {
        // AVVIA IL GIOCO
        gameState = STATE_WAITING;
        draw();
        
        // Genera un ritardo casuale tra 2 e 5 secondi (2000-5000 ms)
        let delay = Math.floor(Math.random() * 3000) + 2000;
        
        timeoutId = setTimeout(() => {
            // Passa allo stato "PRONTO"
            gameState = STATE_READY;
            startTime = Date.now(); // Salva il tempo corrente
            draw();
        }, delay);
        
    } else if (gameState === STATE_WAITING) {
        // CLICCATO TROPPO PRESTO
        clearTimeout(timeoutId); // Cancella il timer del verde
        gameState = STATE_TOO_SOON;
        draw();
        
    } else if (gameState === STATE_READY) {
        // CLIC CORRETTO
        let endTime = Date.now();
        reactionTime = endTime - startTime;
        gameState = STATE_RESULT;
        draw();
    }
}

// Aggiungi listener per click del mouse e tocco su mobile
cvs.addEventListener("mousedown", handleInput);
// cvs.addEventListener("touchstart", handleInput); // Opzionale per migliorare la risposta su mobile

// Avvia il render iniziale
draw();