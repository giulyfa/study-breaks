<?php
require_once 'config.php';

// Recupera l'attività dal database tramite lo slug nell'URL
$slug = isset($_GET['name']) ? sanitize($_GET['name']) : '';
$stmt = $pdo->prepare("SELECT * FROM attivita WHERE slug = ?");
$stmt->execute([$slug]);
$attivita = $stmt->fetch();

if (!$attivita) {
    die("Attività non trovata.");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css"> <title><?php echo $attivita['titolo']; ?> - Study Breaks</title>
</head>
<body class="activity-player-body">
    
    <div id="activity-overlay" class="overlay-container">
        <div class="canvas-wrapper">
            <h2><?php echo $attivita['titolo']; ?></h2>
            
            <canvas id="gameCanvas" width="400" height="400"></canvas>
            
            <div class="game-ui">
                <p>Tempo stimato: <?php echo $attivita['durata']; ?> min</p>
                <button onclick="window.location.href='home.php'" class="btn">Esci</button>
            </div>
        </div>
    </div>

    <script>
        // Passiamo variabili PHP a JS per la logica interna
        const ACTIVITY_SLUG = "<?php echo $attivita['slug']; ?>";
    </script>
    <script src="js/activities/<?php echo $attivita['slug']; ?>.js"></script>
</body>
</html>