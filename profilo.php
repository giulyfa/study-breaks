<?php
// 1. CARICAMENTO CONFIGURAZIONE E CONNESSIONE
require_once 'config.php'; 

// 2. CONTROLLO ACCESSO
// Se la sessione non trova l'ID utente, reindirizza alla pagina di login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 3. RECUPERO DATI UTENTE (Dati "fissi" e totali)
try {
    // Prendiamo streak e sessioni (oggi e totali) direttamente dalla tabella utenti
    $stmt = $pdo->prepare("SELECT streak_attuale, sessioni_oggi, sessioni_totali FROM utenti WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se l'utente non esiste per qualche motivo, inizializziamo a zero
    $streak = $user_data['streak_attuale'] ?? 0;
    $sess_oggi = $user_data['sessioni_oggi'] ?? 0;
    $sess_totali = $user_data['sessioni_totali'] ?? 0;

} catch (PDOException $e) {
    // In caso di errore nel database, mostriamo un messaggio (opzionale)
    die("Errore nel recupero dati: " . $e->getMessage());
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
    <title>Area Personale - Study Breaks</title>
</head>
<body>
    <div class="profile-page">
        <header>
            <button class="menu-btn">&#9776;</button>
            <img src="img/logo.png" alt="STUDY BREAKS Logo" class="header-logo" />
        </header>

        <main class="profile-container">
            <div class="content-profile">
                <section class="streak-card">
                    <div class="streak-content">
                        <h2>Giorni di fila</h2>
                        <span class="streak-big-number">7</span>
                    </div>
                </section>

                <div class="stats-column">
                    <nav class="time-selector">
                        <button class="time-btn">Oggi</button>
                        <button class="time-btn">Settimana</button>
                        <button class="time-btn">Mese</button>
                    </nav>

                    <section class="stats-grid">
                        <div class="stat-card sessioni">
                            <h3>Sessioni</h3>
                            <span class="stat-val">5</span>
                        </div>
                        <div class="stat-card attivita">
                            <h3>Attività</h3>
                            <span class="stat-val">23</span>
                        </div>
                    </section>
                </div>
            </div>

            <div class="left-column">
            <section class="activities-history-container">
                <div class="activities-history-content">
                    <h3 class="section-title">Attività svolte</h3>

                    <div class="activity-row">
                        <div class="activity-info-left">
                            <img src="img/snake.jpg" alt="Snake" class="activity-mini-logo"> <span class="activity-name">snake</span>
                        </div>
                        <div class="activity-stats-right">
                            <div class="progress-bar-container">
                                <div class="progress-fill" style="width: 70%;"></div> </div>
                            <span class="activity-count">4</span> </div>
                    </div>

                    <div class="activity-row">
                        <div class="activity-info-left">
                            <img src="img/quiz.jpg" alt="Quiz" class="activity-mini-logo"> <span class="activity-name">quiz</span>
                        </div>
                        <div class="activity-stats-right">
                            <div class="progress-bar-container">
                                <div class="progress-fill" style="width: 90%;"></div>
                            </div>
                            <span class="activity-count">9</span>
                        </div>
                    </div>

                    <button class="expand-btn">Espandi</button>
                </div>
            </section>

            <div class="weekly-activity-container">
                <div class="chart-wrapper">
                    <h3 class="weekly-title">Attività settimanale</h3>
                    <svg class="activity-chart" viewBox="0 0 400 200">
                        <line x1="10" y1="0" x2="10" y2="180" class="chart-grid-line" />
                        <line x1="75" y1="0" x2="75" y2="180" class="chart-grid-line" />
                        <line x1="140" y1="0" x2="140" y2="180" class="chart-grid-line" />
                        <line x1="205" y1="0" x2="205" y2="180" class="chart-grid-line" />
                        <line x1="270" y1="0" x2="270" y2="180" class="chart-grid-line" />
                        <line x1="335" y1="0" x2="335" y2="180" class="chart-grid-line" />
                        <line x1="390" y1="0" x2="390" y2="180" class="chart-grid-line" />

                        <polyline
                            fill="none"
                            stroke="#E49A7D"
                            stroke-width="3"
                            points="10,140 75,80 140,120 205,90 270,160 335,145 390,70"
                        />
                    </svg>
                    
                    <div class="chart-labels">
                        <span>lun</span>
                        <span>mar</span>
                        <span>mer</span>
                        <span>gio</span>
                        <span>ven</span>
                        <span>sab</span>
                        <span>dom</span>
                    </div>
                </div>
            </div>
            </div>
            
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
</body>
</html>