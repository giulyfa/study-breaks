<?php
require_once 'config.php';

// Verifica opzionale se l'utente è loggato (se vuoi proteggere la home)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
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
                            <p>SESSIONI</p>
                            <span class="stat-number" id="total-sessions-count"><?php echo $_SESSION['sessioni_totali'] ?? 0; ?></span>
                        </div>
                        <div class="stat-box">
                            <p>STREAK</p>
                            <span class="stat-number" id="streak-count">0</span> </div>
                        <div class="stat-box">
                            <p>ATTIVITÀ</p>
                            <span class="stat-number" id="activities-count"><?php echo $_SESSION['attivita_totali'] ?? 0; ?></span>
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
                    ?>

                        <div class="activity-item" onclick="apriAttivita('<?php echo $row['slug']; ?>')" style="cursor: pointer;">
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
    </script>
    <script src="js/timer.js"></script>
    <div id="activity-modal" class="modal activity-overlay">
        <div class="modal-content game-modal-content">
            <span class="close-btn-activity" onclick="chiudiAttivita()">&times;</span>
            
            <iframe id="game-frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        function apriAttivita(slug) {
            const modal = document.getElementById('activity-modal');
            const iframe = document.getElementById('game-frame');
            
            // Imposta la sorgente dell'iframe su activity_player.php passando lo slug
            iframe.src = 'activity_player.php?name=' + slug;
            
            // Mostra il modale
            modal.style.display = 'block';

            // Memorizziamo il momento esatto in cui hai aperto il gioco. PER AGGIORNARE NUMERO ATTIVITA'
            tempoInizioAttivita = Date.now();
        }

        // Funzione per chiudere e resettare
        function chiudiAttivita() {
            const modal = document.getElementById('activity-modal');
            const iframe = document.getElementById('game-frame');

            //PER AGGIORNARE NUMERO ATTIVITA'
            const tempoFineAttivita = Date.now();
            const secondiPassati = (tempoFineAttivita - tempoInizioAttivita) / 1000;
            // CONDIZIONE: Conta l'attività solo se sei stata dentro più di 60 secondi
            if (secondiPassati >= 60) {
                let actSpan = document.getElementById('activities-count');
                if(actSpan) {
                    actSpan.textContent = parseInt(actSpan.textContent) + 1;
                    fetch('salva_dati.php?azione=attivita');
                }
            }   

            modal.style.display = 'none';
            iframe.src = ''; // Questo ferma il gioco immediatamente!
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