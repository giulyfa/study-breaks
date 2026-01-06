/* * SNAKE GAME - Study Breaks
 * Logica di gioco per l'attività "Snake"
 */

// Recuperiamo il canvas creato nel file activity_player.php (o nell'iframe)
const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

// Unità di misura della griglia (20px per quadrato)
const box = 20;

// Variabili di gioco
let snake = [];
snake[0] = {
    x: 9 * box,
    y: 10 * box
};


let food = {
    x: Math.floor(Math.random() * (cvs.width / box)) * box,
    y: Math.floor(Math.random() * (cvs.height / box)) * box
};

let score = 0;
let d; // Direzione
let game; // Variabile per l'intervallo del gioco

// Ascoltatore eventi tastiera
document.addEventListener("keydown", direction);

function direction(event) {
    let key = event.keyCode;
    
    // Evita lo scroll della pagina quando si usano le frecce
    if([37, 38, 39, 40].indexOf(key) > -1) {
        event.preventDefault();
    }

    // Logica direzionale (non si può tornare indietro di 180 gradi)
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

// Funzione per disegnare il gioco
function draw() {
    // 1. Sfondo del Canvas (Dark Green del tema)
    ctx.fillStyle = "#274c43"; 
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    // 2. Disegna il Serpente
    for( let i = 0; i < snake.length; i++){
        // Testa verde chiaro, corpo verde medio
        ctx.fillStyle = ( i == 0 ) ? "#8EBAA3" : "#69A297"; 
        ctx.fillRect(snake[i].x, snake[i].y, box, box);
        
        // Contorno per distinguere i blocchi
        ctx.strokeStyle = "#274c43";
        ctx.strokeRect(snake[i].x, snake[i].y, box, box);
    }

    // 3. Disegna il Cibo (Arancione)
    ctx.fillStyle = "#E49A7D";
    ctx.fillRect(food.x, food.y, box, box);

    // 4. Posizione attuale della testa
    let snakeX = snake[0].x;
    let snakeY = snake[0].y;

    // 5. Aggiorna posizione in base alla direzione
    if( d == "LEFT") snakeX -= box;
    if( d == "UP") snakeY -= box;
    if( d == "RIGHT") snakeX += box;
    if( d == "DOWN") snakeY += box;

    // 6. Se il serpente mangia il cibo
    if(snakeX == food.x && snakeY == food.y){
        score++;
        // Genera nuovo cibo
        food = {
            x: Math.floor(Math.random() * 19 + 1) * box,
            y: Math.floor(Math.random() * 19 + 1) * box
        }
        // NON rimuoviamo la coda -> il serpente cresce
    } else {
        // Rimuovi la coda (movimento normale)
        snake.pop();
    }

    // 7. Nuova testa
    let newHead = {
        x : snakeX,
        y : snakeY
    }

    // 8. Game Over Rules
    if(snakeX < 0 || snakeX >= cvs.width || snakeY < 0 || snakeY >= cvs.height || collision(newHead, snake)){
        clearInterval(game);
        showGameOver();
        return; // Ferma l'esecuzione di draw
    }

    // Aggiungi la nuova testa all'array
    snake.unshift(newHead);

    // 9. Disegna il Punteggio
    ctx.fillStyle = "white";
    ctx.font = "20px Quicksand"; // Usa il font del sito
    ctx.fillText("Punti: " + score, 10, 25);
}

// Funzione rilevamento collisioni con se stesso
function collision(head, array){
    for(let i = 0; i < array.length; i++){
        if(head.x == array[i].x && head.y == array[i].y){
            return true;
        }
    }
    return false;
}

// Funzione Game Over
function showGameOver() {
    // Sfondo semi-trasparente sopra il gioco
    ctx.fillStyle = "rgba(0,0,0,0.5)";
    ctx.fillRect(0, 0, cvs.width, cvs.height);
    
    // Testo Game Over
    ctx.fillStyle = "white";
    ctx.font = "30px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Game Over!", cvs.width/2, cvs.height/2 - 20);
    
    ctx.font = "20px Quicksand";
    ctx.fillText("Punteggio finale: " + score, cvs.width/2, cvs.height/2 + 20);
    
    ctx.fillStyle = "#E49A7D";
    ctx.fillText("Clicca per rigiocare", cvs.width/2, cvs.height/2 + 60);
    
    // Aggiunge listener per ricominciare al click
    cvs.addEventListener("click", restartGame);
}

function restartGame() {
    cvs.removeEventListener("click", restartGame);
    // Reset variabili
    snake = [];
    snake[0] = { x: 9 * box, y: 10 * box };
    score = 0;
    d = null; // Ferma il movimento finché non si preme un tasto
    
    // Riavvia loop
    game = setInterval(draw, 100);
}

// AVVIO DEL GIOCO
// Esegui draw ogni 100ms
game = setInterval(draw, 190);