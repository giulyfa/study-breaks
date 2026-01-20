<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'admin') {
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'Accesso negato']);
    exit;
}

// LOGICA PER ELIMINARE 
if (isset($_GET['azione']) && $_GET['azione'] === 'elimina') {
    $id = intval($_GET['id']);
    $tipo = $_GET['tipo']; 
    $tabella = ($tipo === 'attivita') ? 'attivita' : 'playlist';
    
    $stmt = $pdo->prepare("DELETE FROM $tabella WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    header("Content-Type: application/json");
    echo json_encode(['status' => $result ? 'success' : 'error']);
    exit;
}

// LOGICA PER ATTIVITÀ (Salva o Aggiorna) 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['azione']) && $_POST['azione'] === 'modifica_attivita') {
    $id = !empty($_POST['id_attivita']) ? intval($_POST['id_attivita']) : null;
    $titolo = sanitize($_POST['titolo']);
    $tipo = sanitize($_POST['tipo']);
    $durata = intval($_POST['durata']);
    $stato = sanitize($_POST['stato']);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE attivita SET titolo = ?, tipo = ?, durata = ?, stato = ? WHERE id = ?");
        $stmt->execute([$titolo, $tipo, $durata, $stato, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO attivita (titolo, tipo, durata, stato) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titolo, $tipo, $durata, $stato]);
    }

    header("Location: admin_dashboard.php?msg=ok");
    exit;
}

// LOGICA PER PLAYLIST (Salva o Aggiorna)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['azione']) && $_POST['azione'] === 'modifica_playlist') {
    $id = !empty($_POST['id_playlist']) ? intval($_POST['id_playlist']) : null;
    $titolo = sanitize($_POST['titolo']);
    $url = sanitize($_POST['url']); 
    $attiva = intval($_POST['attiva']);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE playlist SET titolo = ?, url_spotify = ?, attiva = ? WHERE id = ?");
        $stmt->execute([$titolo, $url, $attiva, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO playlist (titolo, url_spotify, attiva) VALUES (?, ?, ?)");
        $stmt->execute([$titolo, $url, $attiva]);
    }

    header("Location: admin_dashboard.php?status=success");
    exit;
}
?>
