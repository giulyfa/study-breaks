<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// RECUPERO DATI 
$attivita = $pdo->query("SELECT * FROM attivita ORDER BY id DESC")->fetchAll();
$playlists = $pdo->query("SELECT * FROM playlist ORDER BY id DESC")->fetchAll();
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
    <title>Dashboard Admin - Study Breaks</title>
</head>
<body>
    <div class="admin-page">
        <?php include '../includes/header.php'; ?>
        <?php include '../includes/sidebar_admin.php'; ?>

        <main class="admin-container">
            <section class="admin-section">
                <h1>Micro-attività</h1>
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
                                    <button class="action-icon" onclick='apriModifica(<?php echo json_encode($row); ?>)'>📝</button> 
                                    <button class="action-icon" onclick="eliminaElemento(<?php echo $row['id']; ?>, 'attivita')">🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button class="propose-btn" style="margin-top: 25px;" onclick="apriNuovaAttivita()">+ Nuova Attività</button>
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
                                    <span class="status-badge <?php echo ($p['attiva'] ? 'attiva' : 'disattivata'); ?>">
                                        <?php echo ($p['attiva'] ? 'attiva' : 'disattivata'); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-icon" onclick='apriModificaPlaylist(<?php echo json_encode($p); ?>)'>📝</button>
                                    <button class="action-icon" onclick="eliminaElemento(<?php echo $p['id']; ?>, 'playlist')">🗑️</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button class="propose-btn" style="margin-top: 25px;" onclick="apriNuovaPlaylist()">+ Nuova Playlist</button>
            </section>

            <div class="admin-navigation">
                <a href="gestione_proposte.php" class="btn primary-btn">Vai alle proposte degli utenti</a>
                <a href="utenti.php" class="btn primary-btn">Vai alla gestione degli utenti</a>
                <a href="statistiche.php" class="btn primary-btn">Vai alle statistiche degli utenti</a>
            </div>
        </main>

        <?php include '../includes/footer_simple.php'; ?>
    </div>

    <div id="modal-modifica" class="modal">
        <div class="modal-content">
            <h3>Modifica Stato Attività</h3>
            <form action="dati_admin.php" method="POST">
                <input type="hidden" name="azione" value="modifica_attivita">
                <input type="hidden" name="id_attivita" id="edit-id">
                
                <div class="form-group">
                    <label for="edit-titolo">Attività</label>
                    <input type="text" name="titolo" id="edit-titolo" required>
                </div>
                <div class="form-group">
                    <label for="edit-tipo">Tipo</label>
                    <select name="tipo" id="edit-tipo" required>
                        <option value="" disabled selected>Seleziona un tipo</option>
                        <option value="gioco">Gioco</option>
                        <option value="relax">Relax</option>
                        <option value="fisico">Fisico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-durata">Durata (min)</label>
                    <input type="number" name="durata" id="edit-durata" required>
                </div>
                <div class="form-group">
                    <label for="edit-stato">Stato</label>
                    <select name="stato" id="edit-stato">
                        <option value="attiva">Attiva</option>
                        <option value="disattivata">Disattivata</option>
                    </select>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="save-btn">Salva</button>
                    <button type="button" class="cancel-btn" onclick="chiudiModale('modal-modifica')">Annulla</button>
                </div>
            </form>
        </div>
    </div>

    <div id="playlist-overlay" class="modal">
        <div class="modal-content">
            <h3>Modifica Stato Playlist</h3>
            <form action="dati_admin.php" method="POST">
                <input type="hidden" name="azione" value="modifica_playlist">
                <input type="hidden" name="id_playlist" id="edit-pl-id">

                <div class="form-group">
                    <label for="edit-pl-titolo">Titolo Playlist</label>
                    <input type="text" name="titolo" id="edit-pl-titolo" required>
                </div>
                <div class="form-group">
                    <label for="edit-pl-url">URL Spotify</label>
                    <input type="text" name="url" id="edit-pl-url" required>
                </div>
                <div class="form-group">
                    <label for="edit-pl-attiva">Stato</label>
                    <select name="attiva" id="edit-pl-attiva">
                        <option value="1">Attiva</option>
                        <option value="0">Disattivata</option>
                    </select>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="save-btn">Salva</button>
                    <button type="button" class="cancel-btn" onclick="chiudiModale('playlist-overlay')">Annulla</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/admin.js"></script><?php include '../includes/scripts.php'; ?>
</body>
</html>
