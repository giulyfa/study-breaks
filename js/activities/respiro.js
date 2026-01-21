const cvs = document.getElementById("gameCanvas");
const ctx = cvs ? cvs.getContext("2d") : null;

const DURATA_FASE = 4000;
const NUM_FASI = 32;
const DURATA_TOTALE = DURATA_FASE * NUM_FASI; 

const fasi = [
    { testo: "INSPIRA", colore: "#8EBAA3", start: 0, end: 1 },   
    { testo: "TRATTIENI", colore: "#69A297", start: 1, end: 1 }, 
    { testo: "ESPIRA", colore: "#E49A7D", start: 1, end: 0 },   
    { testo: "TRATTIENI", colore: "#D17A55", start: 0, end: 0 }      
];

let tempoInizioEsercizio = Date.now();
let tempoInizioFase = Date.now();
let numeroFase = 0;
let indiceFase = 0;
let animazioneId;
let isRunning = true;

function cicloRespiro() {
    if (!isRunning) return;

    const ora = Date.now();
    const tempoTotale = ora - tempoInizioEsercizio;
    const tempoFase = ora - tempoInizioFase;

    if (tempoFase >= DURATA_FASE) {
        tempoInizioFase = ora;
        indiceFase = (indiceFase + 1) % fasi.length;
        numeroFase++;
    }

    if (numeroFase >= NUM_FASI) {
        mostraSchermataFinale();
        return;
    }

    disegna(tempoFase, tempoTotale);
    animazioneId = requestAnimationFrame(cicloRespiro);
}

function disegna(tempoFase, tempoTotale) {
    ctx.clearRect(0, 0, cvs.width, cvs.height);
    ctx.fillStyle = "#faf6ee"; 
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    const fase = fasi[indiceFase];
    const progresso = Math.min(tempoFase / DURATA_FASE, 1);

    const raggioBase = 70;
    const delta = 40;

    const currentScale = fase.start + (fase.end - fase.start) * progresso;
    const raggioAttuale = raggioBase + (delta * currentScale);

    ctx.beginPath();
    ctx.arc(cvs.width / 2, cvs.height / 2, raggioAttuale, 0, Math.PI * 2);

    ctx.shadowColor = fase.colore;
    ctx.shadowBlur = 20;
    
    ctx.fillStyle = fase.colore;
    ctx.globalAlpha = 0.3;
    ctx.fill();

    ctx.globalAlpha = 1.0; 
    ctx.strokeStyle = fase.colore;
    ctx.lineWidth = 4;
    ctx.stroke();

    ctx.shadowBlur = 0;

    ctx.fillStyle = "#274c43";
    ctx.font = "bold 26px Quicksand";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(fase.testo, cvs.width / 2, cvs.height / 2);

    const barraW = 280;
    const barraH = 10;
    const barraX = (cvs.width - barraW) / 2;
    const barraY = cvs.height - 60;

    ctx.beginPath();
    ctx.lineCap = "round";
    ctx.lineWidth = barraH;
    ctx.strokeStyle = "#e0e0e0";
    ctx.moveTo(barraX, barraY);
    ctx.lineTo(barraX + barraW, barraY);
    ctx.stroke();

    ctx.beginPath();
    ctx.strokeStyle = fase.colore;
    ctx.lineWidth = barraH;
    ctx.moveTo(barraX, barraY);
    const lunghezzaBarra = Math.max(0.1, barraW * progresso);
    ctx.lineTo(barraX + lunghezzaBarra, barraY);
    ctx.stroke();

    const secRimanenti = Math.ceil((DURATA_TOTALE - tempoTotale) / 1000);
    ctx.fillStyle = "#69A297";
    ctx.font = "bold 16px Quicksand";
    ctx.fillText(`TEMPO RIMASTO: ${Math.floor(secRimanenti / 60)}:${(secRimanenti % 60).toString().padStart(2, '0')}`, cvs.width / 2, 40);
}

function mostraSchermataFinale() {
    isRunning = false;

    ctx.fillStyle = "rgba(0,0,0,0.7)";
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    ctx.fillStyle = "#ffffff";
    ctx.font = "bold 28px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Rilassamento completato", cvs.width / 2, cvs.height / 2 - 25);

    ctx.font = "bold 18px Quicksand";
    ctx.fillStyle = "#E49A7D";
    ctx.fillText("Clicca per ricominciare", cvs.width / 2, cvs.height / 2 + 70);

    function riavviaHandler() {
        cvs.removeEventListener("click", riavviaHandler);
        tempoInizioEsercizio = Date.now();
        tempoInizioFase = Date.now();
        indiceFase = 0;
        isRunning = true;
        cicloRespiro();
    }
    cvs.addEventListener("click", riavviaHandler);
}

cicloRespiro();