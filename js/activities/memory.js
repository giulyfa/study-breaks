const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

const COLS = 4;
const ROWS = 4;
const MARGIN = 20;
const TOTAL_CARDS = COLS * ROWS;

const maxW = (cvs.width - (COLS + 1) * MARGIN) / COLS;
const maxH = (cvs.height - (ROWS + 1) * MARGIN) / ROWS;

const CARD_SIZE = Math.min(maxW, maxH);
const CARD_W = CARD_SIZE;
const CARD_H = CARD_SIZE;

const GRID_W = COLS * CARD_W + (COLS - 1) * MARGIN;
const GRID_H = ROWS * CARD_H + (ROWS - 1) * MARGIN;

const OFFSET_X = (cvs.width - GRID_W) / 2;
const OFFSET_Y = (cvs.height - GRID_H) / 2;


const IMG_PATH = "./../img/memory/";
const TOTAL_IMAGES = 8;

let cards = [];
let images = {};
let loadedCount = 0;
let gameState = "LOADING";

let firstCard = null;
let secondCard = null;
let matchesFound = 0;
let moves = 0;


const COLOR_BG = "#274c43";
const COLOR_TEXT = "#FFFFFF";
const COLOR_ACCENT = "#E49A7D";


const imageList = ["back.png"];
for (let i = 1; i <= TOTAL_IMAGES; i++) {
    imageList.push(i + ".png");
}

function loadImages() {
    imageList.forEach((fileName) => {
        let img = new Image();
        img.src = IMG_PATH + fileName;

        let key = fileName.split(".")[0];
        
        img.onload = () => {
            images[key] = img;
            loadedCount++;
            checkLoading();
        };

        img.onerror = () => {
            console.error("Immagine mancante: " + fileName);
            images[key] = null;
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


function initGame() {
    let ids = [];
    for (let i = 1; i <= TOTAL_IMAGES; i++) {
        ids.push(i);
        ids.push(i);
    }
    
    for (let i = ids.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [ids[i], ids[j]] = [ids[j], ids[i]];
    }

    cards = [];
    let index = 0;
    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            let posX = OFFSET_X + c * (CARD_W + MARGIN);
            let posY = OFFSET_Y + r * (CARD_H + MARGIN);

            cards.push({
                x: posX,
                y: posY,
                w: CARD_W,
                h: CARD_H,
                id: ids[index],
                state: "HIDDEN"
            });
            index++;
        }
    }
    
    gameState = "PLAYING";
    draw();
}

function draw() {
    ctx.fillStyle = COLOR_BG;
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    if (gameState === "WIN") {
        drawWinScreen();
        return;
    }

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
        ctx.fillStyle = "#fff";
        ctx.fillRect(card.x, card.y, card.w, card.h);
        
        ctx.fillStyle = "#333";
        ctx.font = "bold 30px Quicksand";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText(card.id, card.x + card.w/2, card.y + card.h/2);
    }

    if (card.state === "MATCHED") {
        ctx.strokeStyle = "#8EBAA3";
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


cvs.addEventListener("mousedown", onInput);

cvs.addEventListener("touchstart", function(e) {
    e.preventDefault();
    let touch = e.touches[0];
    let mouseEvent = new MouseEvent("mousedown", {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    cvs.dispatchEvent(mouseEvent);
}, {passive: false});


function onInput(e) {
    const rect = cvs.getBoundingClientRect();
    const scaleX = cvs.width / rect.width;
    const scaleY = cvs.height / rect.height;

    const mouseX = (e.clientX - rect.left) * scaleX;
    const mouseY = (e.clientY - rect.top) * scaleY;

    if (gameState === "WIN") {
        resetGame();
        return;
    }

    if (gameState === "BLOCKED") return;

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
    if (card.state !== "HIDDEN") return;


    card.state = "FLIPPED";
    draw();

    if (!firstCard) {
        firstCard = card;
    } else {
        secondCard = card;
        moves++;
        gameState = "BLOCKED";

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
        }, 1000);
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