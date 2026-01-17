/*
 * WHACK-A-MOLE (Acchiappa la Talpa) - Study Breaks
 * Colpisci le talpe arancioni per fare punti!
 */

const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

// --- CONFIGURAZIONE ---
const ROWS = 3;
const COLS = 3;
const GAME_DURATION = 30; // Secondi
const MOLE_STAY_MIN = 600; // ms minimi
const MOLE_STAY_MAX = 1200; // ms massimi

// Colori Theme
const COLOR_BG = "#274c43";      // Sfondo generale
const COLOR_HOLE = "#1a332d";    // Buco scuro
const COLOR_MOLE = "#E49A7D";    // Talpa (Arancione)
const COLOR_MOLE_HIT = "#d15e45"; // Talpa colpita (Rosso/Arancio scuro)
const COLOR_TEXT = "#FFFFFF";    // Testo

// --- STATO DEL GIOCO ---
let holes = [];
let score = 0;
let timeLeft = GAME_DURATION;
let isPlaying = false;
let gameInterval;   // Timer per il conto alla rovescia
let peepTimeout;    // Timer per il movimento delle talpe
let lastHole = -1;

// Inizializza le posizioni dei buchi
function initGrid() {
    holes = [];
    // Calcoliamo dimensioni e margini per centrare la griglia 3x3
    // Canvas 500x400. 
    // Facciamo buchi di raggio X.
    const marginX = 60;
    const marginY = 80;
    const stepX = (cvs.width - 2 * marginX) / (COLS - 1);
    const stepY = (cvs.height - 100 - marginY) / (ROWS - 1); // -100 per spazio header punti

    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            holes.push({
                x: marginX + c * stepX,
                y: marginY + 100 + r * stepY, // Offset Y per header
                r: 40,      // Raggio orizzontale buco
                ry: 15,     // Raggio verticale buco (ellisse)
                active: false, // C'è la talpa?
                hit: false,    // È stata colpita?
                anim: 0        // Per animazione salita/discesa (0 a 1)
            });
        }
    }
}

// --- LOGICA DI GIOCO ---

function startGame() {
    score = 0;
    timeLeft = GAME_DURATION;
    isPlaying = true;
    initGrid();
    
    // Avvia timer gioco
    gameInterval = setInterval(() => {
        timeLeft--;
        if (timeLeft <= 0) {
            endGame();
        }
    }, 1000);

    // Inizia a far uscire le talpe
    peep();
    drawLoop();
}

function endGame() {
    isPlaying = false;
    clearInterval(gameInterval);
    clearTimeout(peepTimeout);
    draw(); // Disegna schermata finale
}

function randomTime(min, max) {
    return Math.round(Math.random() * (max - min) + min);
}

function randomHole(holes) {
    const idx = Math.floor(Math.random() * holes.length);
    if (idx === lastHole) return randomHole(holes);
    lastHole = idx;
    return holes[idx];
}

function peep() {
    if (!isPlaying) return;

    const time = randomTime(MOLE_STAY_MIN, MOLE_STAY_MAX);
    const hole = randomHole(holes);
    
    hole.active = true;
    hole.hit = false;
    hole.anim = 1; // Talpa completamente fuori (semplifichiamo animazione)

    peepTimeout = setTimeout(() => {
        hole.active = false;
        if (isPlaying) peep();
    }, time);
}

// Gestione Input
cvs.addEventListener("mousedown", (e) => {
    if (!isPlaying) {
        startGame();
        return;
    }

    const rect = cvs.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    holes.forEach(hole => {
        if (hole.active && !hole.hit) {
            // Check collisione semplice (rettangolare attorno alla talpa)
            // La talpa spunta sopra il buco (y - 50 px circa)
            if (
                mouseX >= hole.x - hole.r && 
                mouseX <= hole.x + hole.r &&
                mouseY >= hole.y - 70 && // Altezza talpa
                mouseY <= hole.y + hole.ry
            ) {
                // COLPITA!
                score++;
                hole.hit = true;
                hole.active = false; // Sparisce subito o mostra animazione hit
                // Se volessimo feedback visivo immediato senza nascondere:
                // hole.active rimane true ma disegniamo diversamente per un attimo
                // Qui la facciamo sparire per semplicità e ne chiamiamo subito un'altra
                clearTimeout(peepTimeout);
                peep(); 
            }
        }
    });
});

// --- DISEGNO ---

function drawLoop() {
    if (isPlaying) {
        draw();
        requestAnimationFrame(drawLoop);
    }
}

function draw() {
    // Sfondo
    ctx.fillStyle = COLOR_BG;
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    // Header (Punti e Tempo)
    ctx.fillStyle = COLOR_TEXT;
    ctx.font = "bold 24px Quicksand";
    ctx.textAlign = "left";
    ctx.fillText("Punti: " + score, 20, 40);
    
    ctx.textAlign = "right";
    ctx.fillText("Tempo: " + timeLeft, cvs.width - 20, 40);

    // Disegna buchi e talpe
    // Nota: l'ordine è importante per la sovrapposizione (z-index simulato)
    // Disegniamo riga per riga per dare profondità
    
    // Ordiniamo i buchi per Y così quelli davanti coprono quelli dietro se si sovrappongono
    // (Anche se con la griglia attuale non succede, è buona pratica)
    
    holes.forEach(hole => {
        drawHoleAndMole(hole);
    });

    // Schermata Game Over o Start
    if (!isPlaying) {
        ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.textAlign = "center";
        
        if (timeLeft <= 0) {
            ctx.font = "bold 40px Quicksand";
            ctx.fillText("Tempo Scaduto!", cvs.width/2, cvs.height/2 - 20);
            ctx.font = "25px Quicksand";
            ctx.fillText("Punteggio finale: " + score, cvs.width/2, cvs.height/2 + 30);
        } else {
            ctx.font = "bold 40px Quicksand";
            ctx.fillText("Acchiappa la Talpa", cvs.width/2, cvs.height/2 - 20);
            ctx.font = "20px Quicksand";
            ctx.fillText("Clicca per iniziare", cvs.width/2, cvs.height/2 + 30);
        }
    }
}

function drawHoleAndMole(hole) {
    // 1. Disegna il buco (dietro)
    ctx.fillStyle = COLOR_HOLE;
    ctx.beginPath();
    ctx.ellipse(hole.x, hole.y, hole.r, hole.ry, 0, 0, 2 * Math.PI);
    ctx.fill();
    
    // 2. Disegna la Talpa (se attiva)
    // Usiamo una maschera (clip) per far sembrare che esca dal buco?
    // Per semplicità disegniamo la talpa sopra il buco ma "tagliata" in basso se necessario.
    // In questo stile flat, possiamo disegnare la talpa che parte dal centro del buco e va in su.
    
    if (hole.active) {
        let moleColor = hole.hit ? COLOR_MOLE_HIT : COLOR_MOLE;
        
        // Corpo talpa (Cerchio sopra + Rettangolo sotto)
        let moleW = hole.r * 1.2; // Larghezza
        let moleH = 60; // Altezza
        let moleX = hole.x - moleW/2;
        let moleY = hole.y - moleH + 5; // +5 per affondarla un po' nel buco
        
        ctx.fillStyle = moleColor;
        
        // Testa rotonda
        ctx.beginPath();
        ctx.arc(hole.x, moleY, moleW/2, Math.PI, 0); // Semicerchio superiore
        // Lati dritti verso il basso
        ctx.lineTo(moleX + moleW, hole.y); 
        ctx.lineTo(moleX, hole.y);
        ctx.fill();
        
        // Occhi
        ctx.fillStyle = "#333";
        ctx.beginPath();
        ctx.arc(hole.x - 10, moleY - 10, 3, 0, 2*Math.PI);
        ctx.arc(hole.x + 10, moleY - 10, 3, 0, 2*Math.PI);
        ctx.fill();
        
        // Naso
        ctx.fillStyle = "#333";
        ctx.beginPath();
        ctx.ellipse(hole.x, moleY + 5, 6, 4, 0, 0, 2*Math.PI);
        ctx.fill();
        
        // Riflesso naso
        ctx.fillStyle = "#fff";
        ctx.beginPath();
        ctx.arc(hole.x - 2, moleY + 3, 1.5, 0, 2*Math.PI);
        ctx.fill();
    }
    
    // 3. Bordo buco anteriore (opzionale, per dare effetto profondità "davanti" alla talpa)
    // Ridisegniamo la metà inferiore dell'ellisse del buco per coprire la base della talpa
    ctx.fillStyle = COLOR_HOLE; // O un colore leggermente diverso per il bordo
    ctx.beginPath();
    ctx.ellipse(hole.x, hole.y, hole.r, hole.ry, 0, 0, Math.PI); // Solo metà sotto
    ctx.fill();
}

// Avvio schermata iniziale
initGrid();
draw();