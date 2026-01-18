<?php
require_once 'config.php';

// Verifica se l'utente è loggato e se è admin
if (!isset($_SESSION['user_ruolo']) || $_SESSION['user_ruolo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

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

        <?php include 'includes/sidebar_admin.php'; ?>

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

            <section class="admin-navigation" style="margin-top: 50px; display: flex; flex-direction: column; gap: 15px;">
                <a href="approvazione_attivita.php" class="btn primary-btn">Vai alle proposte degli utenti</a>
                <a href="utenti.php" class="btn primary-btn">Vai alla gestione degli utenti</a>
            </section>
        </main>

        <?php include 'includes/footer_simple.php'; ?>
    </div>

    <script>
        function eliminaElemento(id, tipo) {
            if (confirm("Sei sicuro di voler eliminare definitivamente questo elemento?")) {
                fetch(`dati_admin.php?azione=elimina&id=${id}&tipo=${tipo}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload(); 
                        } else {
                            alert("Errore durante l'eliminazione.");
                        }
                    });
            }
        }

        function apriModifica(dati) {
            document.getElementById('edit-id').value = dati.id;
            document.getElementById('edit-titolo').value = dati.titolo;
            document.getElementById('edit-tipo').value = dati.tipo;
            document.getElementById('edit-durata').value = dati.durata;
            document.getElementById('edit-stato').value = dati.stato;

            // 2. BLOCCHIAMO i campi che non vuoi modificare
            const campiDaBloccare = ['edit-titolo', 'edit-tipo', 'edit-durata'];
            campiDaBloccare.forEach(id => {
                const el = document.getElementById(id);
                el.readOnly = true;
                if(el.tagName === 'SELECT') el.style.pointerEvents = 'none'; // Blocca i select
                el.style.background = "#f0f0f0";
            });
            document.getElementById('modal-modifica').style.display = 'block';
        }

        function apriNuovaAttivita() {
            document.getElementById('edit-id').value = '';
            document.getElementById('edit-titolo').value = '';
            document.getElementById('edit-tipo').value = 'gioco';
            document.getElementById('edit-durata').value = '';
            document.getElementById('edit-stato').value = 'disattivata';

            // 2. SBLOCCHIAMO i campi per il nuovo inserimento
            const campiDaSbloccare = ['edit-titolo', 'edit-tipo', 'edit-durata'];
            campiDaSbloccare.forEach(id => {
                const el = document.getElementById(id);
                el.readOnly = false;
                if(el.tagName === 'SELECT') el.style.pointerEvents = 'auto';
                el.style.background = "#fff";
            });

            document.querySelector('#modal-modifica h3').innerText = "Aggiungi Nuova Attività";
            document.getElementById('modal-modifica').style.display = 'block';
        }

        function apriModificaPlaylist(dati) {
            document.getElementById('edit-pl-id').value = dati.id;
            const titoloInput = document.getElementById('edit-pl-titolo');
            titoloInput.value = dati.titolo;
            const urlInput = document.getElementById('edit-pl-url');
            urlInput.value = dati.url_spotify || '';
            
            // 3. Blocchiamo il campo URL (sola lettura)
            urlInput.readOnly = true; 
            titoloInput.readOnly = true;
            titoloInput.style.background = "#f0f0f0";
            urlInput.style.background = "#f0f0f0";
        
            document.getElementById('edit-pl-attiva').value = dati.attiva;
            document.getElementById('playlist-overlay').style.display = 'block';
        }

        function apriNuovaPlaylist() {
            document.getElementById('edit-pl-id').value = ''; // ID vuoto = Nuovo inserimento
            const titoloInput = document.getElementById('edit-pl-titolo');
            titoloInput.value = '';
            titoloInput.readOnly = false;
            document.getElementById('edit-pl-attiva').value = '1'; 

            const urlInput = document.getElementById('edit-pl-url');
            urlInput.value = '';
            urlInput.readOnly = false;
            urlInput.style.background = "#fff";
            
            document.querySelector('#playlist-overlay h3').innerText = "Aggiungi Nuova Playlist";
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
                    <input type="text" name="titolo" id="edit-titolo" required>
                </div>

                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo" id="edit-tipo" required>
                        <option value="gioco">Gioco</option>
                        <option value="relax">Relax</option>
                        <option value="fisico">Fisico</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Durata (min)</label>
                    <input type="number" name="durata" id="edit-durata" required>
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
            <h3>Modifica Stato Playlist</h3>
            <form action="dati_admin.php" method="POST">
                <input type="hidden" name="azione" value="modifica_playlist">
                <input type="hidden" name="id_playlist" id="edit-pl-id">

                <div class="form-group">
                    <label>Titolo Playlist</label>
                    <input type="text" name="titolo" id="edit-pl-titolo" required>
                </div>

                <div class="form-group">
                    <label>URL Spotify</label>
                    <input type="text" name="url" id="edit-pl-url" required>
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
