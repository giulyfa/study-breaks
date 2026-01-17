<?php
require_once 'config.php';
// Non è obbligatorio essere loggati per vedere chi siamo, ma carichiamo la sessione se c'è
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
    <title>Chi Siamo - Study Breaks</title>
</head>
<body>
    <div class="about-page">
        <?php include 'includes/header.php'; ?>

        <?php include 'includes/sidebar.php'; ?>

        <main class="about-container">
            <section class="hero-about">
                <h1>La nostra missione</h1>
                <p class="subtitle">Trasformare le pause studio da momenti di distrazione a momenti di ricarica reale.</p>
            </section>

            <div class="about-grid">
                <div class="about-card">
                    <div class="icon"><img src="img/cervello.jpg" alt="Cervello"></div>
                    <h3>Perché Study Breaks?</h3>
                    <p>Sappiamo quanto sia difficile mantenere la concentrazione. Study Breaks nasce dall'idea di fornire strumenti rapidi (1-5 min) per staccare il cervello senza perdere il ritmo.</p>
                </div>

                <div class="about-card">
                    <div class="icon"><img src="img/bilancia.jpg" alt="Bilancia"></div>
                    <h3>Equilibrio</h3>
                    <p>Integriamo giochi di logica, esercizi fisici e playlist rilassanti per offrirti un supporto a 360 gradi durante le tue giornate di studio.</p>
                </div>

                <div class="about-card">
                    <div class="icon"><img src="img/utenti.jpg" alt="Utenti"></div>
                    <h3>Community</h3>
                    <p>Crediamo nella condivisione. Grazie alla sezione "Proposta", ogni utente può contribuire a migliorare l'esperienza degli altri utenti.</p>
                </div>
            </div>

            <section class="team-section">
                <h2>Il Progetto</h2>
                <div class="team-content">
                    <p>Study Breaks è un progetto nato per supportare gli studenti universitari nella gestione del tempo. È stato sviluppato pensando alla semplicità d'uso e all'efficacia psicologica delle micro-pause.</p>
                </div>
            </section>

            <section class="contact-section">
                <h2>Contattaci</h2>
                <p>Hai domande, suggerimenti o vuoi semplicemente salutarci? Scrivici una mail!</p>
                <strong>Creatrici:</strong>
                <a href="mailto:eleonora.bianco3@studio.unibo.it">eleonora.bianco3@studio.unibo.it</a><br>
                <a href="mailto:giulia.fares@studio.unibo.it">giulia.fares@studio.unibo.it</a>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>