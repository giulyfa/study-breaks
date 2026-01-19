const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

const STATE_START = 0;
const STATE_WAITING = 1;
const STATE_READY = 2;
const STATE_RESULT = 3;
const STATE_TOO_SOON = 4;

let gameState = STATE_START;
let startTime = 0;
let timeoutId = null;
let reactionTime = 0;


const COLOR_BG_DARK = "#274c43";
const COLOR_BG_WAIT = "#E49A7D";
const COLOR_BG_GO = "#69A297";
const COLOR_TEXT = "#FFFFFF";

function draw() {
    ctx.clearRect(0, 0, cvs.width, cvs.height);

    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillStyle = COLOR_TEXT;
    
    let centerX = cvs.width / 2;
    let centerY = cvs.height / 2;

    if (gameState === STATE_START) {
        ctx.fillStyle = COLOR_BG_DARK;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 30px Quicksand";
        ctx.fillText("Test dei Riflessi", centerX, centerY - 30);
        
        ctx.font = "20px Quicksand";
        ctx.fillText("Clicca per iniziare", centerX, centerY + 20);
        
    } else if (gameState === STATE_WAITING) {
        ctx.fillStyle = COLOR_BG_WAIT;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 35px Quicksand";
        ctx.fillText("Aspetta il verde...", centerX, centerY);
        
    } else if (gameState === STATE_READY) {
        ctx.fillStyle = COLOR_BG_GO;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 50px Quicksand";
        ctx.fillText("CLICCA!", centerX, centerY);
        
    } else if (gameState === STATE_RESULT) {
        ctx.fillStyle = COLOR_BG_DARK;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 50px Quicksand";
        ctx.fillText(reactionTime + " ms", centerX, centerY - 20);
        
        ctx.font = "20px Quicksand";

        let comment = "";
        if(reactionTime < 200) comment = "Incredibile!";
        else if(reactionTime < 300) comment = "Ottimo lavoro!";
        else comment = "Puoi fare di meglio!";
        
        ctx.fillText(comment, centerX, centerY + 30);
        
        ctx.font = "16px Quicksand";
        ctx.fillStyle = "#E49A7D";
        ctx.fillText("Clicca per riprovare", centerX, centerY + 70);

    } else if (gameState === STATE_TOO_SOON) {
        ctx.fillStyle = COLOR_BG_WAIT;
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = COLOR_TEXT;
        ctx.font = "bold 35px Quicksand";
        ctx.fillText("Troppo presto!", centerX, centerY - 20);
        
        ctx.font = "20px Quicksand";
        ctx.fillText("Clicca per riprovare", centerX, centerY + 30);
    }
}


function handleInput() {
    if (gameState === STATE_START || gameState === STATE_RESULT || gameState === STATE_TOO_SOON) {
        gameState = STATE_WAITING;
        draw();

        let delay = Math.floor(Math.random() * 3000) + 2000;
        
        timeoutId = setTimeout(() => {
            gameState = STATE_READY;
            startTime = Date.now();
            draw();
        }, delay);
        
    } else if (gameState === STATE_WAITING) {
        clearTimeout(timeoutId);
        gameState = STATE_TOO_SOON;
        draw();
        
    } else if (gameState === STATE_READY) {
        let endTime = Date.now();
        reactionTime = endTime - startTime;
        gameState = STATE_RESULT;
        draw();
    }
}

cvs.addEventListener("mousedown", handleInput);
cvs.addEventListener("touchstart", handleInput);

draw();