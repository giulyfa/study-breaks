<?php
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // 1. RECUPERO DATI UTENTE
    $stmt = $pdo->prepare("SELECT nome, streak, sessioni_oggi, attivita_oggi FROM utenti WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $nome_utente = $user_data['nome'] ?? 'Studente';

    $streak = $user_data['streak'] ?? 0;
    $sess_oggi = $user_data['sessioni_oggi'] ?? 0;
    $att_oggi = $user_data['attivita_oggi'] ?? 0;

    // --- 2. QUERY PER SETTIMANA E MESE ---
    function getPeriodStats($pdo, $user_id, $tipo, $intervallo) {
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

    // --- 3. ATTIVITÀ PREFERITE (Aumentato il limite per l'espansione) ---
    $stmtFav = $pdo->prepare("SELECT asv.nome_attivita, COUNT(*) as totale, a.slug 
                             FROM attivita_svolte asv
                             LEFT JOIN attivita a ON asv.id_attivita = a.id
                             WHERE asv.id_utente = ? AND asv.id_attivita > 0 
                             GROUP BY asv.id_attivita ORDER BY totale DESC LIMIT 10");
    $stmtFav->execute([$user_id]);
    $preferite = $stmtFav->fetchAll(PDO::FETCH_ASSOC);

    // --- 4. LOGICA GRAFICO STUDIO ---
    $punti_grafico = "";
    $etichette_giorni = [];
    $giorni_it = ['Sun'=>'dom','Mon'=>'lun','Tue'=>'mar','Wed'=>'mer','Thu'=>'gio','Fri'=>'ven','Sat'=>'sab'];
    
    $width_svg = 400;
    $margin = 35; 
    $spazio_utile = $width_svg - ($margin * 2);
    $step_x = $spazio_utile / 6;

    for ($i = 6; $i >= 0; $i--) {
        $data_check = date('Y-m-d', strtotime("-$i days"));
        $giorno_en = date('D', strtotime($data_check));
        $etichette_giorni[] = $giorni_it[$giorno_en];

        $stmtG = $pdo->prepare("SELECT COUNT(*) FROM attivita_svolte WHERE id_utente = ? AND id_attivita = 0 AND DATE(data_ora) = ?");
        $stmtG->execute([$user_id, $data_check]);
        $val = $stmtG->fetchColumn();

        $current_x = $margin + ((6 - $i) * $step_x);
        $y = 180 - (min($val, 10) * 16); 
        $punti_grafico .= round($current_x) . "," . round($y) . " ";
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
    <style>
        /* Stile per nascondere le righe extra */
        .hidden-row {
            display: none !important;
        }
        .show-row {
            display: flex !important;
        }
    </style>
</head>
<body>
    <div class="profile-page">
        <header>
            <button class="menu-btn" id="open-sidebar">&#9776;</button>
            <a href="home.php"><img src="img/logo.png" alt="STUDY BREAKS Logo" class="header-logo" /></a>
        </header>

        <div id="sidebar-nav" class="sidebar">
            <button class="close-btn">&times;</button>
            <div class="sidebar-links">
                <a href="home.php">Home</a>
                <a href="attivita.php">Attività</a>
                <a href="profilo.php">Profilo</a>
                <a href="proposta.php">Proposta</a>
                <a href="chi-siamo.php">Chi Siamo</a>
                <br><br>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <main class="profile-container">
            <section class="welcome-card">
                <div class="welcome-content">
                    <h1>Ciao, <?php echo htmlspecialchars($nome_utente); ?>! </h1>
                    <p>Questa è la tua area personale. Qui puoi monitorare i tuoi progressi e vedere quanto sei stato produttivo.</p>
                </div>
            </section>

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
                        <div id="activities-wrapper">
                            <?php if(empty($preferite)): ?>
                                <p style="text-align:center; padding: 10px;">Ancora nessuna attività registrata.</p>
                            <?php else: 
                                $count = 0;
                                foreach($preferite as $fav): 
                                    $count++;
                                    // Le righe dopo la terza partono nascoste con display:none
                                    $display = ($count > 3) ? 'display: none;' : 'display: flex;';
                            ?>
                                <div class="activity-row" style="<?php echo $display; ?>">
                                    <div class="activity-info-left">
                                        <?php 
                                            $img = !empty($fav['slug']) ? 'img/'.$fav['slug'].'.jpg' : 'img/logo.png';
                                            if(!file_exists($img)) $img = 'img/logo.png';
                                        ?>
                                        <img src="<?php echo $img; ?>" class="activity-mini-logo"> 
                                        <span class="activity-name"><?php echo htmlspecialchars($fav['nome_attivita']); ?></span>
                                    </div>
                                    <div class="activity-stats-right">
                                        <div class="progress-bar-container">
                                            <?php $percent = min(($fav['totale'] / 20) * 100, 100); ?>
                                            <div class="progress-fill" style="width: <?php echo $percent; ?>%;"></div>
                                        </div>
                                        <span class="activity-count"><?php echo $fav['totale']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>

                        <?php if(count($preferite) > 3): ?>
                            <button class="expand-btn" id="toggle-btn" onclick="toggleRows()">Espandi</button>
                        <?php endif; ?>
                    </div>
                </section>

                <div class="weekly-activity-container">
                    <div class="chart-wrapper">
                        <h3 class="weekly-title">Impegno Studio (Settimana)</h3>
                        <svg class="activity-chart" viewBox="0 0 400 200" preserveAspectRatio="none">
                            <?php for($i=0; $i<7; $i++): $lineX = 35 + ($i * ($spazio_utile/6)); ?>
                                <line x1="<?php echo $lineX; ?>" y1="0" x2="<?php echo $lineX; ?>" y2="180" class="chart-grid-line" />
                            <?php endfor; ?>
                            <polyline fill="none" stroke="#E49A7D" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"
                                points="<?php echo trim($punti_grafico); ?>" />
                        </svg>
                        <div class="chart-labels" style="display: flex; justify-content: space-between; padding: 0 8%;">
                            <?php foreach($etichette_giorni as $label) echo "<span style='flex:1; text-align:center;'>$label</span>"; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <nav class="footer-links">
                <a href="home.php" class="footer-link">Home</a>
                <a href="activities.php" class="footer-link">Attività</a>
                <a href="profile.php" class="footer-link">Profilo</a>
                <a href="proposta.php" class="footer-link">Proposta</a><br>
                <a href="chi-siamo.php" class="footer-link about-link">Chi siamo?</a>
            </nav>
        </footer>
    </div>

    <script>
        const data = {
            oggi: { s: <?php echo $sess_oggi; ?>, a: <?php echo $att_oggi; ?> },
            settimana: { s: <?php echo $sess_sett; ?>, a: <?php echo $att_sett; ?> },
            mese: { s: <?php echo $sess_mese; ?>, a: <?php echo $att_mese; ?> }
        };

        function updateStats(periodo, btn) {
            document.getElementById('display-sess').textContent = data[periodo].s;
            document.getElementById('display-att').textContent = data[periodo].a;
            document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        // Funzione per mostrare/nascondere le attività extra
        function toggleRows() {
            // Prende TUTTE le righe delle attività
            const rows = document.querySelectorAll('.activity-row');
            const btn = document.getElementById('toggle-btn');

            if (btn.textContent === "Espandi") {
                // Mostra tutto
                rows.forEach(row => {
                    row.style.display = 'flex';
                });
                btn.textContent = "Chiudi";
            } else {
                // Nasconde dal quarto in poi (indice 3)
                rows.forEach((row, index) => {
                    if (index >= 3) {
                        row.style.display = 'none';
                    }
                });
                btn.textContent = "Espandi";
            }
        }
    </script>
    <script src="js/global.js"></script>
</body>
</html>