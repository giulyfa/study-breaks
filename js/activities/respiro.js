const cvs = document.getElementById("gameCanvas");
const ctx = cvs ? cvs.getContext("2d") : null;

// --- 1. CONFIGURAZIONE ---
const DURATA_FASE = 4000; 
const DURATA_TOTALE = 120000; 

// Aggiornati i colori per matchare style.css
// #8EBAA3 (Verde Attività), #69A297 (Verde Header), #E49A7D (Accento Peach), #CCE2DC (Verde Chiaro Footer)
const fasi = [
    { testo: "INSPIRA", colore: "#8EBAA3", start: 0, end: 1 },   
    { testo: "TRATTIENI", colore: "#69A297", start: 1, end: 1 }, 
    { testo: "ESPIRA", colore: "#E49A7D", start: 1, end: 0 },   
    { testo: "TRATTIENI", colore: "#D17A55", start: 0, end: 0 }      
];

// --- 2. VARIABILI ---
let tempoInizioEsercizio = Date.now();
let tempoInizioFase = Date.now();
let indiceFase = 0;
let animazioneId;
let isRunning = true;

// --- 3. LOOP DI GIOCO ---
function cicloRespiro() {
    if (!isRunning) return;

    const ora = Date.now();
    const tempoTotale = ora - tempoInizioEsercizio;
    const tempoFase = ora - tempoInizioFase;

    if (tempoTotale >= DURATA_TOTALE) {
        mostraSchermataFinale();
        return;
    }

    if (tempoFase >= DURATA_FASE) {
        tempoInizioFase = ora;
        indiceFase = (indiceFase + 1) % fasi.length;
    }

    disegna(tempoFase, tempoTotale);
    animazioneId = requestAnimationFrame(cicloRespiro);
}

// --- 4. FUNZIONE DI DISEGNO AGGIORNATA ---
function disegna(tempoFase, tempoTotale) {
    // Pulisci e imposta lo sfondo color crema (come .welcome-card in CSS)
    ctx.clearRect(0, 0, cvs.width, cvs.height);
    ctx.fillStyle = "#faf6ee"; 
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    const fase = fasi[indiceFase];
    const progresso = Math.min(tempoFase / DURATA_FASE, 1);

    // --- CALCOLO RAGGIO FLUIDO ---
    const raggioBase = 70;
    const delta = 40; // Espansione massima
    
    // Easing function per rendere il movimento più naturale (sinusoidale)
    // Invece di lineare, usiamo una curva morbida
    const currentScale = fase.start + (fase.end - fase.start) * progresso;
    const raggioAttuale = raggioBase + (delta * currentScale);
    
    // --- CERCHIO RESPIRAZIONE ---
    ctx.beginPath();
    ctx.arc(cvs.width / 2, cvs.height / 2, raggioAttuale, 0, Math.PI * 2);
    
    // Ombra/Glow per effetto "soft"
    ctx.shadowColor = fase.colore;
    ctx.shadowBlur = 20;
    
    ctx.fillStyle = fase.colore;
    ctx.globalAlpha = 0.3; // Trasparenza più leggera
    ctx.fill();
    
    // Bordo
    ctx.globalAlpha = 1.0; 
    ctx.strokeStyle = fase.colore;
    ctx.lineWidth = 4;
    ctx.stroke();
    
    // Reset ombra per il testo
    ctx.shadowBlur = 0;

    // --- TESTO CENTRALE ---
    ctx.fillStyle = "#274c43"; // Colore scuro primario del tema
    ctx.font = "bold 26px Quicksand"; // Font leggermente più grande
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(fase.testo, cvs.width / 2, cvs.height / 2);

    // --- BARRA PROGRESSO STILE PILLOLA ---
    const barraW = 280;
    const barraH = 10; // Spessore barra
    const barraX = (cvs.width - barraW) / 2;
    const barraY = cvs.height - 60;

    // Sfondo Barra (Grigio chiaro caldo)
    ctx.beginPath();
    ctx.lineCap = "round"; // Bordi arrotondati
    ctx.lineWidth = barraH;
    ctx.strokeStyle = "#e0e0e0";
    ctx.moveTo(barraX, barraY);
    ctx.lineTo(barraX + barraW, barraY);
    ctx.stroke();

    // Riempimento Barra (Colore fase attuale)
    ctx.beginPath();
    ctx.strokeStyle = fase.colore;
    ctx.lineWidth = barraH;
    ctx.moveTo(barraX, barraY);
    // Assicura che la linea abbia una lunghezza minima per evitare glitch grafici a 0
    const lunghezzaBarra = Math.max(0.1, barraW * progresso);
    ctx.lineTo(barraX + lunghezzaBarra, barraY);
    ctx.stroke();

    // --- TIMER (Stile pulito) ---
    const secRimanenti = Math.ceil((DURATA_TOTALE - tempoTotale) / 1000);
    ctx.fillStyle = "#69A297"; // Verde medio (Header color)
    ctx.font = "bold 16px Quicksand";
    ctx.fillText(`TEMPO RIMASTO: ${Math.floor(secRimanenti / 60)}:${(secRimanenti % 60).toString().padStart(2, '0')}`, cvs.width / 2, 40);
}

// --- SCHERMATA FINALE AGGIORNATA ---
function mostraSchermataFinale() {
    isRunning = false;
    
    // Sfondo crema
    ctx.fillStyle = "#faf6ee";
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    // Testo principale
    ctx.fillStyle = "#274c43";
    ctx.font = "bold 28px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Rilassamento completato", cvs.width / 2, cvs.height / 2 - 25);

    // Icona o elemento decorativo semplice (un cerchio statico)
    ctx.beginPath();
    ctx.arc(cvs.width / 2, cvs.height / 2 + 30, 10, 0, Math.PI * 2);
    ctx.fillStyle = "#E49A7D";
    ctx.fill();

    // Testo secondario
    ctx.font = "bold 18px Quicksand";
    ctx.fillStyle = "#8EBAA3"; // Verde attività
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