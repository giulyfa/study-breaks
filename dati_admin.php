<?php
require_once 'config.php';

// Protezione: Solo l'admin può eseguire queste azioni
if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'admin') {
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'Accesso negato']);
    exit;
}

// --- LOGICA PER ELIMINARE (Richiesta via GET/Fetch) ---
if (isset($_GET['azione']) && $_GET['azione'] === 'elimina') {
    $id = intval($_GET['id']);
    $tipo = $_GET['tipo']; // 'attivita' o 'playlist'
    
    // Decidiamo su quale tabella lavorare
    $tabella = ($tipo === 'attivita') ? 'attivita' : 'playlist';
    
    $stmt = $pdo->prepare("DELETE FROM $tabella WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    header("Content-Type: application/json");
    echo json_encode(['status' => $result ? 'success' : 'error']);
    exit;
}

// --- LOGICA PER MODIFICARE ATTIVITÀ (Richiesta via POST dal Modale) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['azione']) && $_POST['azione'] === 'modifica_attivita') {
    $id = intval($_POST['id_attivita']);
    $titolo = sanitize($_POST['titolo']);
    $tipo = sanitize($_POST['tipo']);
    $durata = intval($_POST['durata']);
    $stato = sanitize($_POST['stato']);

    $stmt = $pdo->prepare("UPDATE attivita SET titolo = ?, tipo = ?, durata = ?, stato = ? WHERE id = ?");
    $stmt->execute([$titolo, $tipo, $durata, $stato, $id]);

    header("Location: admin_dashboard.php?msg=updated");
    exit;
}
?>