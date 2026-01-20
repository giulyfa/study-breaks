const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

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


let domande = [];
let indiceDomandaCorrente = 0;
let risposteCorrette = 0;
let gameOver = false;
let timer = 30;
let intervalId = null;
let rispostaSelezionata = null;
let mostraRisultato = false;
let opzioniRects = [];

function iniziaGioco() {
    domande = selezionaDomandeRandom(6);
    indiceDomandaCorrente = 0;
    risposteCorrette = 0;
    gameOver = false;
    timer = 30;
    rispostaSelezionata = null;
    mostraRisultato = false;
    
    mostraDomanda();
}

function selezionaDomandeRandom(n) {
    const shuffled = [...databaseDomande].sort(() => Math.random() - 0.5);
    return shuffled.slice(0, Math.min(n, shuffled.length));
}

function mostraDomanda() {
    if (indiceDomandaCorrente >= domande.length) {
        terminaGioco();
        return;
    }
    
    timer = 30;
    rispostaSelezionata = null;
    mostraRisultato = false;
    
    if (intervalId) clearInterval(intervalId);
    intervalId = setInterval(() => {
        timer--;
        if (timer <= 0) {
            clearInterval(intervalId);
            verificaRisposta(-1);
        }
        disegna();
    }, 1000);
    
    disegna();
}

function roundRect(ctx, x, y, width, height, radius) {
    if (width < 2 * radius) radius = width / 2;
    if (height < 2 * radius) radius = height / 2;
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.arcTo(x + width, y, x + width, y + height, radius);
    ctx.arcTo(x + width, y + height, x, y + height, radius);
    ctx.arcTo(x, y + height, x, y, radius);
    ctx.arcTo(x, y, x + width, y, radius);
    ctx.closePath();
    return ctx;
}

function disegna() {
    const w = canvas.width;
    const h = canvas.height;

    ctx.fillStyle = '#FAF6EE'; 
    ctx.fillRect(0, 0, w, h);
    
    if (gameOver) {
        disegnaGameOver();
        return;
    }
    
    const domandaCorrente = domande[indiceDomandaCorrente];

    ctx.fillStyle = '#E2DDD1';
    roundRect(ctx, 20, 20, w - 40, 10, 5).fill();

    const maxTime = 30; 
    const larghezzaBarra = (timer / maxTime) * (w - 40);

    ctx.fillStyle = timer > 10 ? '#69A297' : '#E49A7D';

    roundRect(ctx, 20, 20, Math.max(5, larghezzaBarra), 10, 5).fill();

    ctx.fillStyle = '#4D7D72'; 
    ctx.font = `bold ${Math.floor(w * 0.04)}px Quicksand`;
    ctx.textAlign = 'left';
    ctx.fillText(`Domanda ${indiceDomandaCorrente + 1} di ${domande.length}`, 20, 55);

    ctx.fillStyle = '#333';
    ctx.font = `bold ${Math.floor(w * 0.05)}px Quicksand`;
    ctx.textAlign = 'center';
    
    const domandaTesto = domandaCorrente.domanda;
    const maxWidth = w - 60;
    const parole = domandaTesto.split(' ');
    let riga = '';
    let y = h * 0.25; 
    const lineHeight = Math.floor(w * 0.06);
    
    for (let i = 0; i < parole.length; i++) {
        const test = riga + parole[i] + ' ';
        const metrics = ctx.measureText(test);
        if (metrics.width > maxWidth && i > 0) {
            ctx.fillText(riga, w / 2, y);
            riga = parole[i] + ' ';
            y += lineHeight;
        } else {
            riga = test;
        }
    }
    ctx.fillText(riga, w / 2, y);

    const opzioniY = h * 0.50;
    const gap = 15;
    const opzioneHeight = ((h * 0.45) / 4) - gap;
    const opzioneWidth = w - 40;
    
    opzioniRects = [];
    
    for (let i = 0; i < domandaCorrente.opzioni.length; i++) {
        const x = 20;
        const currentY = opzioniY + i * (opzioneHeight + gap);
        
        opzioniRects.push({ x, currentY, width: opzioneWidth, height: opzioneHeight, indice: i });
        
        let bgColor = '#FFFFFF'; 
        let textColor = '#4D7D72'; 
        let borderColor = '#E2DDD1'; 
        
        if (mostraRisultato) {
            if (i === domandaCorrente.corretta) {
                bgColor = '#8EBAA3'; 
                textColor = '#FFFFFF';
                borderColor = '#8EBAA3';
            } else if (i === rispostaSelezionata) {
                bgColor = '#E49A7D'; 
                textColor = '#FFFFFF';
                borderColor = '#E49A7D';
            }
        }
        
        ctx.save();
        ctx.shadowColor = "rgba(0, 0, 0, 0.1)";
        ctx.shadowBlur = 10;
        ctx.shadowOffsetY = 4;
        
        ctx.fillStyle = bgColor;
        roundRect(ctx, x, currentY, opzioneWidth, opzioneHeight, 15).fill();
        ctx.restore();

        ctx.lineWidth = 2;
        ctx.strokeStyle = borderColor;
        roundRect(ctx, x, currentY, opzioneWidth, opzioneHeight, 15).stroke();
        
        ctx.fillStyle = textColor;
        ctx.font = `600 ${Math.floor(w * 0.045)}px Quicksand`; 
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        
        ctx.fillText(domandaCorrente.opzioni[i], w / 2, currentY + opzioneHeight / 2);
    }
}

function disegnaGameOver() {
    const w = canvas.width;
    const h = canvas.height;

    ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
    ctx.fillRect(0, 0, w, h);

    ctx.fillStyle = "#FFFFFF";
    ctx.font = `bold ${Math.floor(w * 0.1)}px Quicksand`;
    ctx.textAlign = "center";
    ctx.fillText("Quiz Finito!", w / 2, h / 2 - (h * 0.1));

    ctx.fillStyle = "#FFFFFF";
    ctx.font = `${Math.floor(w * 0.06)}px Quicksand`;
    ctx.fillText(`Punteggio: ${risposteCorrette}/${domande.length}`, w / 2, h / 2 + (h * 0.05));

    ctx.fillStyle = "#E49A7D";
    ctx.font = `bold ${Math.floor(w * 0.05)}px Quicksand`;
    ctx.fillText("Clicca per rigiocare", w / 2, h / 2 + (h * 0.2));
}

function verificaRisposta(indice) {
    if (mostraRisultato) return;
    
    clearInterval(intervalId);
    rispostaSelezionata = indice;
    mostraRisultato = true;
    
    const domandaCorrente = domande[indiceDomandaCorrente];
    if (indice === domandaCorrente.corretta) {
        risposteCorrette++;
    }
    
    disegna();
    
    setTimeout(() => {
        indiceDomandaCorrente++;
        mostraDomanda();
    }, 2000);
}

function terminaGioco() {
    gameOver = true;
    if (intervalId) clearInterval(intervalId);
    disegna();
}

canvas.addEventListener('click', (e) => {
    if (gameOver) {
        iniziaGioco();
        return;
    }
    
    if (mostraRisultato) return;
    
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const x = (e.clientX - rect.left) * scaleX;
    const y = (e.clientY - rect.top) * scaleY;
    
    for (let opzione of opzioniRects) {
    if (x >= opzione.x && x <= opzione.x + opzione.width &&
        y >= opzione.currentY && y <= opzione.currentY + opzione.height) {
        verificaRisposta(opzione.indice);
        break;
    }
}
});

canvas.addEventListener('touchstart', (e) => {
    e.preventDefault();
    if (gameOver) {
        iniziaGioco();
        return;
    }
    
    if (mostraRisultato) return;
    
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const touch = e.touches[0];
    const x = (touch.clientX - rect.left) * scaleX;
    const y = (touch.clientY - rect.top) * scaleY;
    
    for (let opzione of opzioniRects) {
        if (x >= opzione.x && x <= opzione.x + opzione.width &&
            y >= opzione.y && y <= opzione.y + opzione.height) {
            verificaRisposta(opzione.indice);
            break;
        }
    }
});

iniziaGioco();