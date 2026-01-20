<div id="sidebar-nav" class="sidebar">
    <button class="close-btn">&times;</button>
    <div class="sidebar-links">
        <a href="../php/admin_dashboard.php">Home</a>
        <a href="../php/utenti.php">Utenti</a>
        <a href="../php/gestione_proposte.php">Proposte</a>
        <a href="../php/statistiche.php">Statistiche</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <br><br>
            <a href="../php/logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>