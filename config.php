<?php
// config.php - Configurazione del database
session_start();

// Configurazione database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'study_breaks');

// Connessione al database
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}

// Funzione per sanitizzare input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Funzione per validare email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Controllo e azzeramento Streak
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Recuperiamo i dati dell'utente
    $stmt = $pdo->prepare("SELECT ultima_sessione, streak FROM utenti WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if ($user_data && $user_data['ultima_sessione']) {
        $oggi = new DateTime(date('Y-m-d'));
        $ultima = new DateTime($user_data['ultima_sessione']);
        
        // Calcoliamo la differenza in giorni
        $intervallo = $oggi->diff($ultima);
        $giorni_passati = $intervallo->days;

        // Se è passato più di un giorno solare dall'ultima sessione, la streak si rompe
        // Esempio: Ultima sessione Lunedì, oggi è Mercoledì -> giorni_passati = 2 -> Reset!
        if ($giorni_passati > 1) {
            $stmtReset = $pdo->prepare("UPDATE utenti SET streak = 0 WHERE id = ?");
            $stmtReset->execute([$user_id]);
            
            // Opzionale: aggiorna anche la variabile in sessione se la usi per il display veloce
            if(isset($_SESSION['streak'])) {
                $_SESSION['streak'] = 0;
            }
        }
    }
}
?>