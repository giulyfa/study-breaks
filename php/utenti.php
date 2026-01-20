<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_ruolo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// GESTIONE AZIONI (Blocca/Sblocca)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_user_id'])) {
    $id_utente = intval($_POST['toggle_user_id']);
    $stato_attuale = $_POST['current_status'];
    
    $nuovo_stato = ($stato_attuale === 'attivo') ? 'bloccato' : 'attivo';
    
    $stmt = $pdo->prepare("UPDATE utenti SET stato = ? WHERE id = ?");
    $stmt->execute([$nuovo_stato, $id_utente]);
    
    header("Location: utenti.php");
    exit;
}

// GESTIONE RICERCA E LETTURA DATI
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM utenti WHERE ruolo != 'admin'";
$params = [];

if ($search) {
    $sql .= " AND (nome LIKE ? OR cognome LIKE ? OR email LIKE ?)";
    $term = "%$search%";
    $params = [$term, $term, $term];
}

$sql .= " ORDER BY nome ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$utenti = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <title>Gestione Utenti - Study Breaks</title>
</head>
<body>
    <div class="admin-page">
        <?php include '../includes/header.php'; ?>
        <?php include '../includes/sidebar_admin.php'; ?>

        <main class="admin-container">
            <section class="admin-intro">
                <h1>Gestisci utenti</h1>
            </section>

            <form action="utenti.php" method="GET" class="search-bar">
                <input type="text" name="search" aria-label="Cerca utenti per nome o email" placeholder="Cerca per nome o email..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if($search): ?>
                    <a href="utenti.php" aria-label="Cancella ricerca" style="margin-left: 10px; text-decoration: none; color: #333; display: flex; align-items: center;">✕</a>
                <?php endif; ?>
            </form>

            <div class="table-container">
                <table class="user-table user-management-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Stato</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($utenti) > 0): ?>
                            <?php foreach ($utenti as $user): ?>
                                <?php 
                                    $stato = $user['stato'] ?: 'attivo';
                                    $badgeClass = ($stato === 'attivo') ? 'attivo' : 'bloccato';
                                    $testoBadge = ucfirst($stato);
                                    $icon = ($stato === 'attivo') ? '🔒' : '🔓';
                                    $title = ($stato === 'attivo') ? 'Blocca utente' : 'Sblocca utente';
                                ?>
                                <tr>
                                    <td data-label="Nome">
                                        <strong><?php echo htmlspecialchars($user['nome'] . ' ' . $user['cognome']); ?></strong>
                                    </td>
                                    <td data-label="E-mail">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </td>
                                    <td data-label="Stato">
                                        <span class="status-badge <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($stato); ?>
                                        </span>
                                    </td>
                                    <td data-label="Azioni">
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="toggle_user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $stato; ?>">
                                            <button type="submit" class="action-icon" title="<?php echo $title; ?>">
                                                <?php echo $icon; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 20px;">Nessun utente trovato.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-navigation">
                <a href="admin_dashboard.php" class="btn primary-btn">Torna alla Dashboard</a>
            </div>
        </main>

        <?php include '../includes/footer_simple.php'; ?>
    </div>
    <?php include '../includes/scripts.php'; ?>
</body>
</html>
