<?php
require_once 'config.php'; // Connessione al database

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$azione = $_GET['azione'] ?? '';

if ($azione == 'studio') {
    // 1. Recupero dati utente per la streak
    $stmt = $pdo->prepare("SELECT streak, ultima_sessione FROM utenti WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    $streak_attuale = $user['streak'] ?? 0;
    $ultima_data = $user['ultima_sessione'];
    
    $oggi = date('Y-m-d');
    $ieri = date('Y-m-d', strtotime('-1 day'));

    if ($ultima_data == $ieri) {
        $nuova_streak = $streak_attuale + 1;
    } elseif ($ultima_data == $oggi) {
        $nuova_streak = ($streak_attuale == 0) ? 1 : $streak_attuale;
    } else {
        $nuova_streak = 1;
    }

    // 2. Aggiornamento tabella UTENTI (Totali e Streak)
    $stmt = $pdo->prepare("UPDATE utenti SET 
        sessioni_totali = sessioni_totali + 1, 
        sessioni_oggi = sessioni_oggi + 1, 
        streak = ?, 
        ultima_sessione = ? 
        WHERE id = ?");
    $stmt->execute([$nuova_streak, $oggi, $user_id]);

    // --- AGGIUNTA PER IL GRAFICO: Inserimento in attivita_svolte ---
    // Recuperiamo la durata passata dal JS, se manca mettiamo 25 di default
    $durata_studio = intval($_GET['durata'] ?? 25); 

    $stmtLogStudio = $pdo->prepare("INSERT INTO attivita_svolte 
        (id_utente, id_attivita, categoria, nome_attivita, durata_minuti, data_ora) 
        VALUES (?, 0, 'Studio', 'Sessione Studio', ?, NOW())");
    $stmtLogStudio->execute([$user_id, $durata_studio]);
    // --------------------------------------------------------------
    
    $_SESSION['sessioni_totali'] = ($_SESSION['sessioni_totali'] ?? 0) + 1;
    $_SESSION['sessioni_oggi'] = ($_SESSION['sessioni_oggi'] ?? 0) + 1;
    $_SESSION['streak'] = $nuova_streak;
} 
elseif ($azione == 'pausa') {
    $stmt = $pdo->prepare("UPDATE utenti SET pause_oggi = pause_oggi + 1 WHERE id = ?");
    $stmt->execute([$user_id]);
    $_SESSION['pause_oggi'] = ($_SESSION['pause_oggi'] ?? 0) + 1;
}
elseif ($azione == 'attivita') {
    $id_att = intval($_GET['id_att'] ?? 0); 
    $nome_att = $_GET['nome'] ?? 'Attività';
    $cat_att = $_GET['categoria'] ?? 'Generale';
    $durata_att = intval($_GET['durata'] ?? 0);

    $stmtUser = $pdo->prepare("UPDATE utenti SET 
        attivita_totali = attivita_totali + 1,
        attivita_oggi = attivita_oggi + 1 
        WHERE id = ?");
    $stmtUser->execute([$user_id]);

    $stmtLog = $pdo->prepare("INSERT INTO attivita_svolte (id_utente, id_attivita, categoria, nome_attivita, durata_minuti, data_ora) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmtLog->execute([$user_id, $id_att, $cat_att, $nome_att, $durata_att]);

    $_SESSION['attivita_totali'] = ($_SESSION['attivita_totali'] ?? 0) + 1;
    $_SESSION['attivita_oggi'] = ($_SESSION['attivita_oggi'] ?? 0) + 1; 
}
elseif ($azione == 'set_timer') {
    if (isset($_GET['minuti'])) {
        $minuti = intval($_GET['minuti']);
        $tipo = $_GET['tipo'] ?? 'studio'; 

        if ($tipo == 'pausa') {
            $_SESSION['pausa_scelta'] = $minuti;
        } else {
            $_SESSION['timer_scelto'] = $minuti;
        }
    }
}
elseif ($azione === 'log_playlist') {
    $idP = $_GET['id_p'];
    $userId = $_SESSION['user_id'];

    $stmtLog = $pdo->prepare("INSERT INTO log_ascolti (id_utente, id_playlist) VALUES (?, ?)");
    
    if ($stmtLog->execute([$userId, $idP])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Errore query']);
    }
    exit;
}

echo json_encode(['status' => 'success']);
?>