<?php
require_once 'config.php'; 

// Controllo accesso: se non è loggato torna alla index
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$successo = false;
$errore = "";

// Gestione dell'invio del modulo
// Gestione dell'invio del modulo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = trim($_POST['titolo'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $durata = intval($_POST['durata_selezionata'] ?? 3); // Recupera il valore dal campo nascosto
    $descrizione = trim($_POST['descrizione'] ?? '');
    $istruzioni = trim($_POST['istruzioni'] ?? '');

    if (!empty($titolo) && !empty($tipo) && !empty($descrizione)) {
        try {
            // AGGIORNATA: Inseriamo anche la colonna 'durata'
            $stmt = $pdo->prepare("INSERT INTO proposte (id_utente, nome_attivita, categoria, durata, descrizione, link_suggerito) VALUES (?, ?, ?, ?, ?, ?)");
            
            // Passiamo i 6 parametri corrispondenti ai ?
            if ($stmt->execute([$user_id, $titolo, $tipo, $durata, $descrizione, $istruzioni])) {
                $successo = true;
            }
        } catch (PDOException $e) {
            $errore = "Errore nel salvataggio: " . $e->getMessage();
        }
    } else {
        $errore = "Per favore, compila tutti i campi obbligatori.";
    }
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
    <title>Proposta - Study Breaks</title>
    <style>
        /* Stili specifici per rendere i bottoni durata interattivi */
        .dur-btn {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dur-btn.active {
            background-color: #E49A7D !important;
            color: white !important;
            border-color: #E49A7D !important;
            transform: scale(1.1);
        }
        .success-banner {
            background-color: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 25px;
            border: 1px solid #c3e6cb;
        }
        .error-banner {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="proposal-page">
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

        <main class="proposal-container">
            <div class="proposal-banner">
                <h2>Proponi un’attività</h2>
                <p>Hai una nuova idea innovativa? Proponila!</p>
            </div>

            <?php if ($successo): ?>
                <div class="success-banner">
                    <strong>Ottimo lavoro!</strong> La tua proposta è stata inviata all'admin per la revisione. 
                    <br><br>
                    <a href="home.php" class="footer-link" style="text-decoration: underline;">Torna alla Home</a>
                </div>
            <?php endif; ?>

            <?php if ($errore): ?>
                <div class="error-banner"><?php echo $errore; ?></div>
            <?php endif; ?>

            <div class="proposal-content-wrapper" <?php if($successo) echo 'style="display:none;"'; ?>>
                <section class="form-section">
                    <form action="proposta.php" method="POST" class="proposal-form" id="form-proposta">
                        <div class="form-group">
                            <label>Titolo dell’Attività</label>
                            <input type="text" name="titolo" required placeholder="Es. Stretching per occhi per chi studia al PC">
                        </div>

                        <div class="form-group">
                            <label>Tipo di Attività</label>
                            <select name="tipo" required>
                                <option value="" disabled selected>Seleziona un tipo</option>
                                <option value="gioco">Gioco</option>
                                <option value="relax">Relax</option>
                                <option value="fisico">Esercizio Fisico</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Durata (minuti)</label>
                            <div class="duration-selector">
                                <button type="button" class="dur-btn" data-val="1">1</button>
                                <button type="button" class="dur-btn" data-val="2">2</button>
                                <button type="button" class="dur-btn" data-val="3">3</button>
                                <button type="button" class="dur-btn" data-val="4">4</button>
                                <button type="button" class="dur-btn" data-val="5">5</button>
                            </div>
                            <input type="hidden" name="durata_selezionata" id="durata_input" value="3">
                        </div>

                        <div class="form-group">
                            <label>Descrizione breve</label>
                            <textarea name="descrizione" required placeholder="Spiega in poche parole di cosa si tratta"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Dettagli/Istruzioni (opzionale)</label>
                            <textarea name="istruzioni" placeholder="Aggiungi istruzioni, link o altre informazioni utili"></textarea>
                        </div>

                        <p class="form-disclaimer">
                            Suggerimento: Le attività più apprezzate sono semplici e veloci. Pensa a qualcosa che faresti durante una pausa dallo studio!
                        </p>

                        <button type="submit" class="submit-proposal-btn">Invia proposta</button>
                        <p class="admin-note">L’admin esaminerà la tua proposta prima di pubblicarla</p>
                    </form>
                </section>

                <aside class="examples-section">
                    <h3 class="example-title">Esempi attività</h3>
                    
                    <div class="example-card card-orange">
                        <div class="ex-text">
                            <strong>Rotazione polsi</strong>
                            <span>2 min – Perfetto per chi scrive molto</span>
                        </div>
                    </div>

                    <div class="example-card card-green">
                        <div class="ex-text">
                            <strong>Quiz capitali del mondo</strong>
                            <span>5 min – Cultura generale rilassante</span>
                        </div>
                    </div>

                    <div class="example-card card-yellow">
                        <div class="ex-text">
                            <strong>Color match</strong>
                            <span>2 min – Abbina i colori velocemente</span>
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        <footer>
            <nav class="footer-links">
                <a href="home.php" class="footer-link">Home</a>
                <a href="activities.php" class="footer-link">Attività</a>
                <a href="profile.php" class="footer-link">Profilo</a>
                <a href="proposta.php" class="footer-link">Proposta</a><br>
                <a href="about.php" class="footer-link about-link">Chi siamo?</a>
            </nav>
        </footer>
    </div>

    <script>
        // Gestione della selezione della durata
        const durBtns = document.querySelectorAll('.dur-btn');
        const durInput = document.getElementById('durata_input');

        durBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Rimuovi classe active da tutti
                durBtns.forEach(b => b.classList.remove('active'));
                // Aggiungi al cliccato
                this.classList.add('active');
                // Salva valore nell'input nascosto
                durInput.value = this.getAttribute('data-val');
            });
        });

        // Imposta il valore predefinito (3 min) come attivo al caricamento
        document.addEventListener('DOMContentLoaded', () => {
            const defaultBtn = document.querySelector('.dur-btn[data-val="3"]');
            if (defaultBtn) defaultBtn.classList.add('active');
        });
    </script>
    <script src="js/global.js"></script>
</body>
</html>