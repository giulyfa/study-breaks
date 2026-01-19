/* * SNAKE GAME - Study Breaks
 * Logica di gioco adattiva per canvas quadrato
 */

// Recuperiamo il canvas
const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

// Unità di misura della griglia (20px per quadrato)
const box = 20;

// Calcoliamo quante colonne e righe ci sono in base alle dimensioni del canvas
// Questo rende il gioco adattabile se cambi width/height nell'HTML
const cols = Math.floor(cvs.width / box);
const rows = Math.floor(cvs.height / box);

// Variabili di gioco
let snake = [];

// Posizioniamo il serpente al centro del canvas all'inizio
snake[0] = {
    x: Math.floor(cols / 2) * box,
    y: Math.floor(rows / 2) * box
};

let food = spawnFood(); // Usiamo una funzione per generare il cibo
let score = 0;
let d; // Direzione
let game; // Variabile intervallo

// Velocità del gioco (ms)
const gameSpeed = 100;

// Ascoltatore eventi tastiera
document.addEventListener("keydown", direction);

function direction(event) {
    let key = event.keyCode;
    
    // Evita lo scroll della pagina con le frecce
    if([37, 38, 39, 40].indexOf(key) > -1) {
        event.preventDefault();
    }

    // Logica direzionale (impedisce inversione a U)
    if( key == 37 && d != "RIGHT") {
        d = "LEFT";
    } else if(key == 38 && d != "DOWN") {
        d = "UP";
    } else if(key == 39 && d != "LEFT") {
        d = "RIGHT";
    } else if(key == 40 && d != "UP") {
        d = "DOWN";
    }
}

// Funzione per generare coordinate casuali per il cibo
function spawnFood() {
    return {
        x: Math.floor(Math.random() * cols) * box,
        y: Math.floor(Math.random() * rows) * box
    };
}

// Funzione principale di disegno
function draw() {
    // 1. Sfondo
    ctx.fillStyle = "#274c43"; 
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    // 2. Disegna Serpente
    for( let i = 0; i < snake.length; i++){
        ctx.fillStyle = ( i == 0 ) ? "#8EBAA3" : "#69A297"; 
        ctx.fillRect(snake[i].x, snake[i].y, box, box);
        
        ctx.strokeStyle = "#274c43";
        ctx.strokeRect(snake[i].x, snake[i].y, box, box);
    }

    // 3. Disegna Cibo
    ctx.fillStyle = "#E49A7D";
    ctx.fillRect(food.x, food.y, box, box);

    // 4. Posizione testa attuale
    let snakeX = snake[0].x;
    let snakeY = snake[0].y;

    // 5. Aggiornamento posizione
    if( d == "LEFT") snakeX -= box;
    if( d == "UP") snakeY -= box;
    if( d == "RIGHT") snakeX += box;
    if( d == "DOWN") snakeY += box;

    // 6. Logica Cibo
    if(snakeX == food.x && snakeY == food.y){
        score++;
        food = spawnFood(); // Genera nuovo cibo
        // NON facciamo snake.pop(), così il serpente cresce
    } else {
        // Rimuovi la coda
        snake.pop();
    }

    // 7. Nuova testa
    let newHead = {
        x : snakeX,
        y : snakeY
    }

    // 8. Game Over (Muri o corpo)
    if(snakeX < 0 || snakeX >= cvs.width || snakeY < 0 || snakeY >= cvs.height || collision(newHead, snake)){
        clearInterval(game);
        showGameOver();
        return;
    }

    // Aggiungi nuova testa
    snake.unshift(newHead);

    // 9. Punteggio
    ctx.fillStyle = "white";
    ctx.font = "20px Quicksand";
    ctx.fillText("Punti: " + score, 10, 25);
}

// Rilevamento collisioni corpo
function collision(head, array){
    for(let i = 0; i < array.length; i++){
        if(head.x == array[i].x && head.y == array[i].y){
            return true;
        }
    }
    return false;
}

// Schermata Game Over
function showGameOver() {
    ctx.fillStyle = "rgba(0,0,0,0.5)";
    ctx.fillRect(0, 0, cvs.width, cvs.height);
    
    ctx.fillStyle = "white";
    ctx.font = "30px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Game Over!", cvs.width/2, cvs.height/2 - 20);
    
    ctx.font = "20px Quicksand";
    ctx.fillText("Punteggio finale: " + score, cvs.width/2, cvs.height/2 + 20);
    
    ctx.fillStyle = "#E49A7D";
    ctx.fillText("Clicca per rigiocare", cvs.width/2, cvs.height/2 + 60);
    
    // Piccolo ritardo per evitare click accidentali immediati
    setTimeout(() => {
        cvs.addEventListener("click", restartGame);
    }, 200);
}

function restartGame() {
    cvs.removeEventListener("click", restartGame);
    
    // Reset variabili
    snake = [];
    snake[0] = { 
        x: Math.floor(cols / 2) * box, 
        y: Math.floor(rows / 2) * box 
    };
    score = 0;
    d = null; 
    food = spawnFood();
    
    // Riavvia loop
    game = setInterval(draw, gameSpeed);
}

// AVVIO DEL GIOCO
game = setInterval(draw, gameSpeed);