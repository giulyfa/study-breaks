<?php
require_once 'config.php';

// Funzione per il reindirizzamento 
function redirectUser($role) {
    $target = ($role === 'admin') ? "admin_dashboard.php" : "home.php";
    header("Location: $target");
    exit;
}

// Controllo accesso già effettuato
if (isset($_SESSION['user_id'])) {
    redirectUser($_SESSION['user_ruolo']);
}

$errore = "";

// Logica Login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utenti WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['stato'] === 'bloccato') {
            $errore = "Il tuo account è stato bloccato. Contatta l'amministratore.";
        } else {
            // Popolamento Sessione 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_ruolo'] = $user['ruolo'];
            
            // Dati statistici
            $stats = ['sessioni_totali', 'streak', 'sessioni_oggi', 'pause_oggi', 'attivita_oggi'];
            foreach ($stats as $field) {
                $_SESSION[$field] = $user[$field] ?? 0;
            }
            $_SESSION['data_ultimo_accesso'] = $user['ultima_sessione'] ?? '';

            redirectUser($user['ruolo']);
        }
    } else {
        $errore = "Email o password errati.";
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
    <link href="https://fonts.googleapis.com/css2?family=Nothing+You+Could+Do&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> <title>Login - Study Breaks</title>
</head>
<body>
    <header>
        <a href="home.html" title="Torna alla Home Page">
            <img src="img/logo.png" alt="STUDY BREAKS Logo" class="header-logo"/>
        </a>
    </header>

    <div class="login-page">
        <main> 
            <div class="content">
                <div class="user-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.6-6 8-6s8 2 8 6"/>
                    </svg>
                </div>   

                <h2>Login</h2>

                <?php if($errore): ?>
                    <p class="error-message" style="color:red; margin-bottom: 15px;"><?= htmlspecialchars($errore) ?></p>
                <?php endif; ?>

                <form method="POST" action="index.php">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="email">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                    </div>
                    
                    <button type="submit" class="btn">LOGIN</button>
                </form>
            
                
                <div class="register-link">Non hai un account? <a href="registrazione.php">Registrati</a></div>
            </div> 
        
            <div class="tagline">non mollare, un passo alla volta!</div>
        </main> 
    </div>
    
    <?php include 'includes/footer_simple.php'; ?>
</body>
</html>
