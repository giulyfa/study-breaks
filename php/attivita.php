<?php
require_once 'config.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'studente') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$oggi = date('Y-m-d'); 

// RECUPERO DATI
$attivita = $pdo->query("SELECT * FROM attivita WHERE stato = 'attiva'")->fetchAll();
$playlists = $pdo->query("SELECT * FROM playlist WHERE attiva = 1")->fetchAll();
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
    <link rel="stylesheet" href="../css/style.css">
    <title>Attività - Study Breaks</title>
</head>
<body>
    <div class="activity-page">
        <?php include '../includes/header.php'; ?>
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-area">
            <section class="activity-section">
                <h2>Attività</h2>

                <div class="filters">
                    <button class="filter-btn active" data-filter="all">Tutte</button>
                    <?php foreach([1, 2, 3, 5] as $min): ?>
                            <button class="filter-btn" data-filter="<?= $min ?>"><?= $min ?> min</button>
                    <?php endforeach; ?>
                </div>

                <div class="activity-grid">
                    <?php foreach ($attivita as $row): 
                        $imagePath = file_exists("../img/{$row['slug']}.jpg") ? "../img/{$row['slug']}.jpg" : "../img/logo.png";
                    ?>

                    <div class="activity-item"
                        data-durata="<?php echo $row['durata']; ?>"
                        onclick="apriAttivita(<?php echo $row['id']; ?>, '<?php echo $row['slug']; ?>', '<?php echo addslashes($row['titolo']); ?>', '<?php echo $row['tipo']; ?>', <?php echo $row['durata']; ?>)">
                        
                        <div class="activity-icon">
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['titolo']); ?>">
                        </div>
                        <p><?php echo htmlspecialchars($row['titolo']); ?> - <?php echo $row['durata']; ?> min</p>
                    </div>

                    <?php endforeach; ?>
                </div>

                <div class="suggestion-section">
                    <p>Hai un'idea per una nuova attività?</p>
                    <p class="small-text">Proponi la tua micro-attività e sarà valutata dall'admin</p>
                    <button class="propose-btn" onclick="window.location.href='../php/proposta.php'">Proponi nuova attività</button>
                    <p class="help">Aiutaci a migliorare!</p>
                </div>
            </section>

            <section class="playlist-section">
                <p class="playlist-intro">Oppure...</p>
                <h3>Rilassati con una playlist!</h3>
                
                <div class="spotify-container">
                    <?php foreach ($playlists as $p): ?>
                        <div class="spotify-card" onclick="registraAscolto(event, <?php echo $p['id']; ?>); window.open('<?php echo $p['url_spotify']; ?>', '_blank')">
                            <div class="spotify-icon"></div>
                            <span><?php echo htmlspecialchars($p['titolo']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <?php include '../includes/footer.php'; ?>
    </div>

    <div id="activity-modal" class="modal activity-overlay">
        <div class="modal-content game-modal-content">
            <span class="close-btn-activity" onclick="chiudiAttivita()">&times;</span>
            <iframe id="game-frame" src="about:blank"></iframe>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const items = document.querySelectorAll('.activity-item');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const filtro = this.getAttribute('data-filter');

                    items.forEach(item => {
                        const durataItem = item.getAttribute('data-durata');
                        item.style.display = (filtro === 'all' || filtro === durataItem) ? 'flex' : 'none';
                    });
                });
            });
        });
    </script>
    <script src="../js/activities.js"></script><?php include '../includes/scripts.php'; ?>
</body>
</html>