<div id="sidebar-nav" class="sidebar">
    <button class="close-btn">&times;</button>
    <div class="sidebar-links">
        <a href="home.php">Home</a>
        <a href="attivita.php">Attività</a>
        <a href="profilo.php">Profilo</a>
        <a href="proposta.php">Proposta</a>
        <a href="chi-siamo.php">Chi Siamo</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <br><br>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>