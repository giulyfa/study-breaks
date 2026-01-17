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
// --- MODIFICA SOLO STATO ATTIVITÀ ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['azione']) && $_POST['azione'] === 'modifica_attivita') {
    $id = intval($_POST['id_attivita']);
    $stato = sanitize($_POST['stato']);

    $stmt = $pdo->prepare("UPDATE attivita SET stato = ? WHERE id = ?");
    $stmt->execute([$stato, $id]);

    header("Location: admin_dashboard.php?msg=ok");
    exit;
}

// --- SALVATAGGIO MODIFICA PLAYLIST ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['azione']) && $_POST['azione'] === 'modifica_playlist') {
    
    // Recuperiamo i dati inviati dal form
    $id = intval($_POST['id_playlist']);
    $titolo = sanitize($_POST['titolo']);
    $attiva = intval($_POST['attiva']); // Riceve 1 o 0 dal select

    // Prepariamo la query (Assicurati che la colonna si chiami 'attiva' nel tuo DB)
    $stmt = $pdo->prepare("UPDATE playlist SET titolo = ?, attiva = ? WHERE id = ?");
    
    if ($stmt->execute([$titolo, $attiva, $id])) {
        // Se va a buon fine, torna alla dashboard con un messaggio di successo
        header("Location: admin_dashboard.php?status=success");
    } else {
        // Se c'è un errore, mostralo (utile per il debug)
        echo "Errore durante l'aggiornamento: " . print_r($stmt->errorInfo(), true);
    }
    exit;
}
?>