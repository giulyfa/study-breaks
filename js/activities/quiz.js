const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

const databaseDomande = [
    { domanda: "Qual è la capitale dell'Italia?", opzioni: ["Milano", "Roma", "Firenze", "Napoli"], corretta: 1 },
    { domanda: "Qual è il fiume più lungo d'Italia?", opzioni: ["Tevere", "Arno", "Po", "Adige"], corretta: 2 },
    { domanda: "In che anno è caduto il Muro di Berlino?", opzioni: ["1987", "1989", "1991", "1990"], corretta: 1 },
    { domanda: "Qual è il pianeta più grande del sistema solare?", opzioni: ["Saturno", "Giove", "Urano", "Nettuno"], corretta: 1 },
    { domanda: "Chi ha dipinto la Gioconda?", opzioni: ["Michelangelo", "Raffaello", "Leonardo da Vinci", "Caravaggio"], corretta: 2 },
    { domanda: "Quanti continenti ci sono sulla Terra?", opzioni: ["5", "6", "7", "8"], corretta: 2 },
    { domanda: "Qual è l'elemento chimico con simbolo 'O'?", opzioni: ["Oro", "Ossigeno", "Osmio", "Ozono"], corretta: 1 },
    { domanda: "In che anno l'uomo è atterrato sulla Luna?", opzioni: ["1967", "1968", "1969", "1970"], corretta: 2 },
    { domanda: "Qual è la montagna più alta del mondo?", opzioni: ["K2", "Monte Bianco", "Everest", "Kilimangiaro"], corretta: 2 },
    { domanda: "Chi ha scritto 'La Divina Commedia'?", opzioni: ["Petrarca", "Boccaccio", "Dante Alighieri", "Manzoni"], corretta: 2 },
    { domanda: "Qual è l'oceano più grande?", opzioni: ["Atlantico", "Indiano", "Artico", "Pacifico"], corretta: 3 },
    { domanda: "Quante sono le regioni italiane?", opzioni: ["18", "19", "20", "21"], corretta: 2 },
    { domanda: "Qual è la velocità della luce?", opzioni: ["300.000 km/s", "150.000 km/s", "500.000 km/s", "200.000 km/s"], corretta: 0 },
    { domanda: "In che anno è iniziata la Prima Guerra Mondiale?", opzioni: ["1912", "1914", "1916", "1918"], corretta: 1 },
    { domanda: "Qual è il simbolo chimico dell'oro?", opzioni: ["Au", "Ag", "Fe", "Cu"], corretta: 0 }
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

function disegna() {
    const w = canvas.width;
    const h = canvas.height;
    
    ctx.clearRect(0, 0, w, h);
    
    if (gameOver) {
        disegnaGameOver();
        return;
    }
    
    const domandaCorrente = domande[indiceDomandaCorrente];
    
    // Timer
    ctx.fillStyle = timer <= 10 ? '#E74C3C' : '#4D7D72';
    ctx.font = `bold ${Math.floor(w * 0.08)}px Quicksand`;
    ctx.textAlign = 'right';
    ctx.fillText(`${timer}s`, w - 20, 40);
    
    // Progresso
    ctx.fillStyle = '#333';
    ctx.font = `${Math.floor(w * 0.05)}px Quicksand`;
    ctx.textAlign = 'left';
    ctx.fillText(`Domanda ${indiceDomandaCorrente + 1}/${domande.length}`, 20, 35);
    
    // Punteggio
    ctx.fillText(`Corrette: ${risposteCorrette}`, 20, 60);
    
    // Domanda
    ctx.fillStyle = '#333';
    ctx.font = `bold ${Math.floor(w * 0.045)}px Quicksand`;
    ctx.textAlign = 'center';
    
    const domandaTesto = domandaCorrente.domanda;
    const maxWidth = w - 40;
    const parole = domandaTesto.split(' ');
    let riga = '';
    let y = h * 0.2;
    const lineHeight = Math.floor(w * 0.055);
    
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
    
    // Opzioni
    const opzioniY = h * 0.45;
    const opzioneHeight = (h * 0.5) / 4 - 10;
    const opzioneWidth = w - 40;
    
    opzioniRects = [];
    
    for (let i = 0; i < domandaCorrente.opzioni.length; i++) {
        const x = 20;
        const y = opzioniY + i * (opzioneHeight + 10);
        
        opzioniRects.push({ x, y, width: opzioneWidth, height: opzioneHeight, indice: i });
        
        // Colore sfondo
        let bgColor = '#ffffff';
        if (mostraRisultato) {
            if (i === domandaCorrente.corretta) {
                bgColor = '#2ECC71';
            } else if (i === rispostaSelezionata) {
                bgColor = '#E74C3C';
            }
        }
        
        ctx.fillStyle = bgColor;
        ctx.fillRect(x, y, opzioneWidth, opzioneHeight);
        
        ctx.strokeStyle = '#4D7D72';
        ctx.lineWidth = 2;
        ctx.strokeRect(x, y, opzioneWidth, opzioneHeight);
        
        // Testo opzione
        ctx.fillStyle = mostraRisultato && (i === domandaCorrente.corretta || i === rispostaSelezionata) ? '#fff' : '#333';
        ctx.font = `${Math.floor(w * 0.04)}px Quicksand`;
        ctx.textAlign = 'left';
        
        const testoOpzione = domandaCorrente.opzioni[i];
        const maxWidthOpzione = opzioneWidth - 20;
        const parolOpzione = testoOpzione.split(' ');
        let rigaOpzione = '';
        let yOpzione = y + opzioneHeight / 2;
        const lineHeightOpzione = Math.floor(w * 0.045);
        let righe = [];
        
        for (let j = 0; j < parolOpzione.length; j++) {
            const testOpzione = rigaOpzione + parolOpzione[j] + ' ';
            const metricsOpzione = ctx.measureText(testOpzione);
            if (metricsOpzione.width > maxWidthOpzione && j > 0) {
                righe.push(rigaOpzione);
                rigaOpzione = parolOpzione[j] + ' ';
            } else {
                rigaOpzione = testOpzione;
            }
        }
        righe.push(rigaOpzione);
        
        const startY = yOpzione - (righe.length - 1) * lineHeightOpzione / 2;
        for (let k = 0; k < righe.length; k++) {
            ctx.fillText(righe[k].trim(), x + 10, startY + k * lineHeightOpzione);
        }
    }
}

function disegnaGameOver() {
    const w = canvas.width;
    const h = canvas.height;
    
    ctx.fillStyle = '#4D7D72';
    ctx.font = `bold ${Math.floor(w * 0.1)}px Quicksand`;
    ctx.textAlign = 'center';
    ctx.fillText('GAME OVER', w / 2, h * 0.35);
    
    ctx.fillStyle = '#333';
    ctx.font = `${Math.floor(w * 0.06)}px Quicksand`;
    ctx.fillText(`Punteggio: ${risposteCorrette}/${domande.length}`, w / 2, h * 0.5);
    
    const percentuale = Math.round((risposteCorrette / domande.length) * 100);
    ctx.font = `${Math.floor(w * 0.045)}px Quicksand`;
    ctx.fillText(`${percentuale}% corretto`, w / 2, h * 0.6);
    
    ctx.fillStyle = '#E49A7D';
    ctx.font = `${Math.floor(w * 0.04)}px Quicksand`;
    ctx.fillText('Clicca per ricominciare', w / 2, h * 0.75);
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
            y >= opzione.y && y <= opzione.y + opzione.height) {
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

// Avvia il gioco
iniziaGioco();