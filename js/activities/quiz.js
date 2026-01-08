const cvs = document.getElementById("gameCanvas");
const ctx = cvs.getContext("2d");

// --- 1. SET DI DOMANDE ---
const databaseDomande = [
    { domanda: "Qual è la capitale dell'Italia?", opzioni: ["Milano", "Roma", "Firenze", "Napoli"], corretta: 1 },
    { domanda: "Qual è l'oceano più grande del mondo?", opzioni: ["Atlantico", "Indiano", "Pacifico", "Artico"], corretta: 2 },
    { domanda: "Quanti sono i nani di Biancaneve?", opzioni: ["5", "6", "7", "8"], corretta: 2 },
    { domanda: "Quale gas respiriamo per vivere?", opzioni: ["Azoto", "Anidride Carbonica", "Idrogeno", "Ossigeno"], corretta: 3 },
    { domanda: "Chi ha scritto 'I Promessi Sposi'?", opzioni: ["Giacomo Leopardi", "Alessandro Manzoni", "Dante Alighieri", "Giovanni Boccaccio"], corretta: 1 },
    { domanda: "In quale continente si trova il deserto del Sahara?", opzioni: ["Asia", "America", "Africa", "Australia"], corretta: 2 },
    { domanda: "Qual è il metallo il cui simbolo chimico è Au?", opzioni: ["Argento", "Rame", "Ferro", "Oro"], corretta: 3 },
    { domanda: "In che anno è caduto il muro di Berlino?", opzioni: ["1985", "1989", "1991", "1995"], corretta: 1 },
    { domanda: "Qual è il colore ottenuto mescolando blu e giallo?", opzioni: ["Verde", "Arancione", "Viola", "Marrone"], corretta: 0 },
    { domanda: "Quanti lati ha un esagono?", opzioni: ["5", "6", "7", "8"], corretta: 1 },
    { domanda: "Chi ha scoperto l'America nel 1492?", opzioni: ["Amerigo Vespucci", "Marco Polo", "Cristoforo Colombo", "Vasco da Gama"], corretta: 2 },
    { domanda: "Qual è il pianeta più grande del sistema solare?", opzioni: ["Terra", "Marte", "Saturno", "Giove"], corretta: 3 },
    { domanda: "Qual è la capitale del Giappone?", opzioni: ["Pechino", "Seul", "Tokyo", "Bangkok"], corretta: 2 },
    { domanda: "Chi ha dipinto la Cappella Sistina?", opzioni: ["Leonardo", "Raffaello", "Michelangelo", "Donatello"], corretta: 2 },
    { domanda: "Qual è la formula chimica dell'acqua?", opzioni: ["CO2", "H2O", "O2", "NaCl"], corretta: 1 },
    { domanda: "Qual è l'animale terrestre più veloce?", opzioni: ["Leone", "Ghepardo", "Cavallo", "Antilope"], corretta: 1 },
    { domanda: "Quanti giorni ci sono in un anno bisestile?", opzioni: ["364", "365", "366", "367"], corretta: 2 },
    { domanda: "Qual è lo strumento musicale a fiato più piccolo?", opzioni: ["Flauto", "Clarinetto", "Ottavino", "Tromba"], corretta: 2 },
    { domanda: "Chi ha inventato la lampadina?", opzioni: ["Nikola Tesla", "Albert Einstein", "Thomas Edison", "Alexander Bell"], corretta: 2 },
    { domanda: "In quale città si trova la Torre Eiffel?", opzioni: ["Londra", "Parigi", "Berlino", "Madrid"], corretta: 1 },
    { domanda: "Qual è il vulcano che distrusse Pompei?", opzioni: ["Etna", "Vesuvio", "Stromboli", "Vulcano"], corretta: 1 },
    { domanda: "Quante corde ha un violino?", opzioni: ["3", "4", "5", "6"], corretta: 1 },
    { domanda: "Qual è il mammifero più grande del mondo?", opzioni: ["Elefante", "Balenottera Azzurra", "Squalo Balena", "Giraffa"], corretta: 1 },
    { domanda: "Chi è l'autore di 'Piccolo Principe'?", opzioni: ["Saint-Exupéry", "Jules Verne", "Charles Perrault", "Carlo Collodi"], corretta: 0 },
    { domanda: "Qual è la capitale della Spagna?", opzioni: ["Barcellona", "Siviglia", "Valencia", "Madrid"], corretta: 3 },
    { domanda: "Quale pianeta è noto come il 'Pianeta Rosso'?", opzioni: ["Venere", "Marte", "Saturno", "Mercurio"], corretta: 1 },
    { domanda: "Quanti sono i continenti sulla Terra?", opzioni: ["5", "6", "7", "8"], corretta: 2 },
    { domanda: "Chi ha vinto i Mondiali di Calcio nel 2006?", opzioni: ["Francia", "Germania", "Brasile", "Italia"], corretta: 3 },
    { domanda: "Qual è l'osso più lungo del corpo umano?", opzioni: ["Omero", "Femore", "Tibia", "Radio"], corretta: 1 },
    { domanda: "Chi scrisse 'Romeo e Giulietta'?", opzioni: ["Dante", "Shakespeare", "Molière", "Homer"], corretta: 1 },
    { domanda: "Qual è la lingua più parlata al mondo?", opzioni: ["Inglese", "Spagnolo", "Cinese Mandarino", "Hindi"], corretta: 2 },
    { domanda: "In che anno è iniziata la Seconda Guerra Mondiale?", opzioni: ["1914", "1918", "1939", "1945"], corretta: 2 },
    { domanda: "Qual è la capitale della Germania?", opzioni: ["Monaco", "Amburgo", "Francoforte", "Berlino"], corretta: 3 },
    { domanda: "Qual è il metallo liquido a temperatura ambiente?", opzioni: ["Ferro", "Mercurio", "Piombo", "Zinco"], corretta: 1 },
    { domanda: "Chi è il dio del mare nella mitologia greca?", opzioni: ["Zeus", "Ares", "Poseidone", "Apollo"], corretta: 2 },
    { domanda: "Qual è l'organo più esteso del corpo umano?", opzioni: ["Cuore", "Fegato", "Pelle", "Polmoni"], corretta: 2 },
    { domanda: "Quanti colori ha l'arcobaleno?", opzioni: ["5", "6", "7", "8"], corretta: 2 },
    { domanda: "In quale nazione si trovano le Piramidi di Giza?", opzioni: ["Grecia", "Messico", "Egitto", "Perù"], corretta: 2 },
    { domanda: "Qual è il monte più alto della Terra?", opzioni: ["K2", "Monte Bianco", "Everest", "Kilimangiaro"], corretta: 2 },
    { domanda: "Chi ha dipinto 'L'Ultima Cena'?", opzioni: ["Michelangelo", "Raffaello", "Caravaggio", "Leonardo da Vinci"], corretta: 3 },
    { domanda: "Qual è la capitale del Regno Unito?", opzioni: ["Liverpool", "Manchester", "Londra", "Edimburgo"], corretta: 2 },
    { domanda: "Quale parte del corpo produce l'insulina?", opzioni: ["Fegato", "Pancreas", "Reni", "Cuore"], corretta: 1 },
    { domanda: "Chi è considerato il padre della lingua italiana?", opzioni: ["Francesco Petrarca", "Giovanni Boccaccio", "Dante Alighieri", "Ugo Foscolo"], corretta: 2 },
    { domanda: "Quanti secondi ci sono in un minuto?", opzioni: ["30", "50", "60", "100"], corretta: 2 },
    { domanda: "Quale città italiana è famosa per i suoi canali?", opzioni: ["Firenze", "Venezia", "Roma", "Genova"], corretta: 1 },
    { domanda: "Qual è il frutto che ha i semi all'esterno?", opzioni: ["Mela", "Fragola", "Pera", "Banana"], corretta: 1 },
    { domanda: "Chi ha scritto 'Odissea'?", opzioni: ["Virgilio", "Omero", "Aristotele", "Platone"], corretta: 1 },
    { domanda: "Qual è il deserto più freddo del mondo?", opzioni: ["Gobi", "Sahara", "Antartide", "Atacama"], corretta: 2 },
    { domanda: "Quale animale è il simbolo della pace?", opzioni: ["Aquila", "Leone", "Colomba", "Delfino"], corretta: 2 },
    { domanda: "In quale anno l'uomo è sbarcato sulla Luna?", opzioni: ["1965", "1969", "1972", "1975"], corretta: 1 }
];

let domandeSessione = [];
let indiceDomanda = 0;
let score = 0;
let giocoFinito = false;
let mostrandoFeedback = false;
let feedbackCorretto = false;

let tempoRimanente = 30;
let intervalloTimer;

function initQuiz() {
    giocoFinito = false;
    mostrandoFeedback = false;
    score = 0;
    indiceDomanda = 0;
    // Seleziona 6 domande casuali dal database
    domandeSessione = [...databaseDomande].sort(() => 0.5 - Math.random()).slice(0, 6);
    prossimaDomanda();
}

function prossimaDomanda() {
    if (indiceDomanda < domandeSessione.length) {
        mostrandoFeedback = false;
        tempoRimanente = 30;
        resetTimer();
        draw();
    } else {
        giocoFinito = true;
        clearInterval(intervalloTimer);
        draw();
    }
}

function resetTimer() {
    clearInterval(intervalloTimer);
    intervalloTimer = setInterval(() => {
        tempoRimanente--;
        if (tempoRimanente <= 0) {
            gestisciRisposta(-1); // Tempo scaduto (risposta errata)
        }
        draw();
    }, 1000);
}

function gestisciRisposta(indiceScelto) {
    if (mostrandoFeedback) return;

    clearInterval(intervalloTimer);
    const domandaAttuale = domandeSessione[indiceDomanda];
    feedbackCorretto = (indiceScelto === domandaAttuale.corretta);
    
    if (feedbackCorretto) score++;
    
    mostrandoFeedback = true;
    draw();

    setTimeout(() => {
        indiceDomanda++;
        prossimaDomanda();
    }, 1500);
}

// --- 4. DISEGNO (RENDER) ---
//da modificare perchè non è molto bello
function draw() {
    // Sfondo
    ctx.fillStyle = "#274c43"; 
    ctx.fillRect(0, 0, cvs.width, cvs.height);

    if (giocoFinito) {
        showGameOver();
        return;
    }

    const q = domandeSessione[indiceDomanda];

    // Info testata (Domanda X/6 e Score)
    ctx.fillStyle = "white";
    ctx.font = "14px Quicksand";
    ctx.textAlign = "left";
    ctx.fillText(`Domanda: ${indiceDomanda + 1}/6`, 15, 25);
    ctx.textAlign = "right";
    ctx.fillText(`Punti: ${score}`, cvs.width - 15, 25);

    // Timer (Barra)
    ctx.fillStyle = tempoRimanente < 10 ? "#E49A7D" : "#8EBAA3";
    ctx.fillRect(0, 40, (cvs.width * tempoRimanente) / 30, 5);

    // Testo Domanda
    ctx.fillStyle = "white";
    ctx.font = "20px Quicksand";
    ctx.textAlign = "center";
    wrapText(ctx, q.domanda, cvs.width / 2, 85, cvs.width - 40, 25);

    // Opzioni
    q.opzioni.forEach((opt, i) => {
        const yPos = 160 + (i * 55);
        
        // Colore bottone basato sul feedback
        ctx.fillStyle = "#69A297"; 
        if (mostrandoFeedback) {
            if (i === q.corretta) ctx.fillStyle = "#8EBAA3"; // Verde (Corretta)
            else ctx.fillStyle = "#E49A7D"; // Arancio (Errata)
        }

        ctx.fillRect(40, yPos, cvs.width - 80, 45);
        ctx.strokeStyle = "white";
        ctx.strokeRect(40, yPos, cvs.width - 80, 45);

        ctx.fillStyle = "white";
        ctx.font = "16px Quicksand";
        ctx.fillText(opt, cvs.width / 2, yPos + 28);
    });

    // Messaggio Feedback
    if (mostrandoFeedback) {
        ctx.fillStyle = feedbackCorretto ? "#8EBAA3" : "#E49A7D";
        ctx.font = "bold 18px Quicksand";
        const msg = feedbackCorretto ? "CORRETTO!" : `SBAGLIATO! La corretta era: ${q.opzioni[q.corretta]}`;
        ctx.fillText(msg, cvs.width / 2, cvs.height - 20);
    }
}

cvs.addEventListener("click", (e) => {
    if (giocoFinito) { initQuiz(); return; }
    if (mostrandoFeedback) return;

    const rect = cvs.getBoundingClientRect();
    const mx = e.clientX - rect.left;
    const my = e.clientY - rect.top;

    for(let i=0; i<4; i++) {
        const yPos = 160 + (i * 55);
        if (mx >= 40 && mx <= cvs.width-40 && my >= yPos && my <= yPos+45) {
            gestisciRisposta(i);
            break;
        }
    }
});

function showGameOver() {
    ctx.fillStyle = "rgba(0,0,0,0.8)";
    ctx.fillRect(0, 0, cvs.width, cvs.height);
    ctx.fillStyle = "white";
    ctx.font = "30px Quicksand";
    ctx.textAlign = "center";
    ctx.fillText("Quiz Terminato!", cvs.width/2, cvs.height/2 - 20);
    ctx.font = "22px Quicksand";
    ctx.fillText(`Punteggio: ${score} su 6`, cvs.width/2, cvs.height/2 + 20);
    ctx.fillStyle = "#E49A7D";
    ctx.fillText("Clicca per rigiocare", cvs.width/2, cvs.height/2 + 70);
}

function wrapText(context, text, x, y, maxWidth, lineHeight) {
    let words = text.split(' '), line = '';
    for(let n = 0; n < words.length; n++) {
        let testLine = line + words[n] + ' ';
        if (context.measureText(testLine).width > maxWidth && n > 0) {
            context.fillText(line, x, y);
            line = words[n] + ' '; y += lineHeight;
        } else line = testLine;
    }
    context.fillText(line, x, y);
}

initQuiz();