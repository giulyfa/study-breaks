<?php
session_start();

// Controlliamo cosa vuole salvare il JavaScript
if (isset($_GET['azione'])) {
    $azione = $_GET['azione'];

    if ($azione == 'studio') {
        // Aumenta sessioni di oggi e totali
        $_SESSION['sessioni_oggi'] = ($_SESSION['sessioni_oggi'] ?? 0) + 1;
        $_SESSION['sessioni_totali'] = ($_SESSION['sessioni_totali'] ?? 0) + 1;
    } 
    elseif ($azione == 'pausa') {
        // Aumenta il conteggio delle pause
        $_SESSION['pause_totali'] = ($_SESSION['pause_totali'] ?? 0) + 1;
    }
    elseif ($azione == 'attivita') {
        // Aumenta il numero di giochini fatti
        $_SESSION['attivita_totali'] = ($_SESSION['attivita_totali'] ?? 0) + 1;
    }
    elseif ($azione == 'set_timer') {
        if (isset($_GET['minuti'])) {
            $_SESSION['timer_scelto'] = intval($_GET['minuti']);
        }
    }
}
?>