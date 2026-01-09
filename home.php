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

$stmtP = $pdo->query("SELECT * FROM playlist WHERE attiva = 1");
$playlists = $stmtP->fetchAll();
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
                 <button onclick="togglePlaylist()" class="btn secondary-btn">Vai alle Playlist</button>
            </section>
        </main>
        
        <footer>
            <nav class="footer-links">
                <a href="home.php" class="footer-link">Home</a>
                <a href="attivita.php" class="footer-link">Attività</a>
                <a href="profile.php" class="footer-link">Profilo</a><br>
                <a href="about.php" class="footer-link about-link">Chi siamo?</a>
            </nav>
        </footer>
        
    </div> 

    <div id="activity-modal" class="modal activity-overlay">
        <div class="modal-content game-modal-content">
            <span class="close-btn-activity" onclick="chiudiAttivita()">&times;</span>
            
            <iframe id="game-frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        // Recuperiamo il valore dalla sessione PHP, se non c'è usiamo 25 di default
        const minutiSalvati = <?php echo $_SESSION['timer_scelto'] ?? 25; ?>;
        const pausaSalvata = <?php echo $_SESSION['pausa_scelta'] ?? 5; ?>; // Aggiungi questa
    </script>
    <script src="js/global.js"></script>
    <script src="js/timer.js"></script>                    
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

        function togglePlaylist() {
            const overlay = document.getElementById('playlist-overlay');
            overlay.style.display = (overlay.style.display === 'none' || overlay.style.display === '') ? 'block' : 'none';
        }

        function registraAscolto(event, id) {
            // Inviamo solo l'id_p al server
            const url = `salva_dati.php?azione=log_playlist&id_p=${id}`;
            
            if (navigator.sendBeacon) {
                navigator.sendBeacon(url);
            } else {
                fetch(url);
            }
            
            return true; // Permette l'apertura del link Spotify
        }

        // Chiude se si clicca fuori dal box del gioco
        window.onclick = function(event) {
            const activityModal = document.getElementById('activity-modal');
            const playlistOverlay = document.getElementById('playlist-overlay');

            // Se clicchi fuori dal box dell'attività
            if (event.target == activityModal) {
                chiudiAttivita();
            }
            
            // Se clicchi fuori dal box delle playlist
            if (event.target == playlistOverlay) {
                togglePlaylist();
            }
        }
    </script>

    <div id="playlist-overlay" class="modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background-color: rgba(0,0,0,0.8); backdrop-filter: blur(5px);">
        <div class="modal-content" style="background:#fff; margin: 5% auto; padding:25px; border-radius:20px; width:90%; max-width:500px; position:relative;">
            <span class="close-btn" onclick="togglePlaylist()" style="position:absolute; right:20px; top:10px; font-size:30px; cursor:pointer;">&times;</span>
            
            <h2 style="color:#333; margin-bottom:20px; font-family:'Quicksand';">Scegli la tua musica</h2>
            
            <div class="playlist-list" style="display:flex; flex-direction:column; gap:15px;">
                <?php foreach ($playlists as $p): ?>
                    <?php if ($p['attiva']): // Entra qui solo se attiva è 1 (true) ?>
                        <div class="playlist-item" style="background:#1DB954; padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center; color:white;">
                            <div>
                                <strong style="display:block;"><?php echo htmlspecialchars($p['titolo']); ?></strong>
                                <small>Playlist Spotify</small>
                            </div>
                            <a href="<?php echo $p['url_spotify']; ?>" 
                            target="_blank" 
                            onclick="return registraAscolto(event, <?php echo $p['id']; ?>)" 
                            class="btn" 
                            style="background:rgba(255,255,255,0.2); padding:5px 15px; border-radius:20px; color:white; text-decoration:none; font-size:0.8em; border:1px solid white;">
                            Ascolta
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>