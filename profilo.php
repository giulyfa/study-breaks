<?php
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- 1. RECUPERO DATI UTENTE (Dalla tabella utenti) ---
try {
    $stmt = $pdo->prepare("SELECT streak, sessioni_oggi, attivita_oggi FROM utenti WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $streak = $user_data['streak'] ?? 0;
    $sess_oggi = $user_data['sessioni_oggi'] ?? 0;
    $att_oggi = $user_data['attivita_oggi'] ?? 0;

    // --- 2. QUERY PER SETTIMANA E MESE (Dalla cronologia attivita_svolte) ---
    function getPeriodStats($pdo, $user_id, $tipo, $intervallo) {
        // Studio = id_attivita 0 | Giochi = id_attivita > 0
        $condition = ($tipo == 'studio') ? "id_attivita = 0" : "id_attivita > 0";
        $sql = "SELECT COUNT(*) FROM attivita_svolte 
                WHERE id_utente = ? AND $condition 
                AND data_ora >= DATE_SUB(NOW(), INTERVAL $intervallo DAY)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    $sess_sett = getPeriodStats($pdo, $user_id, 'studio', 7);
    $sess_mese = getPeriodStats($pdo, $user_id, 'studio', 30);
    $att_sett = getPeriodStats($pdo, $user_id, 'gioco', 7);
    $att_mese = getPeriodStats($pdo, $user_id, 'gioco', 30);

    // --- 3. ATTIVITÀ PIÙ SVOLTE (Per le barre di progresso) ---
    $stmtFav = $pdo->prepare("SELECT nome_attivita, COUNT(*) as totale 
                             FROM attivita_svolte 
                             WHERE id_utente = ? AND id_attivita > 0 
                             GROUP BY nome_attivita ORDER BY totale DESC LIMIT 2");
    $stmtFav->execute([$user_id]);
    $preferite = $stmtFav->fetchAll(PDO::FETCH_ASSOC);

    // --- 4. LOGICA GRAFICO STUDIO (Ultimi 7 giorni) ---
    $punti_grafico = "";
    $etichette_giorni = [];
    $x = 10;

    // Array di traduzione manuale
    $giorni_it = [
        'Sun' => 'dom', 'Mon' => 'lun', 'Tue' => 'mar', 
        'Wed' => 'mer', 'Thu' => 'gio', 'Fri' => 'ven', 'Sat' => 'sab'
    ];

    for ($i = 6; $i >= 0; $i--) {
        $data_check = date('Y-m-d', strtotime("-$i days"));
        $giorno_en = date('D', strtotime($data_check)); // Prende "Mon", "Tue"...
        $etichette_giorni[] = $giorni_it[$giorno_en]; // Traduce in italiano

        $stmtG = $pdo->prepare("SELECT COUNT(*) FROM attivita_svolte WHERE id_utente = ? AND id_attivita = 0 AND DATE(data_ora) = ?");
        $stmtG->execute([$user_id, $data_check]);
        $val = $stmtG->fetchColumn();

        $y = 180 - (min($val, 10) * 17); 
        $punti_grafico .= "$x," . round($y) . " ";
        $x += 63;
    }

} catch (PDOException $e) {
    die("Errore: " . $e->getMessage());
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
    <link rel="stylesheet" href="css/style.css">
    <title>Area Personale - Study Breaks</title>
</head>
<body>
    <div class="profile-page">
        <header>
            <button class="menu-btn" id="open-sidebar">&#9776;</button>
            <a href="home.php"><img src="img/logo.png" alt="STUDY BREAKS Logo" class="header-logo" /></a>
        </header>

        <main class="profile-container">
            <div class="content-profile">
                <section class="streak-card">
                    <div class="streak-content">
                        <h2>Giorni di fila</h2>
                        <span class="streak-big-number"><?php echo $streak; ?></span>
                    </div>
                </section>

                <div class="stats-column">
                    <nav class="time-selector">
                        <button class="time-btn active" onclick="updateStats('oggi', this)">Oggi</button>
                        <button class="time-btn" onclick="updateStats('settimana', this)">Settimana</button>
                        <button class="time-btn" onclick="updateStats('mese', this)">Mese</button>
                    </nav>

                    <section class="stats-grid">
                        <div class="stat-card sessioni">
                            <h3>Sessioni</h3>
                            <span class="stat-val" id="display-sess"><?php echo $sess_oggi; ?></span>
                        </div>
                        <div class="stat-card attivita">
                            <h3>Attività</h3>
                            <span class="stat-val" id="display-att"><?php echo $att_oggi; ?></span>
                        </div>
                    </section>
                </div>
            </div>

            <div class="left-column">
                <section class="activities-history-container">
                    <div class="activities-history-content">
                        <h3 class="section-title">Attività preferite</h3>
                        <?php if(empty($preferite)): ?>
                            <p style="text-align:center; padding: 10px;">Ancora nessuna attività registrata.</p>
                        <?php else: foreach($preferite as $fav): 
                            $percent = min(($fav['totale'] / 20) * 100, 100); // 20 è il target massimo puramente estetico
                        ?>
                            <div class="activity-row">
                                <div class="activity-info-left">
                                    <img src="img/<?php echo strtolower($fav['nome_attivita']); ?>.jpg" class="activity-mini-logo"> 
                                    <span class="activity-name"><?php echo $fav['nome_attivita']; ?></span>
                                </div>
                                <div class="activity-stats-right">
                                    <div class="progress-bar-container">
                                        <div class="progress-fill" style="width: <?php echo $percent; ?>%;"></div>
                                    </div>
                                    <span class="activity-count"><?php echo $fav['totale']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                        <button class="expand-btn">Espandi</button>
                    </div>
                </section>

                <div class="weekly-activity-container">
                    <div class="chart-wrapper">
                        <h3 class="weekly-title">Impegno Studio (Settimana)</h3>
                        <svg class="activity-chart" viewBox="0 0 400 200">
                            <?php for($i=0; $i<7; $i++): ?>
                                <line x1="<?php echo 10 + ($i*63); ?>" y1="0" x2="<?php echo 10 + ($i*63); ?>" y2="180" class="chart-grid-line" />
                            <?php endfor; ?>
                            
                            <polyline fill="none" stroke="#E49A7D" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                points="<?php echo trim($punti_grafico); ?>" />
                        </svg>
                        <div class="chart-labels">
                            <?php foreach($etichette_giorni as $label) echo "<span>".strtolower($label)."</span>"; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <nav class="footer-links">
                <a href="home.php" class="footer-link">Home</a>
                <a href="activities.php" class="footer-link">Attività</a>
                <a href="profile.php" class="footer-link">Profilo</a><br>
                <a href="chi-siamo.php" class="footer-link about-link">Chi siamo?</a>
            </nav>
        </footer>
    </div>

    <script>
        // Questi dati vengono stampati da PHP una volta sola al caricamento
        const data = {
            oggi: { s: <?php echo $sess_oggi; ?>, a: <?php echo $att_oggi; ?> },
            settimana: { s: <?php echo $sess_sett; ?>, a: <?php echo $att_sett; ?> },
            mese: { s: <?php echo $sess_mese; ?>, a: <?php echo $att_mese; ?> }
        };

        function updateStats(periodo, btn) {
            // 1. Aggiorna i testi
            document.getElementById('display-sess').textContent = data[periodo].s;
            document.getElementById('display-att').textContent = data[periodo].a;
            
            // 2. Rimuove la classe 'active' da tutti i bottoni
            document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
            
            // 3. Aggiunge la classe 'active' al bottone cliccato
            btn.classList.add('active');
            
            console.log("Visualizzazione aggiornata a: " + periodo);
        }
    </script>
    <script src="js/global.js"></script>
</body>
</html>