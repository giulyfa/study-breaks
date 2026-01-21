const cvs = document.getElementById("gameCanvas");
const ctx = cvs ? cvs.getContext("2d") : null;

if (!ctx) {
    console.error("Canvas non trovato!");
} else {

    const DURATA_POSA = 30000;

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

    let indicePosa = 0;
    let tempoInizioPosa = Date.now();
    let isRunning = true;
    let animazioneId;

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

    function cicloYoga() {
        if (!isRunning) return;

        const ora = Date.now();
        const tempoTrascorso = ora - tempoInizioPosa;

        if (tempoTrascorso >= DURATA_POSA) {
            indicePosa++;
            tempoInizioPosa = ora; 

            if (indicePosa >= pose.length) {
                mostraSchermataFinale();
                return;
            }
        }

        disegna(tempoTrascorso);
        animazioneId = requestAnimationFrame(cicloYoga);
    }

    function disegna(tempoTrascorso) {
        ctx.clearRect(0, 0, cvs.width, cvs.height);

        const gradient = ctx.createLinearGradient(0, 0, 0, cvs.height);
        gradient.addColorStop(0, "#ECE7D9");
        gradient.addColorStop(1, "#faf6ee");
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, cvs.width, cvs.height);

        const posaCorrente = pose[indicePosa];
        const percentuale = Math.min(tempoTrascorso / DURATA_POSA, 1);
        const secondiRimanenti = Math.ceil((DURATA_POSA - tempoTrascorso) / 1000);

        ctx.fillStyle = "#69A297";
        ctx.beginPath();
        ctx.roundRect(cvs.width / 2 - 100, 15, 200, 35, 20);
        ctx.fill();
        
        ctx.fillStyle = "#ffffff";
        ctx.font = "bold 14px Quicksand";
        ctx.textAlign = "center";
        ctx.fillText(`Esercizio ${indicePosa + 1} di ${pose.length}`, cvs.width / 2, 37);

        ctx.fillStyle = "#4D7D72"; 
        ctx.font = "bold 24px Quicksand";
        ctx.fillText(posaCorrente.titolo, cvs.width / 2, 105);

        ctx.fillStyle = "#555";
        ctx.font = "17px Quicksand";
        wrapText(ctx, posaCorrente.descrizione, cvs.width / 2, 170, cvs.width - 60, 24);

        const centerX = cvs.width / 2;
        const centerY = 350;
        const radius = 55;

        ctx.shadowColor = "rgba(0, 0, 0, 0.15)";
        ctx.shadowBlur = 15;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 5;

        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.lineWidth = 12;
        ctx.strokeStyle = "#E2DDD1";
        ctx.stroke();

        ctx.shadowColor = "transparent";
        ctx.shadowBlur = 0;

        ctx.beginPath();
        const endAngle = (2 * Math.PI) * (1 - percentuale) - (0.5 * Math.PI);
        ctx.arc(centerX, centerY, radius, -0.5 * Math.PI, endAngle, false); 
        ctx.strokeStyle = "#E49A7D"; 
        ctx.lineCap = "round";
        ctx.stroke();

        ctx.fillStyle = "#4D7D72";
        ctx.font = "bold 32px Quicksand";
        ctx.textBaseline = "middle"; 
        ctx.fillText(secondiRimanenti, centerX, centerY);
        ctx.textBaseline = "alphabetic"; 

        const barraHeight = 8;
        const barraY = cvs.height - barraHeight - 5;
        
        ctx.fillStyle = "#E2DDD1";
        ctx.beginPath();
        ctx.roundRect(10, barraY, cvs.width - 20, barraHeight, 4);
        ctx.fill();

        const progressoTotale = ((indicePosa * DURATA_POSA) + tempoTrascorso) / (pose.length * DURATA_POSA);
        ctx.fillStyle = "#69A297"; 
        ctx.beginPath();
        ctx.roundRect(10, barraY, (cvs.width - 20) * progressoTotale, barraHeight, 4);
        ctx.fill();
    }

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

    cicloYoga();
}