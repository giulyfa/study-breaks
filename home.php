<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nothing+You+Could+Do&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Home - Study Breaks</title>
</head>
<body>
    <div class="home-page">
        <header>
            <button class="menu-btn" id="open-sidebar">&#9776;</button>
            
            <a href="home.php" class="logo-link">
                <img src="img/logo.png" alt="STUDY BREAKS Logo" class="header-logo" />
            </a>
        </header>

        <div id="sidebar-nav" class="sidebar">
            <button class="close-btn">&times;</button>
            <div class="sidebar-links">
                <a href="home.php">Home</a>
                <a href="attivita.php">Attività</a>
                <a href="playlist.php">Playlist</a>
                <a href="profilo.php">Profilo</a>
                <a href="chi-siamo.php">Chi Siamo</a>
            </div>
        </div>

        <main>
            <div class="dashboard-top">
                <div class="dashboard-inner">
                <div class="dashboard-top-left">
                    <section class="hero-section">
                        <h1>PRENDITI UNA PAUSA INTELLIGENTE</h1>
                        <p>Micro-attività da 1 a 5 minuti per rigenerare la mente senza perdere la concentrazione</p>
                    </section>

                    <section class="stats-container">
                        <div class="stat-box">
                            <p>PAUSE</p>
                            <span class="stat-number">24</span>
                        </div>
                        <div class="stat-box">
                            <p>STREAK</p>
                            <span class="stat-number">7</span>
                        </div>
                        <div class="stat-box">
                            <p>ATTIVITÀ</p>
                            <span class="stat-number">42</span>
                        </div>
                    </section>
                </div>

                <section class="timer-card">
                    <div class="timer-background">
                        <h1>Timer Pomodoro</h1>
                        <div class="settings-icon">
                            <button type="button" id="settings-trigger">⚙️</button>
                        </div>

                        <div id="custom-modal" class="modal">
                            <div class="modal-content">
                                <h3>Impostazioni Timer</h3>
                                <p>Inserisci i minuti per la sessione:</p>
                                <input type="number" id="new-minutes" placeholder="Es. 25">
                                <div class="modal-buttons">
                                    <button id="save-modal" class="save-btn">Salva</button>
                                    <button id="close-modal" class="cancel-btn">Annulla</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timer-circle">
                            <div id="timer-time">25:00</div>
                        </div>
                    
                        <div class="timer-status-selector">
                            <button class="mode-btn" id="mode-studio">Studio</button>
                            <button class="mode-btn" id="mode-pausa">Pausa</button>
                        </div>
                        
                        <div class="timer-controls-bottom">
                            <button id="start-btn" class="control-btn">Start</button>
                            <button id="stop-btn" class="control-btn">Stop</button>
                            <button id="restart-btn" class="control-btn">Restart</button>
                        </div>
                    </div>
                    <p class="next-break">Sessioni di studio oggi: <span id="sessions-count">0</span></p>
                </section>
                </div>
            </div>

            <div id="suggestion-message" style="display: none; font-size: 700; text-align: center; margin: 20px; font-weight: bold; color: #4D7D72;"></div>

            <section class="activity-section">
                <h2>Attività consigliate</h2>
                <div class="activity-grid">
                    <a href="activity_detail.html?name=Snake" class="activity-item">
                        <div class="activity-icon"><img src="img/snake.jpg" alt="Snake Icon"></div>
                        <p>Snake - 5 min</p>
                    </a>
                    
                    <a href="activity_detail.html?name=Quiz" class="activity-item">
                        <div class="activity-icon"><img src="img/quiz.jpg" alt="Quiz Icon"></div>
                        <p>Quiz mentale - 3 min</p>
                    </a>
                    
                    <a href="activity_detail.html?name=Yoga" class="activity-item">
                        <div class="activity-icon"><img src="img/yoga.jpg" alt="Yoga Icon"></div>
                        <p>Yoga - 5 min</p>
                    </a>
                    
                    <a href="activity_detail.html?name=Respiro" class="activity-item">
                        <div class="activity-icon"><img src="img/respiro.jpg" alt="Respiro Icon"></div>
                        <p>Respiro - 2 min</p>
                    </a>
                </div>
                <a href="activities.html" class="btn primary-btn">Vai alle attività</a>
            </section>
            
            <section class="playlist-section">
                 <a href="playlist.html" class="btn secondary-btn">Vai alle Playlist</a>
            </section>
        </main>
        
        <footer>
            <nav class="footer-links">
                <a href="home.php" class="footer-link">Home</a>
                <a href="activities.php" class="footer-link">Attività</a>
                <a href="playlist.php" class="footer-link">Playlist</a>
                <a href="profile.php" class="footer-link">Profilo</a><br>
                <a href="about.php" class="footer-link about-link">Chi siamo?</a>
            </nav>
        </footer>
        
    </div> 
    <script src="js/timer.js"></script>
</body>
</html>