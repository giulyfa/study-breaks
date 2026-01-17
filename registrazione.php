<?php
require_once 'config.php';

$errore = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = sanitize($_POST['nome']);
    $cognome = sanitize($_POST['cognome']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $ripeti = $_POST['ripeti_password'];

    // Validazione dati
    if ($password !== $ripeti) {
        $errore = "Le password non coincidono.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errore = "Formato email non valido.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM utenti WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $errore = "Questa email è già registrata.";
        } else {
            // Creazione nuovo utente
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utenti (nome, cognome, email, password, ruolo) VALUES (?, ?, ?, ?, 'studente')");
            
            if ($stmt->execute([$nome, $cognome, $email, $hash])) {
                // Login automatico post-registrazione
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_nome'] = $nome;
                $_SESSION['user_ruolo'] = 'studente';
                
                // Inizializzazione sessione
                $stats = ['sessioni_totali', 'streak', 'sessioni_oggi', 'pause_oggi', 'attivita_oggi'];
                foreach ($stats as $field) { $_SESSION[$field] = 0; }

                header("Location: home.php"); 
                exit;
            }
        }
    }
}
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
    <title>Registrazione - Study Breaks</title>
</head>
<body>
    <header>
        <a href="home.html" title="Torna alla Home Page">
            <img src="img/logo.png" alt="STUDY BREAKS Logo" class="header-logo" />
        </a>
    </header>

    <div class="register-page">  
        <div class="content">
            <?php if($errore): ?>
                <p style="color:red"><?= htmlspecialchars($errore) ?></p>
            <?php endif; ?>

            <h2>Registrazione</h2>
            
            <form method="POST" action="registrazione.php">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="cognome">Cognome</label>
                    <input type="text" id="cognome" name="cognome" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           autocomplete="new-password" minlength="8"> 
                </div>
                
                <div class="form-group">
                    <label for="ripeti_password">Ripeti password</label>
                    <input type="password" id="ripeti_password" name="ripeti_password" required
                           autocomplete="new-password" minlength="8">
                </div>
                
                <button type="submit" class="btn">REGISTRATI</button>
            </form>
            
            <div class="login-link">Hai già un account?<a href="index.php"> Accedi</a></div>
        </div>
    </div>
    
    <?php include 'includes/footer_simple.php'; ?>
</body>
</html>