const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

let score = 0;
let gameOver = false;
let holes = [];
let moles = [];
let activeTimeout = null;

const GRID_SIZE = 3;
const HOLE_RADIUS = 30;
const MOLE_RADIUS = 25;
const MIN_MOLE_TIME = 800;
const MAX_MOLE_TIME = 1500;
const SPAWN_DELAY = 700;
const WINNING_SCORE = 20;

function resizeCanvas() {
    const size = Math.min(canvas.clientWidth, canvas.clientHeight);
    canvas.width = size;
    canvas.height = size;

    setupHoles();
}

function setupHoles() {
    holes = [];
    const spacing = canvas.width / (GRID_SIZE + 1);
    
    for (let row = 0; row < GRID_SIZE; row++) {
        for (let col = 0; col < GRID_SIZE; col++) {
            holes.push({
                x: spacing * (col + 1),
                y: spacing * (row + 1),
                hasMole: false,
                moleHit: false
            });
        }
    }
}

function drawHoles() {
    holes.forEach(hole => {
        ctx.fillStyle = '#2a2a2a';
        ctx.beginPath();
        ctx.arc(hole.x, hole.y, HOLE_RADIUS, 0, Math.PI * 2);
        ctx.fill();

        ctx.strokeStyle = '#1a1a1a';
        ctx.lineWidth = 3;
        ctx.stroke();
    });
}

function drawMole(hole) {
    if (!hole.hasMole) return;
    
    const x = hole.x;
    const y = hole.y;

    ctx.fillStyle = hole.moleHit ? '#8B4513' : '#6B3410';
    ctx.beginPath();
    ctx.arc(x, y, MOLE_RADIUS, 0, Math.PI * 2);
    ctx.fill();

    ctx.strokeStyle = '#4a2508';
    ctx.lineWidth = 2;
    ctx.stroke();

    ctx.fillStyle = '#000';
    ctx.beginPath();
    ctx.arc(x - 8, y - 5, 3, 0, Math.PI * 2);
    ctx.arc(x + 8, y - 5, 3, 0, Math.PI * 2);
    ctx.fill();

    ctx.fillStyle = '#D2691E';
    ctx.beginPath();
    ctx.arc(x, y + 3, 4, 0, Math.PI * 2);
    ctx.fill();

    if (hole.moleHit) {
        ctx.strokeStyle = '#ff0000';
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.moveTo(x - 10, y - 10);
        ctx.lineTo(x + 10, y + 10);
        ctx.moveTo(x + 10, y - 10);
        ctx.lineTo(x - 10, y + 10);
        ctx.stroke();
    }
}

function drawScore() {
    ctx.fillStyle = '#333';
    ctx.font = 'bold 24px Quicksand, sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('Punteggio: ' + score, 20, 35);
}

function drawGameOver() {
    ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.fillStyle = '#fff';
    ctx.font = 'bold 36px Quicksand, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('GAME OVER!', canvas.width / 2, canvas.height / 2 - 40);
    
    // Punteggio finale
    ctx.font = 'bold 28px Quicksand, sans-serif';
    ctx.fillStyle = '#4D7D72';
    ctx.fillText('Punteggio: ' + score, canvas.width / 2, canvas.height / 2 + 10);

    ctx.font = '18px Quicksand, sans-serif';
    ctx.fillStyle = '#E49A7D';
    ctx.fillText('Clicca per ricominciare', canvas.width / 2, canvas.height / 2 + 60);
}

function render() {
    ctx.fillStyle = '#f4f4f4';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    drawHoles();
    holes.forEach(hole => drawMole(hole));
    drawScore();
    
    if (gameOver) {
        drawGameOver();
    }
}

function spawnMole() {
    if (gameOver) return;

    const freeHoles = holes.filter(h => !h.hasMole);
    if (freeHoles.length === 0) return;

    const hole = freeHoles[Math.floor(Math.random() * freeHoles.length)];
    hole.hasMole = true;
    hole.moleHit = false;

    const stayTime = MIN_MOLE_TIME + Math.random() * (MAX_MOLE_TIME - MIN_MOLE_TIME);

    setTimeout(() => {
        hole.hasMole = false;
        hole.moleHit = false;
        render();
    }, stayTime);
    
    render();

    activeTimeout = setTimeout(spawnMole, SPAWN_DELAY);
}

function handleClick(event) {
    if (gameOver) {
        resetGame();
        return;
    }

    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const x = (event.clientX - rect.left) * scaleX;
    const y = (event.clientY - rect.top) * scaleY;

    for (let hole of holes) {
        if (hole.hasMole && !hole.moleHit) {
            const dx = x - hole.x;
            const dy = y - hole.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance <= MOLE_RADIUS) {
                hole.moleHit = true;
                score++;

                setTimeout(() => {
                    hole.hasMole = false;
                    hole.moleHit = false;
                    render();
                }, 200);
                
                render();

                if (score >= WINNING_SCORE) {
                    endGame();
                }
                break;
            }
        }
    }
}

function endGame() {
    gameOver = true;
    if (activeTimeout) {
        clearTimeout(activeTimeout);
    }
    holes.forEach(h => {
        h.hasMole = false;
        h.moleHit = false;
    });
    render();
}

function resetGame() {
    score = 0;
    gameOver = false;
    holes.forEach(h => {
        h.hasMole = false;
        h.moleHit = false;
    });
    render();
    spawnMole();
}

function init() {
    resizeCanvas();
    window.addEventListener('resize', () => {
        resizeCanvas();
        render();
    });
    
    canvas.addEventListener('click', handleClick);
    
    render();
    spawnMole();
}

init();