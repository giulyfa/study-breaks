/*
 * YOGA & STRETCHING - Study Breaks
 * Attività guidata completa (incluso gambe) - 10 Esercizi da 30s
 */

const cvs = document.getElementById("gameCanvas");
const ctx = cvs ? cvs.getContext("2d") : null;

if (!ctx) {
    console.error("Canvas non trovato!");
} else {

    // --- 1. CONFIGURAZIONE E DATI ---
    const DURATA_POSA = 30000; // 30 secondi in millisecondi
    
    // Lista di 10 esercizi (Total Body da ufficio)
    const pose = [
        {
            titolo: "1. Rotazione del Collo",
            descrizione: "Inizia rilassando le spalle. Ruota lentamente la testa in senso orario facendo cerchi ampi, poi cambia senso. Respira profondamente."
        },
        {
            titolo: "2. Apertura Spalle",
            descrizione: "Intreccia le dita dietro la schiena. Stendi le braccia allontanandole dal corpo aprendo il petto. Guarda leggermente in alto."
        },
        {
            titolo: "3. Torsione Spinale",
            descrizione: "Mano sinistra sul ginocchio destro. Ruota il busto verso destra guardando oltre la spalla. Tieni la schiena dritta. (Poi cambia lato)."
        },
        {
            titolo: "4. Allungamento Laterale",
            descrizione: "Alza un braccio sopra la testa e piegati verso il lato opposto. Senti l'allungamento sul fianco. Tieni i glutei ben appoggiati alla sedia."
        },
        {
            titolo: "5. Estensione Gambe",
            descrizione: "Seduto dritto, stendi una gamba in avanti mantenendola parallela al pavimento. Tieni per qualche secondo e alterna con l'altra."
        },
        {
            titolo: "6. Rotazione Caviglie",
            descrizione: "Solleva leggermente i piedi da terra. Ruota entrambe le caviglie prima in senso orario, poi in senso antiorario per sciogliere le articolazioni."
        },
        {
            titolo: "7. Allungamento Glutei",
            descrizione: "Appoggia la caviglia destra sul ginocchio sinistro. Piegati leggermente in avanti con il busto dritto finché senti tirare il gluteo."
        },
        {
            titolo: "8. Sollevamento Talloni",
            descrizione: "Tieni i piedi piatti a terra. Solleva i talloni premendo sulle punte (attiva i polpacci), poi riappoggiali. Ripeti ritmicamente."
        },
        {
            titolo: "9. Marcia da Seduti",
            descrizione: "Solleva alternativamente le ginocchia verso il petto come se stessi marciando, mantenendo la schiena dritta e gli addominali attivi."
        },
        {
            titolo: "10. Rilassamento Finale",
            descrizione: "Chiudi gli occhi. Appoggia le mani sulle gambe. Fai 3 respiri profondi: inspira dal naso, espira lentamente dalla bocca. Rilascia ogni tensione."
        }
    ];

    // --- 2. VARIABILI DI STATO ---
    let indicePosa = 0;
    let tempoInizioPosa = Date.now();
    let isRunning = true;
    let animazioneId;

    // --- 3. GESTIONE TESTO (Wrap Text) ---
    function wrapText(context, text, x, y, maxWidth, lineHeight) {
        let words = text.split(' ');
        let line = '';

        for(let n = 0; n < words.length; n++) {
            let testLine = line + words[n] + ' ';
            let metrics = context.measureText(testLine);
            let testWidth = metrics.width;
            if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                line = words[n] + ' ';
                y += lineHeight;
            } else {
                line = testLine;
            }
        }
        context.fillText(line, x, y);
    }

    // --- 4. LOOP PRINCIPALE ---
    function cicloYoga() {
        if (!isRunning) return;

        const ora = Date.now();
        const tempoTrascorso = ora - tempoInizioPosa;

        // Controllo fine tempo posa
        if (tempoTrascorso >= DURATA_POSA) {
            indicePosa++;
            tempoInizioPosa = ora; 

            // Fine di tutti gli esercizi
            if (indicePosa >= pose.length) {
                mostraSchermataFinale();
                return;
            }
        }

        disegna(tempoTrascorso);
        animazioneId = requestAnimationFrame(cicloYoga);
    }

    // --- 5. FUNZIONE DI DISEGNO ---
    function disegna(tempoTrascorso) {
        // Pulisci schermo
        ctx.clearRect(0, 0, cvs.width, cvs.height);
        
        // Sfondo chiaro
        ctx.fillStyle = "#ffffff"; 
        ctx.fillRect(0, 0, cvs.width, cvs.height);

        const posaCorrente = pose[indicePosa];
        
        // Calcoli
        const percentuale = Math.min(tempoTrascorso / DURATA_POSA, 1);
        const secondiRimanenti = Math.ceil((DURATA_POSA - tempoTrascorso) / 1000);

        // -- DISEGNO UI --

        // 1. Numero Posa
        ctx.fillStyle = "#8EBAA3";
        ctx.font = "bold 16px Quicksand";
        ctx.textAlign = "center";
        ctx.fillText(`Esercizio ${indicePosa + 1} di ${pose.length}`, cvs.width / 2, 40);

        // 2. Titolo Posa
        ctx.fillStyle = "#274c43"; 
        ctx.font = "bold 26px Quicksand";
        ctx.fillText(posaCorrente.titolo, cvs.width / 2, 80);

        // 3. Descrizione
        ctx.fillStyle = "#555";
        ctx.font = "18px Quicksand";
        wrapText(ctx, posaCorrente.descrizione, cvs.width / 2, 130, cvs.width - 60, 25);

        // 4. Timer Circolare
        const centerX = cvs.width / 2;
        const centerY = 280;
        const radius = 50;

        // Cerchio sfondo
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.lineWidth = 10;
        ctx.strokeStyle = "#eee";
        ctx.stroke();

        // Cerchio progresso (Arancione)
        ctx.beginPath();
        const endAngle = (2 * Math.PI) * (1 - percentuale) - (0.5 * Math.PI);
        ctx.arc(centerX, centerY, radius, -0.5 * Math.PI, endAngle, false); 
        ctx.strokeStyle = "#E49A7D"; 
        ctx.stroke();

        // Testo Secondi
        ctx.fillStyle = "#274c43";
        ctx.font = "bold 24px Quicksand";
        ctx.textBaseline = "middle"; 
        ctx.fillText(secondiRimanenti, centerX, centerY);
        ctx.textBaseline = "alphabetic"; 

        // 5. Barra di progresso totale (opzionale, sotto)
        const barraHeight = 6;
        ctx.fillStyle = "#eee";
        ctx.fillRect(0, cvs.height - barraHeight, cvs.width, barraHeight);
        
        // Calcolo progresso totale sessione
        const progressoTotale = ((indicePosa * DURATA_POSA) + tempoTrascorso) / (pose.length * DURATA_POSA);
        ctx.fillStyle = "#69A297"; 
        ctx.fillRect(0, cvs.height - barraHeight, cvs.width * progressoTotale, barraHeight);
    }

    // --- 6. SCHERMATA FINALE ---
    function mostraSchermataFinale() {
        isRunning = false;
        ctx.clearRect(0, 0, cvs.width, cvs.height);
        
        ctx.fillStyle = "#274c43";
        ctx.fillRect(0, 0, cvs.width, cvs.height);

        ctx.fillStyle = "white";
        ctx.font = "bold 32px Quicksand";
        ctx.textAlign = "center";
        ctx.fillText("Sessione Completata!", cvs.width / 2, cvs.height / 2 - 20);

        ctx.font = "20px Quicksand";
        ctx.fillText("Ben fatto! 💪", cvs.width / 2, cvs.height / 2 + 20);

        ctx.fillStyle = "#E49A7D";
        ctx.font = "16px Quicksand";
        ctx.fillText("Clicca per ricominciare", cvs.width / 2, cvs.height / 2 + 70);

        function riavviaHandler() {
            cvs.removeEventListener("click", riavviaHandler);
            indicePosa = 0;
            tempoInizioPosa = Date.now();
            isRunning = true;
            cicloYoga();
        }
        cvs.addEventListener("click", riavviaHandler);
    }

    // Avvio iniziale
    cicloYoga();
}