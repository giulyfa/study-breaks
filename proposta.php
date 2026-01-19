<?php
require_once 'config.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'studente') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$successo = false;
$errore = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = trim($_POST['titolo'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $durata = intval($_POST['durata_selezionata'] ?? 3); 
    $descrizione = trim($_POST['descrizione'] ?? '');
    $istruzioni = trim($_POST['istruzioni'] ?? '');

    if (!empty($titolo) && !empty($tipo) && !empty($descrizione)) {
        try {
            // CONTROLLO DUPLICATO RECENTE
            $check = $pdo->prepare("SELECT id FROM proposte 
                                    WHERE id_utente = ? 
                                    AND nome_attivita = ? 
                                    AND data_proposta > DATE_SUB(NOW(), INTERVAL 2 MINUTE)");
            $check->execute([$user_id, $titolo]);

            if ($check->rowCount() > 0) {
                $errore = "Hai già inviato questa proposta un momento fa. Controlla se è stata registrata!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO proposte (id_utente, nome_attivita, categoria, durata, descrizione, link_suggerito) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$user_id, $titolo, $tipo, $durata, $descrizione, $istruzioni])) {
                    $successo = true;
                }
            }
        } catch (PDOException $e) {
            $errore = "Errore nel salvataggio: " . $e->getMessage();
        }
    } else {
        $errore = "Per favore, compila tutti i campi obbligatori.";
    }
}

// RECUPERO ATTIVITÀ APPROVATE "IN ARRIVO"
$stmtSoon = $pdo->prepare("
    SELECT a.titolo, a.tipo 
    FROM attivita a
    INNER JOIN proposte p ON a.titolo = p.nome_attivita COLLATE utf8mb4_unicode_ci
    WHERE a.stato = 'disattivata' 
    AND p.stato = 'approvata'
    ORDER BY a.id DESC 
    LIMIT 2
");
$stmtSoon->execute();
$in_arrivo = $stmtSoon->fetchAll();
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
</head>
<body>
    <div class="proposal-page">
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/sidebar.php'; ?>

        <main class="proposal-container">
            <div class="proposal-banner">
                <h2>Proponi un'attività</h2>
                <p>Hai una nuova idea innovativa? Proponila!</p>
            </div>

            <aside class="examples-section">
                <?php if (!empty($in_arrivo)): ?>
                    <div class="coming-soon-box">
                        <h3 class="example-title">Approvate di recente:</h3>
                        <div class="coming-soon-wrapper">
                            <?php foreach($in_arrivo as $pro): ?>
                                <div class="mini-soon-card">
                                    <span class="badge-new">In lavorazione</span>
                                    <strong><?= htmlspecialchars($pro['titolo']) ?></strong>
                                    <small><?= htmlspecialchars($pro['tipo']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="community-hint">L'admin le sta preparando!</p>
                    </div>
                <?php else: ?>
                    <div class="no-activities">
                        <p>Al momento non ci sono nuove attività in arrivo.</p>
                    </div>
                <?php endif; ?>
            </aside>

            <?php if ($successo): ?>
                <div class="success-banner">
                    <strong>Ottimo lavoro!</strong> La tua proposta è stata inviata all'admin per la revisione. <br><br>
                    <a href="home.php" class="footer-link" style="color: #155724;text-decoration: underline;">Torna alla Home</a>
                </div>
            <?php else: ?>
                <?php if ($errore): ?>
                    <div class="error-banner"><?php echo $errore; ?></div>
                <?php endif; ?>

                <div class="proposal-content-wrapper">
                    <div class="form-section">
                        <form action="proposta.php" method="POST" class="proposal-form" id="form-proposta">
                            <div class="form-group">
                                <label>Titolo dell'Attività</label>
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
                                <?php foreach([1,2,3,4,5] as $m): ?>
                                    <button type="button" class="dur-btn" data-val="<?= $m ?>"><?= $m ?></button>
                                <?php endforeach; ?>
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
                        <p class="admin-note">L'admin esaminerà la tua proposta prima di pubblicarla</p>
                        </form>
                    </div>

                    <aside class="examples-section">
                        <h3 class="example-title">Esempi attività</h3>
                        <div class="example-card card-orange">
                            <div class="ex-text">
                                <strong>Rotazione polsi</strong>
                                <span>2 min - Perfetto per chi scrive molto</span>
                            </div>
                        </div>
                        <div class="example-card card-green">
                            <div class="ex-text">
                                <strong>Quiz capitali del mondo</strong>
                                <span>5 min - Cultura generale rilassante</span>
                            </div>
                        </div>
                        <div class="example-card card-yellow">
                            <div class="ex-text">
                                <strong>Color match</strong>
                                <span>2 min - Abbina i colori velocemente</span>
                            </div>
                        </div>
                    </aside>
                </div>
            <?php endif; ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>

    <script>
        const durBtns = document.querySelectorAll('.dur-btn');
        const durInput = document.getElementById('durata_input');

        durBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                durBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                durInput.value = this.getAttribute('data-val');
            });
        });

        document.querySelector('.dur-btn[data-val="3"]').classList.add('active');
    </script>
    <?php include 'includes/scripts.php'; ?>
</body>
</html>
