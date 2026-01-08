<?php
require_once 'config.php'; // Connessione al database

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$azione = $_GET['azione'] ?? '';

if ($azione == 'studio') {
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
        $nuova_streak = $streak_attuale;
    } else {
        $nuova_streak = 1;
    }

    $stmt = $pdo->prepare("UPDATE utenti SET 
        sessioni_totali = sessioni_totali + 1, 
        streak = ?, 
        ultima_sessione = ? 
        WHERE id = ?");
    $stmt->execute([$nuova_streak, $oggi, $user_id]);
    
    $_SESSION['sessioni_totali'] = ($_SESSION['sessioni_totali'] ?? 0) + 1;
    $_SESSION['sessioni_oggi'] = ($_SESSION['sessioni_oggi'] ?? 0) + 1;
    $_SESSION['streak'] = $nuova_streak;
} 

elseif ($azione == 'pausa') {
    // AGGIUNTA: Incrementa il contatore delle pause di oggi nella sessione
    $_SESSION['pause_oggi'] = ($_SESSION['pause_oggi'] ?? 0) + 1;
}

elseif ($azione == 'attivita') {
    $id_att = intval($_GET['id_att'] ?? 0); // Recupera l'ID dell'attività
    $nome_att = $_GET['nome'] ?? 'Attività';
    $cat_att = $_GET['categoria'] ?? 'Generale';
    $durata_att = intval($_GET['durata'] ?? 0);

    // 1. Aggiorna il totale generale nel database
    $stmtUser = $pdo->prepare("UPDATE utenti SET attivita_totali = attivita_totali + 1 WHERE id = ?");
    $stmtUser->execute([$user_id]);

    // 2. Inserisce il record nel log dettagliato
    $stmtLog = $pdo->prepare("INSERT INTO attivita_svolte (id_utente, id_attivita, categoria, nome_attivita, durata_minuti, data_ora) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmtLog->execute([$user_id, $id_att, $cat_att, $nome_att, $durata_att]);

    // 3. AGGIORNAMENTO SESSIONE: Aggiorna sia il totale che il contatore di oggi
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

echo json_encode(['status' => 'success']);
?>