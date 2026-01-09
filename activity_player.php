<?php
require_once 'config.php';

// Recupera l'attività dal database tramite lo slug nell'URL
$slug = isset($_GET['name']) ? $_GET['name'] : '';
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
            overflow: hidden; 
        }
        
        h2 { margin-bottom: 15px; color: #333; }
        
        canvas {
            background-color: #f4f4f4;
            border: 2px solid #4D7D72; 
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
            font-family: 'Quicksand', sans-serif;
        }

        /* --- STILI PER LA SCHERMATA DI AVVIO --- */
        #start-screen {
            text-align: center;
            max-width: 400px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 15px;
            border: 1px solid #ddd;
        }

        .description-text {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .btn-start {
            background-color: #4D7D72; /* Verde del tema */
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
            font-family: 'Quicksand', sans-serif;
        }

        .btn-start:hover {
            background-color: #3b635a;
            transform: scale(1.05);
        }

        /* Contenitore del gioco nascosto all'inizio */
        #game-wrapper {
            display: none;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body>

    <h2><?php echo htmlspecialchars($attivita['titolo']); ?></h2>
    
    <div id="start-screen">
        <p class="description-text">
            <?php
            echo !empty($attivita['descrizione']) 
                ? htmlspecialchars($attivita['descrizione']) 
                : "Rilassati e divertiti con questa attività!"; //scritta di default in caso manchi la descrizione nel db
            ?>
        </p>
        <button id="btn-start" class="btn-start">Inizia</button>
    </div>

    <div id="game-wrapper">
        <canvas id="gameCanvas" width="500" height="400"></canvas>
        
        <div class="game-ui">
            <p>Tempo stimato: <?php echo htmlspecialchars($attivita['durata']); ?> min</p>
            <button onclick="window.parent.chiudiAttivita()" class="btn-exit">Esci</button>
        </div>
    </div>

    <script>
        // Passiamo lo slug a JS
        const ACTIVITY_SLUG = "<?php echo $attivita['slug']; ?>";

        // Gestione del click su "INIZIA"
        document.getElementById('btn-start').addEventListener('click', function() {
            document.getElementById('start-screen').style.display = 'none';
            
            document.getElementById('game-wrapper').style.display = 'flex';
            
            var script = document.createElement('script');
            script.src = "js/activities/" + ACTIVITY_SLUG + ".js";
            document.body.appendChild(script);
        });
    </script>
    
    </body>
</html>