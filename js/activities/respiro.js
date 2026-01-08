const cvs = document.getElementById("gameCanvas");
const ctx = cvs ? cvs.getContext("2d") : null;

if (!ctx) {
    console.error("Canvas non trovato!");
} else {

    // --- 1. CONFIGURAZIONE ---
    const DURATA_FASE = 4000; 
    const DURATA_TOTALE = 120000; 
    
    const fasi = [
        { testo: "INSPIRA", colore: "#8EBAA3", start: 0, end: 1 },   
        { testo: "TRATTIENI", colore: "#69A297", start: 1, end: 1 }, 
        { testo: "ESPIRA", colore: "#E49A7D", start: 1, end: 0 },   
        { testo: "VUOTO", colore: "#B0C4DE", start: 0, end: 0 }      
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

    // --- 4. FUNZIONE DI DISEGNO ---
    function disegna(tempoFase, tempoTotale) {
        ctx.clearRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, cvs.width, cvs.height);

        const fase = fasi[indiceFase];
        const progresso = Math.min(tempoFase / DURATA_FASE, 1);

        // --- CALCOLO RAGGIO FLUIDO ---
        const raggioBase = 70;
        const delta = 40; // Espansione massima
        
        const currentScale = fase.start + (fase.end - fase.start) * progresso;
        
        const raggioAttuale = raggioBase + (delta * currentScale);
        
        // Riempimento Cerchio
        ctx.beginPath();
        ctx.arc(cvs.width / 2, cvs.height / 2, raggioAttuale, 0, Math.PI * 2);
        ctx.fillStyle = fase.colore;
        ctx.globalAlpha = 0.4; 
        ctx.fill();
        ctx.globalAlpha = 1.0; 
        
        // Cerchio Bordo
        ctx.strokeStyle = fase.colore;
        ctx.lineWidth = 3;
        ctx.stroke();

        // Testo
        ctx.fillStyle = "#274c43";
        ctx.font = "bold 24px Quicksand";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText(fase.testo, cvs.width / 2, cvs.height / 2);

        // Barra Progresso
        const barraW = 300;
        const barraH = 8;
        const barraX = (cvs.width - barraW) / 2;
        const barraY = cvs.height - 50;

        ctx.fillStyle = "#eeeeee";
        ctx.fillRect(barraX, barraY, barraW, barraH);
        ctx.fillStyle = fase.colore;
        ctx.fillRect(barraX, barraY, barraW * progresso, barraH);

        // Timer
        const secRimanenti = Math.ceil((DURATA_TOTALE - tempoTotale) / 1000);
        ctx.fillStyle = "#888";
        ctx.font = "14px Quicksand";
        ctx.fillText(`Tempo: ${Math.floor(secRimanenti / 60)}:${(secRimanenti % 60).toString().padStart(2, '0')}`, cvs.width / 2, 30);
    }

    function mostraSchermataFinale() {
        isRunning = false;
        ctx.fillStyle = "#fff";
        ctx.fillRect(0, 0, cvs.width, cvs.height);

        ctx.fillStyle = "#274c43";
        ctx.font = "bold 26px Quicksand";
        ctx.textAlign = "center";
        ctx.fillText("Rilassamento completato", cvs.width / 2, cvs.height / 2 - 20);

        ctx.font = "18px Quicksand";
        ctx.fillStyle = "#E49A7D";
        ctx.fillText("Clicca per ricominciare", cvs.width / 2, cvs.height / 2 + 30);

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
}