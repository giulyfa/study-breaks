const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");
const box = 20; 

let cols, rows;
let snake = [];
let food;
let score = 0;
let d; 
let game; 
const gameSpeed = 100;

function initGameDimensions() {
    size = Math.min(cvs.clientWidth, cvs.clientHeight)
    cvs.width = size;
    cvs.height = size;

    cols = Math.floor(size / box);
    rows = Math.floor(size / box);

    snake = [];
    snake[0] = {
        x: Math.floor(cols / 2) * box,
        y: Math.floor(rows / 2) * box
    };

    food = spawnFood();
}

initGameDimensions();


document.addEventListener("keydown", direction);
function direction(event) {
    let key = event.keyCode;
    if([37, 38, 39, 40].indexOf(key) > -1) event.preventDefault();
    changeDirection(key);
}

let touchStartX = 0;
let touchStartY = 0;

cvs.addEventListener('touchstart', function(e) {
    e.preventDefault(); 
    touchStartX = e.changedTouches[0].screenX;
    touchStartY = e.changedTouches[0].screenY;
}, { passive: false });

cvs.addEventListener('touchend', function(e) {
    e.preventDefault();
    let touchEndX = e.changedTouches[0].screenX;
    let touchEndY = e.changedTouches[0].screenY;
    handleSwipe(touchStartX, touchStartY, touchEndX, touchEndY);
}, { passive: false });

function handleSwipe(sx, sy, ex, ey) {
    let xDiff = ex - sx;
    let yDiff = ey - sy;

    if (Math.abs(xDiff) < 10 && Math.abs(yDiff) < 10) return;

    if (Math.abs(xDiff) > Math.abs(yDiff)) {
        if (xDiff > 0) changeDirection(39);
        else changeDirection(37);
    } else {
        if (yDiff > 0) changeDirection(40);
        else changeDirection(38);
    }
}

function changeDirection(key) {
    if( key == 37 && d != "RIGHT") d = "LEFT";
    else if(key == 38 && d != "DOWN") d = "UP";
    else if(key == 39 && d != "LEFT") d = "RIGHT";
    else if(key == 40 && d != "UP") d = "DOWN";
}


function spawnFood() {
    let newFood;
    let isOnSnake;

    do {
        isOnSnake = false;
        newFood = {
            x: Math.floor(Math.random() * cols) * box,
            y: Math.floor(Math.random() * rows) * box
        };
        for (let part of snake) {
            if (part.x === newFood.x && part.y === newFood.y) {
                isOnSnake = true;
                break;
            }
        }
    } while (isOnSnake);

    return newFood;
}

function draw() {
    ctx.fillStyle = "#274c43"; 
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    for( let i = 0; i < snake.length; i++){
        ctx.fillStyle = ( i == 0 ) ? "#8EBAA3" : "#69A297"; 
        ctx.fillRect(snake[i].x, snake[i].y, box, box);
        ctx.strokeStyle = "#274c43";
        ctx.strokeRect(snake[i].x, snake[i].y, box, box);
    }

    ctx.fillStyle = "#E49A7D";
    ctx.fillRect(food.x, food.y, box, box);

    let snakeX = snake[0].x;
    let snakeY = snake[0].y;

    if( d == "LEFT") snakeX -= box;
    if( d == "UP") snakeY -= box;
    if( d == "RIGHT") snakeX += box;
    if( d == "DOWN") snakeY += box;

    if(snakeX == food.x && snakeY == food.y){
        score++;
        food = spawnFood();
    } else {
        snake.pop();
    }

    let newHead = { x : snakeX, y : snakeY };

    if(snakeX < 0 || snakeX >= cvs.width || snakeY < 0 || snakeY >= cvs.height || collision(newHead, snake)){
        clearInterval(game);
        showGameOver();
        return;
    }

    snake.unshift(newHead);

    ctx.fillStyle = "white";
    ctx.font = "20px Quicksand";
    ctx.fillText("Punti: " + score, 10, 25);
}

function collision(head, array){
    for(let i = 0; i < array.length; i++){
        if(head.x == array[i].x && head.y == array[i].y){
            return true;
        }
    }
    return false;
}

function showGameOver() {
    ctx.fillStyle = "rgba(0,0,0,0.5)";
    ctx.fillRect(0, 0, cvs.width, cvs.height);
    
    ctx.fillStyle = "white";
    ctx.font = "30px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Game Over!", cvs.width/2, cvs.height/2 - 20);
    
    ctx.font = "20px Quicksand";
    ctx.fillText("Punteggio: " + score, cvs.width/2, cvs.height/2 + 20);
    
    ctx.fillStyle = "#E49A7D";
    ctx.fillText("Tocca per rigiocare", cvs.width/2, cvs.height/2 + 60);
    
    setTimeout(() => {
        cvs.addEventListener("click", restartGame);
        cvs.addEventListener("touchend", restartGame, {once: true});
    }, 500);
}

function restartGame(e) {
    if(e) e.preventDefault();
    cvs.removeEventListener("click", restartGame);

    initGameDimensions();
    
    score = 0;
    d = null; 
    
    game = setInterval(draw, gameSpeed);
}

game = setInterval(draw, gameSpeed);