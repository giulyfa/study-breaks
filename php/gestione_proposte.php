<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// GESTIONE AZIONI
if (isset($_GET['azione']) && isset($_GET['id'])) {
    $id_proposta = intval($_GET['id']);
    
    if ($_GET['azione'] === 'approva') {
        $stmt = $pdo->prepare("SELECT * FROM proposte WHERE id = ?");
        $stmt->execute([$id_proposta]);
        $p = $stmt->fetch();

        if ($p) {
            $upd = $pdo->prepare("UPDATE proposte SET stato = 'approvata' WHERE id = ?");
            $upd->execute([$id_proposta]);

             // Creiamo uno slug provvisorio basato sul titolo
            $slug_provvisorio = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $p['nome_attivita'])));

            // Inseriamo l'attività nella tabella 'attivita' come DISATTIVATA (attiva = 0)
            $ins = $pdo->prepare("INSERT INTO attivita (titolo, slug, tipo, durata, descrizione, stato, data_creazione) VALUES (?, ?, ?, ?, ?, 'disattivata', NOW())");
            
            $ins->execute([
                $p['nome_attivita'], 
                $slug_provvisorio,
                $p['categoria'], 
                $p['durata'], 
                $p['descrizione'],
            ]);
        }
    } 
    elseif ($_GET['azione'] === 'rifiuta') {
        $upd = $pdo->prepare("UPDATE proposte SET stato = 'rifiutata' WHERE id = ?");
        $upd->execute([$id_proposta]);
    }
    
    header("Location: gestione_proposte.php");
    exit;
}

// RECUPERO PROPOSTE IN ATTESA
$query = "SELECT p.*, CONCAT(u.nome, ' ', u.cognome) AS autore
          FROM proposte p 
          JOIN utenti u ON p.id_utente = u.id 
          WHERE p.stato = 'in_attesa' 
          ORDER BY p.data_proposta DESC";
$stmt = $pdo->query($query);
$proposte = $stmt->fetchAll();
$totale_attesa = count($proposte);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <title>In attesa - Study Breaks</title>
</head>
<body>
    <div class="admin-page">
        <?php include '../includes/header.php'; ?>
        <?php include '../includes/sidebar_admin.php'; ?>

        <main class="admin-container">
            <section class="admin-intro">
                <h1>Contenuti in attesa di approvazione</h1>
            </section>

            <div class="badge-count">In attesa: <?php echo $totale_attesa; ?></div>

            <?php if ($totale_attesa == 0): ?>
                <p style="text-align:center; margin-top:30px; color:#666;">Ottimo lavoro! Non ci sono proposte da revisionare.</p>
            <?php else: ?>
                <?php foreach ($proposte as $p): ?>
                    <article class="approval-card">
                        <div class="approval-header">
                            <h2><?php echo htmlspecialchars($p['nome_attivita']); ?> - <?php echo $p['durata']; ?> min</h2>
                            
                            <p class="meta-info">
                                Inviata da: <strong><?php echo htmlspecialchars($p['autore']); ?></strong> 
                                • Data: 
                                <?php 
                                    echo date('d/m/Y', strtotime($p['data_proposta'])); 
                                ?>
                            </p>
                        </div>
                        
                        <div class="approval-body">
                            <p><?php echo nl2br(htmlspecialchars($p['descrizione'])); ?></p>
                            
                            <?php if (!empty($p['link_suggerito'])): ?>
                                <div class="extra-instructions">
                                    <strong>Istruzioni extra:</strong>
                                    <p>
                                        <?php 
                                            $testo = htmlspecialchars($p['link_suggerito']);
                                
                                            $testo_con_link = preg_replace(
                                                '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', 
                                                '<a href="$1" target="_blank" style="color: #4D7D72; font-weight: bold; text-decoration: underline;">$1</a>', 
                                                $testo
                                            );
                                            
                                            echo nl2br($testo_con_link); 
                                        ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="approval-footer">
                            <span class="steps-info">Categoria: <?php echo htmlspecialchars($p['categoria']); ?></span>
                            <div class="action-buttons">
                                <a href="gestione_proposte.php?azione=approva&id=<?php echo $p['id']; ?>" 
                                class="admin-btn approve">Approva</a>
                                <a href="gestione_proposte.php?azione=rifiuta&id=<?php echo $p['id']; ?>" 
                                class="admin-btn reject" 
                                onclick="return confirm('Sicuro di voler rifiutare?')">Rifiuta</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="admin-navigation">
                <a href="admin_dashboard.php" class="btn primary-btn">Torna alla Dashboard</a>
            </div>
        </main>
        <?php include '../includes/footer_simple.php'?>
    </div>
    <?php include '../includes/scripts.php'; ?>
</body>
</html>
