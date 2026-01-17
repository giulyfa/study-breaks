const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

// --- CONFIGURAZIONE ---
const COLS = 4;
const ROWS = 4;
const MARGIN = 20;
const TOTAL_CARDS = COLS * ROWS;


const CARD_W = (cvs.width - (COLS + 1) * MARGIN) / COLS;
const CARD_H = (cvs.height - (ROWS + 1) * MARGIN) / ROWS;

// Percorsi immagini (assumiamo siano in img/memory/)
const IMG_PATH = "img/memory/";
const TOTAL_IMAGES = 8; // 8 coppie

// --- VARIABILI DI GIOCO ---
let cards = [];       // Array oggetti carta
let images = {};      // Cache immagini caricate
let loadedCount = 0;  // Contatore immagini caricate
let gameState = "LOADING"; // LOADING, PLAYING, BLOCKED (animazione in corso), WIN

// Variabili per la logica di coppia
let firstCard = null;
let secondCard = null;
let matchesFound = 0;
let moves = 0;

// --- COLORI THEME ---
const COLOR_BG = "#274c43";    // Sfondo tavolo (Verde scuro)
const COLOR_TEXT = "#FFFFFF";  // Testo
const COLOR_ACCENT = "#E49A7D"; // Arancione

// --- CARICAMENTO RISORSE ---
const imageList = ["back.png"]; // Iniziamo con il retro
for (let i = 1; i <= TOTAL_IMAGES; i++) {
    imageList.push(i + ".png");
}

// Funzione per caricare tutte le immagini
function loadImages() {
    imageList.forEach((fileName) => {
        let img = new Image();
        img.src = IMG_PATH + fileName;
        
        // Estrai il nome chiave (es. "1.png" -> "1", "back.png" -> "back")
        let key = fileName.split(".")[0];
        
        img.onload = () => {
            images[key] = img;
            loadedCount++;
            checkLoading();
        };
        
        // Fallback in caso di immagine mancante (disegna un rettangolo colorato)
        img.onerror = () => {
            console.error("Immagine mancante: " + fileName);
            images[key] = null; // Segna come mancante
            loadedCount++;
            checkLoading();
        };
    });
}

function checkLoading() {
    if (loadedCount === imageList.length) {
        initGame();
    } else {
        drawLoading();
    }
}

// --- INIZIALIZZAZIONE ---
function initGame() {
    // 1. Crea le coppie di ID (da 1 a 8, due volte)
    let ids = [];
    for (let i = 1; i <= TOTAL_IMAGES; i++) {
        ids.push(i);
        ids.push(i);
    }
    
    // 2. Mescola (Algoritmo Fisher-Yates)
    for (let i = ids.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [ids[i], ids[j]] = [ids[j], ids[i]];
    }
    
    // 3. Crea griglia carte
    cards = [];
    let index = 0;
    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            cards.push({
                x: MARGIN + c * (CARD_W + MARGIN),
                y: MARGIN + r * (CARD_H + MARGIN),
                w: CARD_W,
                h: CARD_H,
                id: ids[index],      // ID dell'immagine (1-8)
                state: "HIDDEN"      // HIDDEN, FLIPPED, MATCHED
            });
            index++;
        }
    }
    
    gameState = "PLAYING";
    draw();
}

// --- DISEGNO ---
function draw() {
    // Sfondo
    ctx.fillStyle = COLOR_BG;
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    if (gameState === "WIN") {
        drawWinScreen();
        return;
    }

    // Disegna le carte
    cards.forEach(card => {
        if (card.state === "HIDDEN") {
            drawCardBack(card);
        } else {
            drawCardFace(card);
        }
    });
}

function drawCardBack(card) {
    if (images["back"]) {
        ctx.drawImage(images["back"], card.x, card.y, card.w, card.h);
    } else {
        // Fallback grafico se manca l'immagine
        ctx.fillStyle = COLOR_ACCENT;
        ctx.fillRect(card.x, card.y, card.w, card.h);
        ctx.strokeStyle = "#fff";
        ctx.lineWidth = 2;
        ctx.strokeRect(card.x, card.y, card.w, card.h);
        
        ctx.fillStyle = "#fff";
        ctx.font = "30px Quicksand";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText("?", card.x + card.w/2, card.y + card.h/2);
    }
}

function drawCardFace(card) {
    let imgKey = card.id.toString();
    
    if (images[imgKey]) {
        ctx.drawImage(images[imgKey], card.x, card.y, card.w, card.h);
    } else {
        // Fallback grafico
        ctx.fillStyle = "#fff";
        ctx.fillRect(card.x, card.y, card.w, card.h);
        
        ctx.fillStyle = "#333";
        ctx.font = "bold 30px Quicksand";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText(card.id, card.x + card.w/2, card.y + card.h/2);
    }
    
    // Bordo verde per le carte risolte
    if (card.state === "MATCHED") {
        ctx.strokeStyle = "#8EBAA3"; // Verde chiaro
        ctx.lineWidth = 4;
        ctx.strokeRect(card.x, card.y, card.w, card.h);
    }
}

function drawLoading() {
    ctx.fillStyle = COLOR_BG;
    ctx.fillRect(0, 0, cvs.width, cvs.height);
    ctx.fillStyle = COLOR_TEXT;
    ctx.font = "20px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Caricamento...", cvs.width/2, cvs.height/2);
}

function drawWinScreen() {
    // Velo scuro
    ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
    ctx.fillRect(0, 0, cvs.width, cvs.height);
    
    ctx.fillStyle = COLOR_TEXT;
    ctx.font = "bold 40px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Vittoria!", cvs.width/2, cvs.height/2 - 20);
    
    ctx.font = "20px Quicksand";
    ctx.fillText("Mosse totali: " + moves, cvs.width/2, cvs.height/2 + 20);
    
    ctx.fillStyle = COLOR_ACCENT;
    ctx.font = "18px Quicksand";
    ctx.fillText("Clicca per rigiocare", cvs.width/2, cvs.height/2 + 60);
}

// --- LOGICA DI GIOCO ---
cvs.addEventListener("mousedown", onInput);

function onInput(e) {
    const rect = cvs.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    if (gameState === "WIN") {
        resetGame();
        return;
    }

    if (gameState === "BLOCKED") return; // Stiamo aspettando il timer

    // Trova quale carta è stata cliccata
    for (let card of cards) {
        if (
            mouseX >= card.x && mouseX <= card.x + card.w &&
            mouseY >= card.y && mouseY <= card.y + card.h
        ) {
            handleCardClick(card);
            break; 
        }
    }
}

function handleCardClick(card) {
    // Ignora se la carta è già girata o risolta
    if (card.state !== "HIDDEN") return;

    // Gira la carta
    card.state = "FLIPPED";
    draw();

    if (!firstCard) {
        firstCard = card;
    } else {
        secondCard = card;
        moves++;
        gameState = "BLOCKED"; // Blocca input mentre controlliamo

        checkForMatch();
    }
}

function checkForMatch() {
    if (firstCard.id === secondCard.id) {
        firstCard.state = "MATCHED";
        secondCard.state = "MATCHED";
        matchesFound++;
        
        resetTurn();
        
        if (matchesFound === TOTAL_IMAGES) {
            setTimeout(() => {
                gameState = "WIN";
                draw();
            }, 500);
        }
    } else {
        setTimeout(() => {
            firstCard.state = "HIDDEN";
            secondCard.state = "HIDDEN";
            resetTurn();
        }, 1000); // 1 secondo di attesa
    }
}

function resetTurn() {
    firstCard = null;
    secondCard = null;
    if (gameState !== "WIN") gameState = "PLAYING";
    draw();
}

function resetGame() {
    matchesFound = 0;
    moves = 0;
    initGame();
}

loadImages();