<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$periodo = $_GET['periodo'] ?? 'sempre';

$whereAttivita = "";
$wherePlaylist = "";
$btnSempre = "";
$btnOggi = ""; 
$btnSettimana = ""; 
$btnMese = "";

switch ($periodo) {
    case 'oggi':
        $whereAttivita = " AND DATE(data_ora) = CURDATE()";
        $wherePlaylist = " AND DATE(L.data_click) = CURDATE()";
        $btnOggi = "active";
        break;
    case 'settimana':
        $whereAttivita = " AND data_ora >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $wherePlaylist = " AND L.data_click >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $btnSettimana = "active";
        break;
    case 'mese':
        $whereAttivita = " AND data_ora >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $wherePlaylist = " AND L.data_click >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $btnMese = "active";
        break;
    default:
        $whereAttivita = "";
        $wherePlaylist = "";
        $btnSempre = "active";
        break;
}


$sql = "SELECT nome_attivita, COUNT(*) as totale 
    FROM attivita_svolte 
    WHERE categoria != 'studio' $whereAttivita
    GROUP BY nome_attivita 
    ORDER BY totale DESC 
    LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$attivita = $stmt->fetchAll();

$sql = "SELECT P.titolo, COUNT(*) as totale 
    FROM playlist P 
    JOIN log_ascolti L ON P.id = L.id_playlist 
    WHERE 1=1 $wherePlaylist
    GROUP BY P.titolo 
    ORDER BY totale DESC 
    LIMIT 5";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$playlists = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nothing+You+Could+Do&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Statistiche Admin - Study Breaks</title>
</head>
<body>
    <div class="admin-page">
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/sidebar_admin.php'; ?>

        <main class="admin-container">
            <section class="admin-intro">
                <h2>Statistiche generali attività</h2>
            </section>

            <form method="GET" action="statistiche.php" class="time-selector">
                <button type="submit" name="periodo" value="sempre" class="time-btn <?php echo $btnSempre; ?>">Sempre</button>
                <button type="submit" name="periodo" value="oggi" class="time-btn <?php echo $btnOggi; ?>">Oggi</button>
                <button type="submit" name="periodo" value="settimana" class="time-btn <?php echo $btnSettimana; ?>">Settimana</button>
                <button type="submit" name="periodo" value="mese" class="time-btn <?php echo $btnMese; ?>">Mese</button>
            </form>

            
            <section class="admin-section stats-box">
                <div class="table-container">
                    <h3>Attività più giocate</h3>
                    <table class="user-table-stats">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Attività</th>
                                <th style="text-align: right;">Svolgimenti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($attivita) > 0): ?>
                                <?php $rank = 1; foreach ($attivita as $row): ?>
                                <tr>
                                    <td><strong><?php echo $rank++; ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['nome_attivita']); ?></td>
                                    <td style="text-align: right;"><?php echo $row['totale']; ?> volte</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 20px;">
                                        Nessuna attività registrata in questo periodo.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <hr style="border: 0; margin: 30px 0;">

            <section class="admin-section stats-box">
                <div class="table-container">
                    <h3>Playlist più ascoltate</h3>
                    <table class="user-table-stats">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Playlist</th>
                                <th style="text-align: right;">Ascolti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($playlists) > 0): ?>
                                <?php $rank = 1; foreach ($playlists as $p): ?>
                                <tr>
                                    <td><strong><?php echo $rank++; ?></strong></td>
                                    <td><?php echo htmlspecialchars($p['titolo']); ?></td>
                                    <td style="text-align: right;"><?php echo $p['totale']; ?> volte</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 20px;">
                                        Nessun ascolto registrato in questo periodo.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="admin-navigation" style="margin-top: 50px; display: flex; flex-direction: column; gap: 15px;">
                <a href="admin_dashboard.php" class="btn primary-btn" style="width: 100%; text-align: center; text-decoration: none;">Torna alla Dashboard</a>
            </div>
        </main>

        <?php include 'includes/footer_simple.php'?>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>