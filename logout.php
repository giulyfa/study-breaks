<?php
session_start(); // Accede alla sessione attuale

// Rimuove tutte le variabili di sessione
$_SESSION = array();

// Distrugge la sessione sul server
session_destroy();

// Reindirizza l'utente alla pagina iniziale
header("Location: index.php");
exit;
?>