<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'client' && $_SESSION['role'] != 'agent')) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

if ($role === 'client' && isset($_GET['agent_id'])) {
    $agent_id = $_GET['agent_id'];
    $client_id = $_SESSION['client_id'];
    $user_name = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
} elseif ($role === 'agent' && isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];
    $agent_id = $_SESSION['agent_id'];
    $user_name = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
} else {
    die("Erreur paramètres chat.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chat agent/client</title>
    <link rel="stylesheet" href="stylechat.css">
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Omnes Immobilier</h1>
        </header>

        <nav>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="toutparcourir.php">Parcourir</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <li><a href="mesrdv.php">Rendez-vous</a></li>
                <li><a href="login.php">Compte</a></li>
            </ul>
        </nav>

        <div id="wrapper">
            <div id="menu">
                <p>Bienvenue <b><?= htmlspecialchars($user_name) ?></b></p>
                <p><a id="exit" href="home.php">Quitter</a></p>
            </div>

            <div id="chatbox"></div>

            <form id="messageForm" method="post">
                <input name="message" type="text" id="message" placeholder="Votre message">
                <input type="hidden" name="agent_id" value="<?= $agent_id ?>">
                <input type="hidden" name="client_id" value="<?= $client_id ?>">
                <input type="hidden" name="sender" value="<?= $role ?>">
                <input type="submit" id="submit" value="Envoyer">
            </form>
        </div>

                <footer>
            <div class="contact-info">
                <h3>Nous contacter</h3>
                <p>Pour toute question n'hésitez pas à nous contacter :</p>
                <p>Email: mail@omnesimmobilier.com</p>
                <p>Téléphone: +111 111 111</p>
                <p>Adresse: 10 Rue Sextius Michel, 75015 Paris</p>                <br>
                <p1>&copy; 2025 Omnes Immobilier. </p1>
            </div>
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.1!2d2.2885376!3d48.851108!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1716901768269!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy"></iframe>            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(function() {
        function loadMessages() {
            $.get("get_messages.php", {
                agent_id: "<?= $agent_id ?>",
                client_id: "<?= $client_id ?>"
            }, function(data) {
                $("#chatbox").html(data);
                $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
            });
        }

        $("#messageForm").submit(function(e) {
            e.preventDefault();
            $.post("postmessage.php", {
                message: $("#message").val(),
                agent_id: "<?= $agent_id ?>",
                client_id: "<?= $client_id ?>",
                sender: "<?= $role ?>"
            }, function() {
                $("#message").val("");
                loadMessages();
            });
        });

        setInterval(loadMessages, 2000);
    });
    </script>
</body>
</html>
