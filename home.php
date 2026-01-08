<?php
require_once 'config.php'; // Carica sessione e connessione al DB

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Recuperiamo l'ID dell'utente
$oggi = date('Y-m-d'); 

// --- LOGICA DI RESET GIORNALIERO ---
if (!isset($_SESSION['data_ultimo_accesso']) || $_SESSION['data_ultimo_accesso'] !== $oggi) {
    
    // 1. RESET SUL DATABASE: Azzeriamo i contatori per la nuova giornata
    // Questo assicura che se l'utente fa logout e rientra domani, troverà 0
    $stmtReset = $pdo->prepare("UPDATE utenti SET 
        pause_oggi = 0, 
        attivita_oggi = 0, 
        sessioni_oggi = 0 
        WHERE id = ?");
    $stmtReset->execute([$user_id]);

    // 2. RESET IN SESSIONE
    $_SESSION['pause_oggi'] = 0;
    $_SESSION['attivita_oggi'] = 0;
    $_SESSION['sessioni_oggi'] = 0;
    
    $_SESSION['data_ultimo_accesso'] = $oggi;
}

// 3. RECUPERO ATTIVITÀ
// Recuperiamo le attività per visualizzarle nella griglia
$stmt = $pdo->query("SELECT id, slug, titolo, tipo, durata FROM attivita WHERE stato = 'attivo'");
$attivita = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nothing+You+Could+Do&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Home - Study Breaks</title>
</head>
<body>
    <div class="home-page">
        <header>
            <button class="menu-btn" id="open-sidebar">&#9776;</button>
            
            <a href="home.php" class="logo-link">
                <img src="img/logo.png" alt="STUDY BREAKS Logo" class="header-logo" />
            </a>
        </header>

        <div id="sidebar-nav" class="sidebar">
            <button class="close-btn">&times;</button>
            <div class="sidebar-links">
                <a href="home.php">Home</a>
                <a href="attivita.php">Attività</a>
                <a href="playlist.php">Playlist</a>
                <a href="profilo.php">Profilo</a>
                <a href="chi-siamo.php">Chi Siamo</a>
                <br><br>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <main>
            <div class="dashboard-top">
                <div class="dashboard-inner">
                <div class="dashboard-top-left">
                    <section class="hero-section">
                        <h1>PRENDITI UNA PAUSA INTELLIGENTE</h1>
                        <p>Micro-attività da 1 a 5 minuti per rigenerare la mente senza perdere la concentrazione</p>
                    </section>

                    <section class="stats-container">
                        <div class="stat-box">
                            <p>PAUSE OGGI</p>
                            <span class="stat-number" id="pause-count"><?php echo $_SESSION['pause_oggi'] ?? 0; ?></span>                        
                        </div>
                        <div class="stat-box">
                            <p>STREAK</p>
                            <span class="stat-number" id="streak-count"><?php echo $_SESSION['streak'] ?? 0; ?></span>
                        </div>
                        <div class="stat-box">
                            <p>ATTIVITÀ OGGI</p>
                            <span class="stat-number" id="activities-today-count"><?php echo $_SESSION['attivita_oggi'] ?? 0; ?></span>                        
                        </div>
                    </section>
                </div>

                <section class="timer-card"> 
                    <div id="custom-alert" class="alert-toast">
                        <span id="alert-message"></span>
                    </div>
                    <div class="timer-background">
                        <h1>Timer Pomodoro</h1>
                        <div class="settings-icon">
                            <button type="button" id="settings-trigger">⚙️</button>
                        </div>

                        <div id="custom-modal" class="modal">
                            <div class="modal-content">
                                <h3>Impostazioni Timer</h3>
                                <p>Inserisci i minuti per la sessione:</p>
                                <input type="number" id="new-minutes" placeholder="Es. 25">
                                <div class="modal-buttons">
                                    <button id="save-modal" class="save-btn">Salva</button>
                                    <button id="close-modal" class="cancel-btn">Annulla</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timer-circle">
                            <div id="timer-time">25:00</div>
                        </div>
                    
                        <div class="timer-status-selector">
                            <button class="mode-btn" id="mode-studio">Studio</button>
                            <button class="mode-btn" id="mode-pausa">Pausa</button>
                        </div>
                        
                        <div class="timer-controls-bottom">
                            <button id="start-btn" class="control-btn">Start</button>
                            <button id="stop-btn" class="control-btn">Stop</button>
                            <button id="restart-btn" class="control-btn">Restart</button>
                        </div>
                    </div>
                    <p class="next-break">Sessioni di studio oggi: <span id="sessions-count"><?php echo $_SESSION['sessioni_oggi'] ?? 0; ?></span></p>
                </section>
                </div>
            </div>

            <div id="suggestion-message" style="display: none; font-size: 700; text-align: center; margin: 10px 20px; font-weight: bold; color: #E49A7D;"></div>

            <section class="activity-section">
                <h2>Attività consigliate</h2>
                <div class="activity-grid">
                    <?php
                        $stmt = $pdo->query("SELECT * FROM attivita WHERE stato = 'active' LIMIT 4");

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $imagePath = 'img/' . $row['slug'] . '.jpg';
                            
                            if (!file_exists($imagePath)) {
                                $imagePath = 'img/logo.png';
                            }
                            
                            // Prepariamo i dati per il JavaScript
                            $id = $row['id'];
                            $slug = $row['slug'];
                            $titolo = addslashes($row['titolo']); // addslashes evita errori se il titolo ha apostrofi
                            $tipo = $row['tipo']; // Assicurati che la colonna nel DB si chiami 'tipo'
                            $durata = $row['durata'];
                        ?>

                            <div class="activity-item" 
                                onclick="apriAttivita(<?php echo $id; ?>, '<?php echo $slug; ?>', '<?php echo $titolo; ?>', '<?php echo $tipo; ?>', <?php echo $durata; ?>)" 
                                style="cursor: pointer;">
                                
                                <div class="activity-icon">
                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['titolo']); ?>">
                                </div>
                                <p><?php echo htmlspecialchars($row['titolo']); ?> - <?php echo $row['durata']; ?> min</p>
                            </div>

                        <?php 
                        } 
                        ?>
                </div>
                
                <a href="attivita.php" class="btn primary-btn">Vai alle attività</a>
            </section>
            
            <section class="playlist-section">
                 <a href="playlist.html" class="btn secondary-btn">Vai alle Playlist</a>
            </section>
        </main>
        
        <footer>
            <nav class="footer-links">
                <a href="home.php" class="footer-link">Home</a>
                <a href="activities.php" class="footer-link">Attività</a>
                <a href="playlist.php" class="footer-link">Playlist</a>
                <a href="profile.php" class="footer-link">Profilo</a><br>
                <a href="about.php" class="footer-link about-link">Chi siamo?</a>
            </nav>
        </footer>
        
    </div> 

    <script>
        // Recuperiamo il valore dalla sessione PHP, se non c'è usiamo 25 di default
        const minutiSalvati = <?php echo $_SESSION['timer_scelto'] ?? 25; ?>;
        const pausaSalvata = <?php echo $_SESSION['pausa_scelta'] ?? 5; ?>; // Aggiungi questa
    </script>
    <script src="js/timer.js"></script>
    <div id="activity-modal" class="modal activity-overlay">
        <div class="modal-content game-modal-content">
            <span class="close-btn-activity" onclick="chiudiAttivita()">&times;</span>
            
            <iframe id="game-frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        // Variabile globale per memorizzare i dati dell'attività aperta
        let attivitaCorrente = { id: 0, nome: '', tipo: '', durata: 0 };
        let tempoInizioAttivita = 0;

        function apriAttivita(id, slug, titolo, tipo, durata) {
            const modal = document.getElementById('activity-modal');
            const iframe = document.getElementById('game-frame');
            
            // Memorizziamo i dati dell'attività per usarli nella chiusura
            attivitaCorrente.id = id;
            attivitaCorrente.nome = titolo;
            attivitaCorrente.tipo = tipo;
            attivitaCorrente.durata = durata;
            
            // Imposta la sorgente dell'iframe
            iframe.src = 'activity_player.php?name=' + slug;
            
            // Mostra il modale
            modal.style.display = 'block';

            // Segnamo l'orario di inizio
            tempoInizioAttivita = Date.now();
        }

        function chiudiAttivita() {
            const modal = document.getElementById('activity-modal');
            const iframe = document.getElementById('game-frame');

            const secondiPassati = (Date.now() - tempoInizioAttivita) / 1000;

            // CONDIZIONE: 30 secondi per convalidare
            if (secondiPassati >= 30) {
                // Aggiornamento attività in tempo reale
                let actTodaySpan = document.getElementById('activities-today-count'); 
                
                if(actTodaySpan) {
                    // Incrementa il numero che l'utente vede nel quadratino bianco
                    actTodaySpan.textContent = parseInt(actTodaySpan.textContent) + 1;
                    
                    // Prepariamo i dati per il database (questo rimane uguale, è perfetto)
                    const params = new URLSearchParams({
                        azione: 'attivita',
                        id_att: attivitaCorrente.id,
                        nome: attivitaCorrente.nome,
                        categoria: attivitaCorrente.tipo,
                        durata: attivitaCorrente.durata
                    });

                    // Invio al server: il PHP si occuperà di aumentare sia il totale nel DB 
                    // sia la variabile $_SESSION['attivita_oggi']
                    fetch('salva_dati.php?' + params.toString());
                }
            }   

            modal.style.display = 'none';
            iframe.src = ''; 
        }

        // Chiude se si clicca fuori dal box del gioco
        window.onclick = function(event) {
            const modal = document.getElementById('activity-modal');
            if (event.target == modal) {
                chiudiAttivita();
            }
        }
    </script>
</body>
</html>