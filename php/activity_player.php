<?php
require_once 'config.php';

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
        /* CSS RESET & BODY */
        body {
            margin: 0;
            padding: 10px; /* Un po' di padding per non attaccarsi ai bordi */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #ffffff;
            font-family: 'Quicksand', sans-serif;
            overflow: hidden; /* Evita scrollbar indesiderate */
            box-sizing: border-box;
        }
        
        h2 { 
            margin-bottom: 15px; 
            color: #333; 
            text-align: center;
            font-size: 1.5rem;
            flex-shrink: 0; /* Impedisce al titolo di schiacciarsi troppo */
        }
        
        #game-wrapper {
            display: none;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 500px;
            max-height: 100%;

        }

        #gameCanvas {
            background-color: #f4f4f4;
            border: 2px solid #4D7D72;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: block;
            aspect-ratio: 1 / 1;
            width: auto;
            height: auto;
            touch-action: none;
        }

        .game-ui {
            margin-top: 10px;
            text-align: center;
            width: 100%;
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
            width: 90%;
            max-width: 400px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .description-text {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .btn-start {
            background-color: #4D7D72;
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

        @media (max-height: 600px) {
            h2 {
                font-size: 1.2rem; /* Titolo più piccolo */
                margin-bottom: 5px;
            }
            .game-ui p {
                display: none; /* Nascondi "Tempo stimato" per salvare spazio */
            }
            canvas {
                max-height: 80vh; /* Dai più spazio al canvas */
            }
        }

        @media (max-height: 450px) {
            h2 {
                display: none; 
            }
            body {
                padding: 5px;
            }
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
                : "Rilassati e divertiti con questa attività!"; 
            ?>
        </p>
        <button id="btn-start" class="btn-start">Inizia</button>
    </div>

    <div id="game-wrapper">
        <canvas id="gameCanvas" width="500" height="500"></canvas>
        
        <div class="game-ui">
            <p>Tempo stimato: <?php echo htmlspecialchars($attivita['durata']); ?> min</p>
            <button onclick="window.parent.chiudiAttivita()" class="btn-exit">Esci</button>
        </div>
    </div>

    <script>
        
        const ACTIVITY_SLUG = "<?php echo $attivita['slug']; ?>";
        const ACTIVITY_ID = <?php echo $attivita['id']; ?>;
        const ACTIVITY_NAME = "<?php echo addslashes($attivita['titolo']); ?>"; 
        const ACTIVITY_TYPE = "<?php echo $attivita['tipo']; ?>"; 
        const ACTIVITY_DURATION = <?php echo $attivita['durata']; ?>;

        document.getElementById('btn-start').addEventListener('click', function() {
            document.getElementById('start-screen').style.display = 'none';
            document.getElementById('game-wrapper').style.display = 'flex';
            
            var script = document.createElement('script');
            script.src = "../js/activities/" + ACTIVITY_SLUG + ".js?v=" + new Date().getTime(); 
            document.body.appendChild(script);

            const params = new URLSearchParams({
                azione: 'attivita',
                id_att: ACTIVITY_ID,
                nome: ACTIVITY_NAME,
                categoria: ACTIVITY_TYPE, 
                durata: ACTIVITY_DURATION
            });

            fetch('salva_dati.php?' + params.toString())
                .then(() => {
                    // SE il quadratino esiste nella pagina madre, lo aggiorniamo
                    if (window.parent) {
                        let actTodaySpan = window.parent.document.getElementById('activities-today-count');
                        if (actTodaySpan) {
                            let valore = parseInt(actTodaySpan.textContent) || 0;
                            actTodaySpan.textContent = valore + 1;
                        }
                    }
                })
                .catch(err => console.log("Salvataggio silenzioso fallito, ma il gioco parte"));
        });
    </script>

    </body>
</html>