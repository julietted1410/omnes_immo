<?php
session_start();


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Omnes Immobilier</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        function maBoucle(){
            setTimeout(function(){
                maBoucle(); // relance la fonction
            }, 1000);
        }
        maBoucle();

        $(document).ready(function(){
            var $carrousel = $('#carrousel'); // on cible le bloc du carrousel
            $img = $('#carrousel img'); // on cible les images contenues dans le carrousel
            indexImg = $img.length - 1; // on définit l'index du dernier élément
            i = 0; // on initialise un compteur
            $currentImg = $img.eq(i); // enfin, on cible l'image courante, qui possède l'index i (0 pour l'instant)
            $img.css('display', 'none'); // on cache les images
            $currentImg.css('display', 'block'); // on affiche seulement l'image courante

            function updateThumbnails() {
                // Réinitialiser toutes les miniatures
                $('.thumbnail').removeClass('active');
                // Activer la miniature correspondant à l'image actuelle
                $('#thumb' + (i + 1)).addClass('active');
            }

            updateThumbnails();

            $('#next').click(function(){ // image suivante
                $img.css('display', 'none'); // on cache les images
                $currentImg = $img.eq(i); // on définit la nouvelle image
                $currentImg.css('display', 'block'); // puis on l'affiche
                updateThumbnails(); // Mettre à jour les miniatures
            });

            $('#next').click(function(){ // image suivante
                i++; // on incrémente le compteur//
                if (i <= indexImg){
                    $img.css('display', 'none'); // on cache les images
                    $currentImg = $img.eq(i); // on définit la nouvelle image
                    $currentImg.css('display', 'block'); // puis on l'affiche
                    updateThumbnails(); // Mettre à jour les miniatures
                }
                else{
                    i = 0;
                    updateThumbnails();
                }
            });

            $('#prev').click(function(){ // image précédente
                i--; // on décrémente le compteur, puis on réalise la même chose que pour la fonction "suivante"
                $img.css('display', 'none');
                $currentImg = $img.eq(i);
                $currentImg.css('display', 'block');
                updateThumbnails(); // Mettre à jour les miniatures
            });

            $('#prev').click(function(){ // image précédente
                if (i >= 0){
                    $img.css('display', 'none');
                    $currentImg = $img.eq(i);
                    $currentImg.css('display', 'block');
                    updateThumbnails(); // Mettre à jour les miniatures
                }
                else{
                    i = indexImg;
                    updateThumbnails();
                }
            });

            function slideImg(){
                setTimeout(function(){ // on utilise une fonction anonyme
                    if (i < indexImg){ // si le compteur est inférieur au dernier index
                        i++; // on l'incrémente
                    }
                    else { // sinon, on le remet à 0 (première image)
                        i = 0;
                    }
                    $img.css('display', 'none');
                    $currentImg = $img.eq(i);
                    $currentImg.css('display', 'block');
                    slideImg(); // on oublie pas de relancer la fonction à la fin
                    updateThumbnails();
                }, 4000); // on définit l'intervalle à 4000 millisecondes (4s)
            }
            slideImg(); // enfin, on lance la fonction une première fois
            updateThumbnails();
        });
    </script>
</head>

<body>
    <div class="wrapper">
        <header>
            <h1>Accueil - Omnes Immobilier</h1>
        </header>

        <nav>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="toutparcourir.php">Tout Parcourir</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <li><a href="mesrdv.php">Rendez-vous</a></li>
                <?php if(isset($_SESSION['user_nom']) && isset($_SESSION['user_prenom'])): ?>
                    <li><a href="login.php"><?php echo htmlspecialchars($_SESSION['user_nom'] . ' ' . htmlspecialchars($_SESSION['user_prenom'])); ?></a></li>
                <?php else: ?>
                    <li><a href="login.php">Votre Compte</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <section id="welcome-section">
            <h2>Bienvenue chez Omnes Immobilier</h2>
            <p>Votre portail vers l'univers passionnant de l'immobilier ! Que vous soyez à la recherche de votre future maison, d'un appartement en centre-ville, ou même d'un investissement immobilier, vous êtes au bon endroit. Notre équipe dévouée est là pour vous accompagner à chaque étape de votre parcours immobilier.</p>
            <p>Explorez notre vaste sélection de propriétés, des demeures familiales aux villas de luxe, en passant par les appartements modernes. Avec des descriptions détaillées, des photos haute résolution et des visites virtuelles, vous pourrez découvrir chaque bien en détail depuis le confort de votre foyer.</p>
            <p>Mais Omnes Immobilier ne se limite pas à la vente de biens immobiliers. Nous sommes également là pour vous conseiller et vous guider. Vous trouverez sur notre site une mine d'informations utiles sur le marché immobilier, les tendances actuelles, les conseils d'achat et bien plus encore.</p>
            <p>Que vous soyez un acheteur novice ou un investisseur chevronné, notre objectif est de vous offrir une expérience immersive et enrichissante. Naviguez à travers nos différentes rubriques, utilisez nos outils de recherche avancée, et n'hésitez pas à nous contacter pour toute question ou demande d'information. Chez Omnes Immobilier, votre satisfaction est notre priorité.</p>
            <p>Préparez-vous à ouvrir les portes vers votre avenir immobilier. Bienvenue chez Omnes Immobilier, où chaque propriété est une histoire à découvrir, et où chaque rêve immobilier devient réalité.</p>
            <br>

        </section>

        <section id="event-section">
            <h2>Bulletin Immobilier de la semaine</h2>
            <p>
                Ne manquez pas notre visite libre ce week-end ! Venez découvrir une propriété exceptionnelle au cœur de notre ville. Nos agents immobiliers seront là pour vous guider.
            </p>
            <p>
                Participez à notre séminaire sur les différents types d'hypothèques disponibles. Obtenez des informations essentielles sur les taux d'intérêt et les options de financement.
            </p>
            <p>
                Découvrez nos dernières inscriptions ! Des maisons familiales spacieuses aux appartements en centre-ville, nous avons ce que vous cherchez.
            </p>
            <p>
                Profitez de nos offres spéciales sur une sélection de propriétés. Des réductions exclusives et des incitations à l'achat sont disponibles pour une durée limitée.
            </p>
            <p>
                Besoin de conseils d'achat ? Consultez nos articles informatifs sur le processus d'achat immobilier. Des conseils utiles pour les acheteurs débutants et expérimentés.
            </p>
            <p>
                Restez informé sur nos prochains événements immobiliers. Des portes ouvertes aux séminaires, ne manquez aucune occasion de découvrir nos propriétés.
            </p>
            <div id="carrousel">
                <ul>
                    <li><img src="image/immo1.jpg" width="700" height="400.jpg"/></li>
                    <li><img src="image/immo2.jpg" width="700" height="400.jpg"/></li>
                    <li><img src="image/immo3.jpeg" width="700" height="400.jpg"/></li>
                    <li><img src="image/immo4.avif" width="700" height="400.jpg"/></li>
                    <li><img src="image/immo5.jpg" width="700" height="400.jpg"/></li>
                    <li><img src="image/immo6.jpg" width="700" height="400.jpg"/></li>
                    <li><img src="image/immo7.webp" width="700" height="400.jpg"/></li>
                </ul>
            </div>
            <div class="controls">
                <div class="leftbutton"><button id="prev">Précédent</button></div>
                <div class="rightbutton"><button id="next">Suivant</button></div>
            </div>
            <div id="current-slide"></div>
        </section>

        <footer>
            <div class="contact-info">
                <h3>Nous contacter</h3>
                <p>Pour toute question n'hésitez pas à nous contacter :</p>
                <p>Email: mail@omnesimmobilier.com</p>
                <p>Téléphone: +111 111 111</p>
                <p>Adresse: 10 Rue Sextius Michel, 75015 Paris</p><br>
                <p1>&copy; 2025 Omnes Immobilier. </p1>
            </div>
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.1!2d2.2885376!3d48.851108!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1716901768269!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy"></iframe>            </div>
        </footer>
    </div>
</body>

</html>
