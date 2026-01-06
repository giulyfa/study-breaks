<?php
require_once 'config.php';

// Recupera l'attività dal database tramite lo slug nell'URL
$slug = isset($_GET['name']) ? $_GET['name'] : '';
// (Opzionale) Sanificazione base
$slug = htmlspecialchars($slug);

$stmt = $pdo->prepare("SELECT * FROM attivita WHERE slug = ?");
$stmt->execute([$slug]);
$attivita = $stmt->fetch();

if (!$attivita) {
    die("Attività non trovata nel database (controlla lo slug).");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($attivita['titolo']); ?> - Study Breaks</title>
    <style>
        /* CSS specifico per centrare il gioco dentro l'iframe */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #ffffff;
            font-family: 'Quicksand', sans-serif;
            overflow: hidden; /* Evita scrollbar doppie */
        }
        
        h2 { margin-bottom: 10px; color: #333; }
        
        canvas {
            background-color: #f4f4f4;
            border: 2px solid #4D7D72; /* Colore del tuo tema */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: block;
        }

        .game-ui {
            margin-top: 15px;
            text-align: center;
        }

        .btn-exit {
            background-color: #E49A7D;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <h2><?php echo htmlspecialchars($attivita['titolo']); ?></h2>
    
    <canvas id="gameCanvas" width="500" height="400"></canvas>
    
    <div class="game-ui">
        <p>Tempo stimato: <?php echo htmlspecialchars($attivita['durata']); ?> min</p>
        <button onclick="window.parent.chiudiAttivita()" class="btn-exit">Esci</button>
    </div>

    <script>
        // Passiamo lo slug a JS
        const ACTIVITY_SLUG = "<?php echo $attivita['slug']; ?>";
    </script>
    
    <script src="js/activities/<?php echo $attivita['slug']; ?>.js"></script>

</body>
</html>