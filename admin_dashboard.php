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

        <div id="sidebar-nav" class="sidebar">
            <button class="close-btn">&times;</button>
            <div class="sidebar-links">
                <a href="admin_dashboard.php">Home</a>
                <a href="utenti.php">Utenti</a>
                <a href="proposta.php">Proposte</a>
                <a href="statistiche.php">Statistiche</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <br><br>
                    <a href="logout.php">Logout</a>
                <?php endif; ?>
            </div>
        </div>

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
                                    <button class="action-icon" onclick='apriModifica(<?php echo json_encode($row); ?>)'>📝</button> 
                                    
                                    <button class="action-icon" onclick="eliminaElemento(<?php echo $row['id']; ?>, 'attivita')">🗑️</button>
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
                <button class="propose-btn" style="margin-top: 25px;">+ Nuova Playlist</button>
            </section>

            <section class="admin-navigation" style="margin-top: 50px; display: flex; flex-direction: column; gap: 15px;">
                <a href="approvazione_attivita.php" class="btn primary-btn">Vai alle proposte degli utenti</a>
                <a href="gestione_utenti.php" class="btn primary-btn">Vai alla gestione degli utenti</a>
            </section>
        </main>

        <div class="admin-footer-bg">
            <span>2025 - Study Breaks. Tutti i diritti riservati.</span>
        </div>
    </div>

    <script>
        // Funzione per ELIMINARE
        function eliminaElemento(id, tipo) {
            if (confirm("Sei sicuro di voler eliminare definitivamente questo elemento?")) {
                fetch(`dati_admin.php?azione=elimina&id=${id}&tipo=${tipo}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload(); // Ricarica per vedere la riga sparire
                        } else {
                            alert("Errore durante l'eliminazione.");
                        }
                    });
            }
        }

        // Funzione per APRIRE il modale di modifica e popolarlo
        function apriModifica(dati) {
            document.getElementById('edit-id').value = dati.id;
            document.getElementById('edit-titolo').value = dati.titolo;
            document.getElementById('edit-stato').value = dati.stato;
            document.getElementById('modal-modifica').style.display = 'block';
        }

        function apriModificaPlaylist(dati) {
            document.getElementById('edit-pl-id').value = dati.id;
            document.getElementById('edit-pl-titolo').value = dati.titolo;
            document.getElementById('edit-pl-attiva').value = dati.attiva;
            document.getElementById('playlist-overlay').style.display = 'block';
        }

        function chiudiModale(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>

    <div id="modal-modifica" class="modal">
        <div class="modal-content">
            <h3>Modifica Stato Attività</h3>
            <form action="dati_admin.php" method="POST">
                <input type="hidden" name="azione" value="modifica_attivita">
                <input type="hidden" name="id_attivita" id="edit-id">
                
                <div class="form-group">
                    <label>Attività</label>
                    <input type="text" id="edit-titolo" required>
                </div>

                <div class="form-group">
                    <label>Stato</label>
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
            <h3>Modifica Playlist</h3>
            <form action="dati_admin.php" method="POST">
                <input type="hidden" name="azione" value="modifica_playlist">
                <input type="hidden" name="id_playlist" id="edit-pl-id">

                <div class="form-group">
                    <label>Titolo Playlist</label>
                    <input type="text" name="titolo" id="edit-pl-titolo" required>
                </div>

                <div class="form-group">
                    <label>Stato</label>
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

    <?php include 'includes/scripts.php'; ?>
</body>
</html>
