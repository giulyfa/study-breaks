<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit;
}

if (isset($_SESSION['user_ruolo']) && strtolower($_SESSION['user_ruolo']) === 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
$oggi = date('Y-m-d'); 

// RESET GIORNALIERO
if (!isset($_SESSION['data_ultimo_accesso']) || $_SESSION['data_ultimo_accesso'] !== $oggi) {
    $stmtReset = $pdo->prepare("UPDATE utenti SET pause_oggi = 0, attivita_oggi = 0, sessioni_oggi = 0 WHERE id = ?");
    $stmtReset->execute([$user_id]);

    $_SESSION['pause_oggi'] = 0;
    $_SESSION['attivita_oggi'] = 0;
    $_SESSION['sessioni_oggi'] = 0;
    $_SESSION['data_ultimo_accesso'] = $oggi;
}

// RECUPERO ATTIVITÀ
// --- 3. ATTIVITÀ PIÙ SVOLTE (Con JOIN per prendere lo slug della foto) ---
$stmtFav = $pdo->prepare("
    SELECT asv.nome_attivita, COUNT(*) as totale, a.slug 
    FROM attivita_svolte asv
    LEFT JOIN attivita a ON asv.id_attivita = a.id
    WHERE asv.id_utente = ? AND asv.id_attivita > 0 AND a.stato = 'attiva'
    GROUP BY asv.id_attivita 
    ORDER BY totale DESC 
    LIMIT 2
");
$stmtFav->execute([$user_id]);
$preferite = $stmtFav->fetchAll(PDO::FETCH_ASSOC);

$playlists = $pdo->query("SELECT * FROM playlist WHERE attiva = 1")->fetchAll(PDO::FETCH_ASSOC);
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
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/sidebar.php'; ?>

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
                        <div id="custom-alert" class="alert-toast"><span id="alert-message"></span></div>
                        
                        <div class="timer-background">
                            <h1>Timer Pomodoro</h1>
                            <div class="settings-icon"><button type="button" id="settings-trigger">⚙️</button></div>

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
                                
                            <div class="timer-circle"><div id="timer-time">25:00</div></div>
                            
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

            <div id="suggestion-message" class="suggestion-text"></div>

            <section class="activity-section">
                <h2>Attività consigliate</h2>
                <div class="activity-grid">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM attivita WHERE stato = 'attiva' LIMIT 4");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                        $imagePath = file_exists("img/{$row['slug']}.jpg") ? "img/{$row['slug']}.jpg" : "img/logo.png";
                    ?>

                    <div class="activity-item" onclick="apriAttivita(<?= $row['id'] ?>, '<?= $row['slug'] ?>', '<?= addslashes($row['titolo']) ?>', '<?= $row['tipo'] ?>', <?= $row['durata'] ?>)">
                        <div class="activity-icon">
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['titolo']); ?>">
                        </div>
                        <p><?php echo htmlspecialchars($row['titolo']); ?> - <?php echo $row['durata']; ?> min</p>
                    </div>

                    <?php endwhile; ?>
                </div>
                <a href="attivita.php" class="btn primary-btn">Vai alle attività</a>
            </section>
            
            <section class="playlist-section">
                 <button onclick="togglePlaylist()" class="btn secondary-btn">Vai alle Playlist</button>
            </section>
        </main>
        
        <?php include 'includes/footer.php'; ?>
    </div> 

    <div id="activity-modal" class="modal activity-overlay">
        <div class="modal-content game-modal-content">
            <span class="close-btn-activity" onclick="chiudiAttivita()">&times;</span>
            <iframe id="game-frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <div id="playlist-selector-overlay" class="modal" style="display:none;">
        <div class="modal-content playlist-modal">
            <span class="close-btn" onclick="togglePlaylist()">&times;</span>
            <h2>Scegli la tua musica</h2>
            <div class="playlist-list">
                <?php foreach ($playlists as $p): ?>
                    <?php if ($p['attiva']): ?>
                        <div class="playlist-item">
                            <div>
                                <strong><?php echo htmlspecialchars($p['titolo']); ?></strong><br>
                                <small>Playlist Spotify</small>
                            </div>
                            <a href="<?php echo $p['url_spotify']; ?>" target="_blank" onclick="return registraAscolto(event, <?php echo $p['id']; ?>)">
                            Ascolta
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        const minutiSalvati = <?php echo $_SESSION['timer_scelto'] ?? 25; ?>;
        const pausaSalvata = <?php echo $_SESSION['pausa_scelta'] ?? 5; ?>;
    </script>
    <script src="js/timer.js"></script>    
    <script src="js/activities.js"></script><?php include 'includes/scripts.php'; ?>
</body>
</html>