<?php
require_once 'config.php';

// Sicurezza: Solo l'admin può accedere (Assumendo che tu abbia una colonna 'ruolo' o simile)
// Per ora lo lasciamo aperto, ma in futuro aggiungeremo un controllo se $_SESSION['ruolo'] == 'admin'

// 1. Recupero tutte le Micro-attività
$stmtAtt = $pdo->query("SELECT * FROM attivita ORDER BY id DESC");
$attivita = $stmtAtt->fetchAll();

// 2. Recupero tutte le Playlist
$stmtPlay = $pdo->query("SELECT * FROM playlist ORDER BY id DESC");
$playlists = $stmtPlay->fetchAll();
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
    <title>Dashboard Admin - Study Breaks</title>
</head>
<body>
    <div class="admin-page">
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/sidebar.php'; ?>

        <main class="admin-container">
            <section class="admin-section">
                <h2>Micro-attività</h2>
                <div class="table-container">
                    <table class="user-table admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Attività</th>
                                <th>Tipo</th>
                                <th>Durata</th>
                                <th>Stato</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($attivita as $row): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['titolo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                <td><?php echo $row['durata']; ?> min</td>
                                <td>
                                    <span class="status-badge <?php echo $row['stato']; ?>">
                                        <?php echo $row['stato']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-icon">📝</button> 
                                    <button class="action-icon" onclick="eliminaAttivita(<?php echo $row['id']; ?>)">🗑️</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button class="propose-btn" style="margin-top: 25px;">+ Nuova Attività</button>
            </section>

            <section class="admin-section" style="margin-top: 40px;">
                <h2>Playlist</h2>
                <div class="table-container">
                    <table class="user-table admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Titolo</th>
                                <th>Stato</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($playlists as $p): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['titolo']); ?></strong></td>
                                <td>
                                    <span class="status-badge <?php echo ($p['attiva'] ? 'active' : 'inactive'); ?>">
                                        <?php echo ($p['attiva'] ? 'attiva' : 'disattiva'); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-icon">📝</button> 
                                    <button class="action-icon">🗑️</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button class="propose-btn" style="margin-top: 25px;">+ Nuova Playlist</button>
            </section>

            <section class="admin-navigation" style="margin-top: 50px; display: flex; flex-direction: column; gap: 15px;">
                <a href="approvazione_attivita.php" class="btn primary-btn">Vai alle proposte degli utenti</a>
                <a href="gestione_utenti.php" class="btn primary-btn">Vai alla gestione degli utenti</a>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
