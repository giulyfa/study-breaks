<?php
require_once 'config.php'; // Carica sessione e connessione al DB

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Recuperiamo l'ID dell'utente
$oggi = date('Y-m-d'); 

// 3. RECUPERO ATTIVITÀ
// Recuperiamo le attività per visualizzarle nella griglia
$stmt = $pdo->query("SELECT id, slug, titolo, tipo, durata FROM attivita WHERE stato = 'attivo'");
$attivita = $stmt->fetchAll();

// RECUPERO PLAYLIST
$stmtP = $pdo->query("SELECT * FROM playlist WHERE attiva = 1");
$playlists = $stmtP->fetchAll();
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
    <title>Attività - Study Breaks</title>
</head>
<body>
    <div class="activity-page">
        <?php include 'includes/header.php'; ?>

        <?php include 'includes/sidebar.php'; ?>

        <div class="content-area">
        <section class="activity-section">
            <div class="section-header">
                <h2>Attività</h2>
            </div>

            <div class="filters">
                <button class="filter-btn active" data-filter="all">Tutte</button>
                <button class="filter-btn" data-filter="1">1 min</button>
                <button class="filter-btn" data-filter="2">2 min</button>
                <button class="filter-btn" data-filter="3">3 min</button>
                <button class="filter-btn" data-filter="5">5 min</button>
            </div>

            <div class="activity-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM attivita WHERE stato = 'attiva'");

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $imagePath = 'img/' . $row['slug'] . '.jpg';
                    
                    if (!file_exists($imagePath)) {
                        $imagePath = 'img/logo.png';
                    }
                    
                    // Prepariamo i dati per il JavaScript
                    $id = $row['id'];
                    $slug = $row['slug'];
                    $titolo = addslashes($row['titolo']); // addslashes evita errori se il titolo ha apostrofi
                    $tipo = $row['tipo']; // Assicurati che la colonna nel DB si chiami 'tipo'
                    $durata = $row['durata'];
                ?>

                <div class="activity-item"
                    data-durata="<?php echo $durata; ?>"
                    onclick="apriAttivita(<?php echo $id; ?>, '<?php echo $slug; ?>', '<?php echo $titolo; ?>', '<?php echo $tipo; ?>', <?php echo $durata; ?>)" 
                    style="cursor: pointer;">
                    
                    <div class="activity-icon">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['titolo']); ?>">
                    </div>
                    <p><?php echo htmlspecialchars($row['titolo']); ?> - <?php echo $row['durata']; ?> min</p>
                </div>

                <?php 
                    } 
                ?>
            </div>

            <div class="suggestion-section">
                <p>Hai un'idea per una nuova attività?</p>
                <p class="small-text">Proponi la tua micro-attività e sarà valutata dall'admin</p>
                <button class="propose-btn" onclick="window.location.href='proposta.php'">Proponi nuova attività</button>
                <p class="help">Aiutaci a migliorare!</p>
            </div>
        </section>

        <section class="playlist-section">
            <p class="playlist-intro">Oppure...</p>
            <h3>Rilassati con una playlist!</h3>
            
            <div class="spotify-container">
                <?php foreach ($playlists as $p): ?>
                    <div class="spotify-card" 
                        onclick="registraAscolto(event, <?php echo $p['id']; ?>); window.open('<?php echo $p['url_spotify']; ?>', '_blank')" 
                        style="cursor: pointer;">
                        <div class="spotify-icon"></div>
                        <span><?php echo htmlspecialchars($p['titolo']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        </div>

        <?php include 'includes/footer.php'; ?>

    </div>

    <div id="activity-modal" class="modal activity-overlay">
        <div class="modal-content game-modal-content">
            <span class="close-btn-activity" onclick="chiudiAttivita()">&times;</span>
            
            <iframe id="game-frame" src="" frameborder="0"></iframe>
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

                        // Togliamo le transizioni JS che creano artefatti
                        if (filtro === 'all' || filtro === durataItem) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });


        // Variabile globale per memorizzare i dati dell'attività aperta
        let attivitaCorrente = { id: 0, nome: '', tipo: '', durata: 0 };
        let tempoInizioAttivita = 0;

        function apriAttivita(id, slug, titolo, tipo, durata) {
            const modal = document.getElementById('activity-modal');
            const iframe = document.getElementById('game-frame');
            
            // Memorizziamo i dati dell'attività per usarli nella chiusura
            attivitaCorrente.id = id;
            attivitaCorrente.nome = titolo;
            attivitaCorrente.tipo = tipo;
            attivitaCorrente.durata = durata;
            
            // Imposta la sorgente dell'iframe
            iframe.src = 'activity_player.php?name=' + slug;
            
            // Mostra il modale
            modal.style.display = 'block';

            // Segnamo l'orario di inizio
            tempoInizioAttivita = Date.now();
        }

        function chiudiAttivita() {
            const modal = document.getElementById('activity-modal');
            const iframe = document.getElementById('game-frame');

            const secondiPassati = (Date.now() - tempoInizioAttivita) / 1000;

            // CONDIZIONE: 30 secondi per convalidare
            if (secondiPassati >= 30) {
                // Aggiornamento attività in tempo reale
                let actTodaySpan = document.getElementById('activities-today-count'); 
                
                if(actTodaySpan) {
                    // Incrementa il numero che l'utente vede nel quadratino bianco
                    actTodaySpan.textContent = parseInt(actTodaySpan.textContent) + 1;
                    
                    // Prepariamo i dati per il database (questo rimane uguale, è perfetto)
                    const params = new URLSearchParams({
                        azione: 'attivita',
                        id_att: attivitaCorrente.id,
                        nome: attivitaCorrente.nome,
                        categoria: attivitaCorrente.tipo,
                        durata: attivitaCorrente.durata
                    });

                    // Invio al server: il PHP si occuperà di aumentare sia il totale nel DB 
                    // sia la variabile $_SESSION['attivita_oggi']
                    fetch('salva_dati.php?' + params.toString());
                }
            }   

            modal.style.display = 'none';
            iframe.src = ''; 
        }

        // Funzione per registrare l'ascolto (identica alla Home)
        function registraAscolto(event, id) {
            const url = `salva_dati.php?azione=log_playlist&id_p=${id}`;
            if (navigator.sendBeacon) {
                navigator.sendBeacon(url);
            } else {
                fetch(url);
            }
            return true; 
        }

        // Chiude se si clicca fuori dal box del gioco
        window.onclick = function(event) {
            const modal = document.getElementById('activity-modal');
            if (event.target == modal) {
                chiudiAttivita();
            }
        }
    </script>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>