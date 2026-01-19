<div id="sidebar-nav" class="sidebar">
    <button class="close-btn">&times;</button>
    <div class="sidebar-links">
        <a href="admin_dashboard.php">Home</a>
        <a href="utenti.php">Utenti</a>
        <a href="gestione_proposte.php">Proposte</a>
        <a href="statistiche.php">Statistiche</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <br><br>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>