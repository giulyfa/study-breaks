<div id="sidebar-nav" class="sidebar">
    <button class="close-btn">&times;</button>
    <div class="sidebar-links">
        <a href="../php/home.php">Home</a>
        <a href="../php/attivita.php">Attività</a>
        <a href="../php/profilo.php">Profilo</a>
        <a href="../php/proposta.php">Proposta</a>
        <a href="../php/chi-siamo.php">Chi Siamo</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <br><br>
            <a href="../php/logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>