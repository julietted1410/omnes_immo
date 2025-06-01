<?php


session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agent"; // Assurez-vous que le nom de la base de données est correct

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['register'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    if ($role === 'client') {
        $sql = "SELECT * FROM clients WHERE email = ? AND mot_de_passe = ?";
    } elseif ($role === 'agent') {
        $sql = "SELECT * FROM agents_immobiliers WHERE email = ? AND mot_de_passe = ?";
    } else {
        $sql = "SELECT * FROM administrateurs WHERE email = ? AND mot_de_passe = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $mot_de_passe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['role'] = $role;
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];

        // Stocker l'ID dans la session selon le rôle
        if ($role === 'client') {
            $_SESSION['client_id'] = $user['client_id'];
        } elseif ($role === 'agent') {
            $_SESSION['agent_id'] = $user['agent_id'];
        } else {
            $_SESSION['admin_id'] = $user['admin_id'];
        }

        header("Location: login.php");
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

function getUserDetails($conn, $role, $user_id) {
    if ($role === 'client') {
        $sql = "SELECT * FROM clients WHERE client_id = ?";
    } elseif ($role === 'agent') {
        $sql = "SELECT * FROM agents_immobiliers WHERE agent_id = ?";
    } else {
        $sql = "SELECT * FROM administrateurs WHERE admin_id = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Omnes Immobilier</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .block {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            margin: 20px;
        }
        .container {
            display: flex;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .login-form, .register-form, .user-info {
            padding: 20px;
            width: 300px;
        }
        .login-form {
            border-right: 1px solid #eeeeee;
        }
        .user-info p {
            font-size: 18px;
            margin: 10px 0;
        }
        .logout-button, .create-agent-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555555;
        }
        input[type="email"],
        input[type="text"],
        input[type="password"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c0392b;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
        }
        .success {
            color: #27ae60;
            margin-bottom: 15px;
        }
        .create-agent-form {
            display: none;
            flex-direction: column;
            align-items: center;
        }
        .create-agent-form.active {
            display: flex;
        }
    </style>
    <script>
        function checkRole() {
            var role = document.getElementById('role').value;
            var adminCodeField = document.getElementById('admin-code-field');
            if (role === 'agent') {
                var code = prompt("Code de création Agent");
                if (code === 'baule') {
                    adminCodeField.value = code;
                } else {
                    alert("Code incorrect. Vous ne pouvez pas créer un compte agent.");
                    window.location.href = 'login.php';
                }
            }
        }

        function toggleCreateAgentForm() {
            var form = document.querySelector('.create-agent-form');
            form.classList.toggle('active');
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Connexion - Omnes Immobilier</h1>
        </header>

        <nav>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="toutparcourir.php">Tout Parcourir</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <li><a href="mesrdv.php">Rendez-vous</a></li>
                <li><a href="login.php">Votre Compte</a></li>
            </ul>
        </nav>

        <div class="block">
            <div class="container">
                <?php if (isset($_SESSION['role'])): ?>
                    <?php
                        $user_id = $_SESSION['role'] === 'client' ? $_SESSION['client_id'] : ($_SESSION['role'] === 'agent' ? $_SESSION['agent_id'] : $_SESSION['admin_id']);
                        $user_details = getUserDetails($conn, $_SESSION['role'], $user_id);
                    ?>
                    <div class="user-info">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <button class="create-agent-button" onclick="toggleCreateAgentForm()">Créer un compte agent</button>
                        <?php elseif ($_SESSION['role'] === 'agent'): ?>
                            <img src="agents/<?php echo htmlspecialchars($user_details['photo_id']); ?>.jpg" alt="<?php echo htmlspecialchars($user_details['nom']) . ' ' . htmlspecialchars($user_details['prenom']); ?>" style="width: 100px; height: auto;">
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user_details['telephone']); ?></p>
                            <p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($user_details['mot_de_passe']); ?></p>
                        <?php elseif ($_SESSION['role'] === 'client'): ?>
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($user_details['adresse']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user_details['telephone']); ?></p>
                            <p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($user_details['mot_de_passe']); ?></p>
                            <p><strong>Informations financières :</strong></p>
                            <p style="font-size: 14px; color: grey;">
                                <strong>Type de carte :</strong> <?php echo htmlspecialchars($user_details['carte_type']); ?><br>
                                <strong>Numéro de carte :</strong> <?php echo htmlspecialchars(substr($user_details['carte_numero'], 0, 4) . ' **** **** ' . substr($user_details['carte_numero'], -4)); ?><br>
                                <strong>Nom sur la carte :</strong> <?php echo htmlspecialchars($user_details['carte_nom']); ?><br>
                                <strong>Date d'expiration :</strong> <?php echo htmlspecialchars($user_details['carte_expiration']); ?><br>
                                <strong>Code de sécurité :</strong> <?php echo htmlspecialchars('***'); ?>
                            </p>
                        <?php endif; ?>
                        <form method="GET" action="login.php">
                            <button type="submit" name="logout" class="logout-button">Déconnexion</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="login-form">
                        <h2>Se connecter</h2>
                        <?php if (isset($error) && !isset($_POST['register'])): ?>
                            <div class="error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="login.php">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                            <label for="role">Je suis</label>
                            <select id="role" name="role" required>
                                <option value="client">Client</option>
                                <option value="agent">Agent</option>
                                <option value="admin">Administrateur</option>
                            </select>
                            <button type="submit">Se connecter</button>
                        </form>
                    </div>

                    <div class="register-form">
                        <h2>Créer un compte</h2>
                        <?php if (isset($success)): ?>
                            <div class="success"><?php echo $success; ?></div>
                            <script>
                                setTimeout(function() {
                                    document.querySelector('.success').style.display = 'none';
                                }, 2000);
                            </script>
                        <?php endif; ?>
                        <?php if (isset($error) && isset($_POST['register'])): ?>
                            <div class="error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="register.php" onsubmit="checkRole()">
                            <input type="hidden" name="register" value="1">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" required>
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" required>
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" required>
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <label for="telephone">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" required>
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                            <label for="carte_type">Type de carte</label>
                            <input type="text" id="carte_type" name="carte_type" required>
                            <label for="carte_numero">Numéro de carte</label>
                            <input type="text" id="carte_numero" name="carte_numero" required>
                            <label for="carte_nom">Nom sur la carte</label>
                            <input type="text" id="carte_nom" name="carte_nom" required>
                            <label for="carte_expiration">Date d'expiration</label>
                            <input type="text" id="carte_expiration" name="carte_expiration" required>
                            <label for="carte_code">Code de sécurité</label>
                            <input type="text" id="carte_code" name="carte_code" required>
                            <input type="hidden" id="admin-code-field" name="admin_code" value="">
                            <button type="submit">Créer un compte</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulaire de création d'agent -->
        <div class="create-agent-form">
            <h2>Créer un agent immobilier</h2>
            <form method="POST" action="create_agent.php" enctype="multipart/form-data">
                <label for="agent_nom">Nom</label>
                <input type="text" id="agent_nom" name="agent_nom" required>
                <label for="agent_prenom">Prénom</label>
                <input type="text" id="agent_prenom" name="agent_prenom" required>
                <label for="agent_email">Email</label>
                <input type="email" id="agent_email" name="agent_email" required>
                <label for="agent_telephone">Téléphone</label>
                <input type="text" id="agent_telephone" name="agent_telephone" required>
                <label for="agent_specialite">Spécialité</label>
                <input type="text" id="agent_specialite" name="agent_specialite" required>
                <label for="agent_jours_travail">Jours de travail</label>
                <input type="text" id="agent_jours_travail" name="agent_jours_travail" required>
                <label for="agent_mot_de_passe">Mot de passe</label>
                <input type="password" id="agent_mot_de_passe" name="agent_mot_de_passe" required>
                <label for="agent_photo">Photo (JPG)</label>
                <input type="file" id="agent_photo" name="agent_photo" accept=".jpg" required>
                <label for="agent_cv">CV (PDF)</label>
                <input type="file" id="agent_cv" name="agent_cv" accept=".pdf" required>
                <button type="submit">Créer un agent</button>
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
</body>
</html>
<?php


session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agent"; // Assurez-vous que le nom de la base de données est correct

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['register'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    if ($role === 'client') {
        $sql = "SELECT * FROM clients WHERE email = ? AND mot_de_passe = ?";
    } elseif ($role === 'agent') {
        $sql = "SELECT * FROM agents_immobiliers WHERE email = ? AND mot_de_passe = ?";
    } else {
        $sql = "SELECT * FROM administrateurs WHERE email = ? AND mot_de_passe = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $mot_de_passe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['role'] = $role;
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];

        // Stocker l'ID dans la session selon le rôle
        if ($role === 'client') {
            $_SESSION['client_id'] = $user['client_id'];
        } elseif ($role === 'agent') {
            $_SESSION['agent_id'] = $user['agent_id'];
        } else {
            $_SESSION['admin_id'] = $user['admin_id'];
        }

        header("Location: login.php");
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

function getUserDetails($conn, $role, $user_id) {
    if ($role === 'client') {
        $sql = "SELECT * FROM clients WHERE client_id = ?";
    } elseif ($role === 'agent') {
        $sql = "SELECT * FROM agents_immobiliers WHERE agent_id = ?";
    } else {
        $sql = "SELECT * FROM administrateurs WHERE admin_id = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Omnes Immobilier</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .block {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            margin: 20px;
        }
        .container {
            display: flex;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .login-form, .register-form, .user-info {
            padding: 20px;
            width: 300px;
        }
        .login-form {
            border-right: 1px solid #eeeeee;
        }
        .user-info p {
            font-size: 18px;
            margin: 10px 0;
        }
        .logout-button, .create-agent-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555555;
        }
        input[type="email"],
        input[type="text"],
        input[type="password"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c0392b;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
        }
        .success {
            color: #27ae60;
            margin-bottom: 15px;
        }
        .create-agent-form {
            display: none;
            flex-direction: column;
            align-items: center;
        }
        .create-agent-form.active {
            display: flex;
        }
    </style>
    <script>
        function checkRole() {
            var role = document.getElementById('role').value;
            var adminCodeField = document.getElementById('admin-code-field');
            if (role === 'agent') {
                var code = prompt("Code de création Agent");
                if (code === 'baule') {
                    adminCodeField.value = code;
                } else {
                    alert("Code incorrect. Vous ne pouvez pas créer un compte agent.");
                    window.location.href = 'login.php';
                }
            }
        }

        function toggleCreateAgentForm() {
            var form = document.querySelector('.create-agent-form');
            form.classList.toggle('active');
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Connexion - Omnes Immobilier</h1>
        </header>

        <nav>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="toutparcourir.php">Tout Parcourir</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <li><a href="mesrdv.php">Rendez-vous</a></li>
                <li><a href="login.php">Votre Compte</a></li>
            </ul>
        </nav>

        <div class="block">
            <div class="container">
                <?php if (isset($_SESSION['role'])): ?>
                    <?php
                        $user_id = $_SESSION['role'] === 'client' ? $_SESSION['client_id'] : ($_SESSION['role'] === 'agent' ? $_SESSION['agent_id'] : $_SESSION['admin_id']);
                        $user_details = getUserDetails($conn, $_SESSION['role'], $user_id);
                    ?>
                    <div class="user-info">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <button class="create-agent-button" onclick="toggleCreateAgentForm()">Créer un compte agent</button>
                        <?php elseif ($_SESSION['role'] === 'agent'): ?>
                            <img src="agents/<?php echo htmlspecialchars($user_details['photo_id']); ?>.jpg" alt="<?php echo htmlspecialchars($user_details['nom']) . ' ' . htmlspecialchars($user_details['prenom']); ?>" style="width: 100px; height: auto;">
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user_details['telephone']); ?></p>
                            <p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($user_details['mot_de_passe']); ?></p>
                        <?php elseif ($_SESSION['role'] === 'client'): ?>
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($user_details['adresse']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user_details['telephone']); ?></p>
                            <p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($user_details['mot_de_passe']); ?></p>
                            <p><strong>Informations financières :</strong></p>
                            <p style="font-size: 14px; color: grey;">
                                <strong>Type de carte :</strong> <?php echo htmlspecialchars($user_details['carte_type']); ?><br>
                                <strong>Numéro de carte :</strong> <?php echo htmlspecialchars(substr($user_details['carte_numero'], 0, 4) . ' **** **** ' . substr($user_details['carte_numero'], -4)); ?><br>
                                <strong>Nom sur la carte :</strong> <?php echo htmlspecialchars($user_details['carte_nom']); ?><br>
                                <strong>Date d'expiration :</strong> <?php echo htmlspecialchars($user_details['carte_expiration']); ?><br>
                                <strong>Code de sécurité :</strong> <?php echo htmlspecialchars('***'); ?>
                            </p>
                        <?php endif; ?>
                        <form method="GET" action="login.php">
                            <button type="submit" name="logout" class="logout-button">Déconnexion</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="login-form">
                        <h2>Se connecter</h2>
                        <?php if (isset($error) && !isset($_POST['register'])): ?>
                            <div class="error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="login.php">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                            <label for="role">Je suis</label>
                            <select id="role" name="role" required>
                                <option value="client">Client</option>
                                <option value="agent">Agent</option>
                                <option value="admin">Administrateur</option>
                            </select>
                            <button type="submit">Se connecter</button>
                        </form>
                    </div>

                    <div class="register-form">
                        <h2>Créer un compte</h2>
                        <?php if (isset($success)): ?>
                            <div class="success"><?php echo $success; ?></div>
                            <script>
                                setTimeout(function() {
                                    document.querySelector('.success').style.display = 'none';
                                }, 2000);
                            </script>
                        <?php endif; ?>
                        <?php if (isset($error) && isset($_POST['register'])): ?>
                            <div class="error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="register.php" onsubmit="checkRole()">
                            <input type="hidden" name="register" value="1">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" required>
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" required>
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" required>
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <label for="telephone">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" required>
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                            <label for="carte_type">Type de carte</label>
                            <input type="text" id="carte_type" name="carte_type" required>
                            <label for="carte_numero">Numéro de carte</label>
                            <input type="text" id="carte_numero" name="carte_numero" required>
                            <label for="carte_nom">Nom sur la carte</label>
                            <input type="text" id="carte_nom" name="carte_nom" required>
                            <label for="carte_expiration">Date d'expiration</label>
                            <input type="text" id="carte_expiration" name="carte_expiration" required>
                            <label for="carte_code">Code de sécurité</label>
                            <input type="text" id="carte_code" name="carte_code" required>
                            <input type="hidden" id="admin-code-field" name="admin_code" value="">
                            <button type="submit">Créer un compte</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulaire de création d'agent -->
        <div class="create-agent-form">
            <h2>Créer un agent immobilier</h2>
            <form method="POST" action="create_agent.php" enctype="multipart/form-data">
                <label for="agent_nom">Nom</label>
                <input type="text" id="agent_nom" name="agent_nom" required>
                <label for="agent_prenom">Prénom</label>
                <input type="text" id="agent_prenom" name="agent_prenom" required>
                <label for="agent_email">Email</label>
                <input type="email" id="agent_email" name="agent_email" required>
                <label for="agent_telephone">Téléphone</label>
                <input type="text" id="agent_telephone" name="agent_telephone" required>
                <label for="agent_specialite">Spécialité</label>
                <input type="text" id="agent_specialite" name="agent_specialite" required>
                <label for="agent_jours_travail">Jours de travail</label>
                <input type="text" id="agent_jours_travail" name="agent_jours_travail" required>
                <label for="agent_mot_de_passe">Mot de passe</label>
                <input type="password" id="agent_mot_de_passe" name="agent_mot_de_passe" required>
                <label for="agent_photo">Photo (JPG)</label>
                <input type="file" id="agent_photo" name="agent_photo" accept=".jpg" required>
                <label for="agent_cv">CV (PDF)</label>
                <input type="file" id="agent_cv" name="agent_cv" accept=".pdf" required>
                <button type="submit">Créer un agent</button>
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
</body>
</html>
<?php


session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agent"; // Assurez-vous que le nom de la base de données est correct

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['register'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    if ($role === 'client') {
        $sql = "SELECT * FROM clients WHERE email = ? AND mot_de_passe = ?";
    } elseif ($role === 'agent') {
        $sql = "SELECT * FROM agents_immobiliers WHERE email = ? AND mot_de_passe = ?";
    } else {
        $sql = "SELECT * FROM administrateurs WHERE email = ? AND mot_de_passe = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $mot_de_passe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['role'] = $role;
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];

        // Stocker l'ID dans la session selon le rôle
        if ($role === 'client') {
            $_SESSION['client_id'] = $user['client_id'];
        } elseif ($role === 'agent') {
            $_SESSION['agent_id'] = $user['agent_id'];
        } else {
            $_SESSION['admin_id'] = $user['admin_id'];
        }

        header("Location: login.php");
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

function getUserDetails($conn, $role, $user_id) {
    if ($role === 'client') {
        $sql = "SELECT * FROM clients WHERE client_id = ?";
    } elseif ($role === 'agent') {
        $sql = "SELECT * FROM agents_immobiliers WHERE agent_id = ?";
    } else {
        $sql = "SELECT * FROM administrateurs WHERE admin_id = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Omnes Immobilier</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .block {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            margin: 20px;
        }
        .container {
            display: flex;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .login-form, .register-form, .user-info {
            padding: 20px;
            width: 300px;
        }
        .login-form {
            border-right: 1px solid #eeeeee;
        }
        .user-info p {
            font-size: 18px;
            margin: 10px 0;
        }
        .logout-button, .create-agent-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555555;
        }
        input[type="email"],
        input[type="text"],
        input[type="password"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c0392b;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
        }
        .success {
            color: #27ae60;
            margin-bottom: 15px;
        }
        .create-agent-form {
            display: none;
            flex-direction: column;
            align-items: center;
        }
        .create-agent-form.active {
            display: flex;
        }
    </style>
    <script>
        function checkRole() {
            var role = document.getElementById('role').value;
            var adminCodeField = document.getElementById('admin-code-field');
            if (role === 'agent') {
                var code = prompt("Code de création Agent");
                if (code === 'baule') {
                    adminCodeField.value = code;
                } else {
                    alert("Code incorrect. Vous ne pouvez pas créer un compte agent.");
                    window.location.href = 'login.php';
                }
            }
        }

        function toggleCreateAgentForm() {
            var form = document.querySelector('.create-agent-form');
            form.classList.toggle('active');
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Connexion - Omnes Immobilier</h1>
        </header>

        <nav>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="toutparcourir.php">Tout Parcourir</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <li><a href="mesrdv.php">Rendez-vous</a></li>
                <li><a href="login.php">Votre Compte</a></li>
            </ul>
        </nav>

        <div class="block">
            <div class="container">
                <?php if (isset($_SESSION['role'])): ?>
                    <?php
                        $user_id = $_SESSION['role'] === 'client' ? $_SESSION['client_id'] : ($_SESSION['role'] === 'agent' ? $_SESSION['agent_id'] : $_SESSION['admin_id']);
                        $user_details = getUserDetails($conn, $_SESSION['role'], $user_id);
                    ?>
                    <div class="user-info">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <button class="create-agent-button" onclick="toggleCreateAgentForm()">Créer un compte agent</button>
                        <?php elseif ($_SESSION['role'] === 'agent'): ?>
                            <img src="agents/<?php echo htmlspecialchars($user_details['photo_id']); ?>.jpg" alt="<?php echo htmlspecialchars($user_details['nom']) . ' ' . htmlspecialchars($user_details['prenom']); ?>" style="width: 100px; height: auto;">
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user_details['telephone']); ?></p>
                            <p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($user_details['mot_de_passe']); ?></p>
                        <?php elseif ($_SESSION['role'] === 'client'): ?>
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_details['nom']); ?></p>
                            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user_details['prenom']); ?></p>
                            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($user_details['adresse']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user_details['telephone']); ?></p>
                            <p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($user_details['mot_de_passe']); ?></p>
                            <p><strong>Informations financières :</strong></p>
                            <p style="font-size: 14px; color: grey;">
                                <strong>Type de carte :</strong> <?php echo htmlspecialchars($user_details['carte_type']); ?><br>
                                <strong>Numéro de carte :</strong> <?php echo htmlspecialchars(substr($user_details['carte_numero'], 0, 4) . ' **** **** ' . substr($user_details['carte_numero'], -4)); ?><br>
                                <strong>Nom sur la carte :</strong> <?php echo htmlspecialchars($user_details['carte_nom']); ?><br>
                                <strong>Date d'expiration :</strong> <?php echo htmlspecialchars($user_details['carte_expiration']); ?><br>
                                <strong>Code de sécurité :</strong> <?php echo htmlspecialchars('***'); ?>
                            </p>
                        <?php endif; ?>
                        <form method="GET" action="login.php">
                            <button type="submit" name="logout" class="logout-button">Déconnexion</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="login-form">
                        <h2>Se connecter</h2>
                        <?php if (isset($error) && !isset($_POST['register'])): ?>
                            <div class="error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="login.php">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                            <label for="role">Je suis</label>
                            <select id="role" name="role" required>
                                <option value="client">Client</option>
                                <option value="agent">Agent</option>
                                <option value="admin">Administrateur</option>
                            </select>
                            <button type="submit">Se connecter</button>
                        </form>
                    </div>

                    <div class="register-form">
                        <h2>Créer un compte</h2>
                        <?php if (isset($success)): ?>
                            <div class="success"><?php echo $success; ?></div>
                            <script>
                                setTimeout(function() {
                                    document.querySelector('.success').style.display = 'none';
                                }, 2000);
                            </script>
                        <?php endif; ?>
                        <?php if (isset($error) && isset($_POST['register'])): ?>
                            <div class="error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="register.php" onsubmit="checkRole()">
                            <input type="hidden" name="register" value="1">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" required>
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" required>
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" required>
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <label for="telephone">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" required>
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                            <label for="carte_type">Type de carte</label>
                            <input type="text" id="carte_type" name="carte_type" required>
                            <label for="carte_numero">Numéro de carte</label>
                            <input type="text" id="carte_numero" name="carte_numero" required>
                            <label for="carte_nom">Nom sur la carte</label>
                            <input type="text" id="carte_nom" name="carte_nom" required>
                            <label for="carte_expiration">Date d'expiration</label>
                            <input type="text" id="carte_expiration" name="carte_expiration" required>
                            <label for="carte_code">Code de sécurité</label>
                            <input type="text" id="carte_code" name="carte_code" required>
                            <input type="hidden" id="admin-code-field" name="admin_code" value="">
                            <button type="submit">Créer un compte</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulaire de création d'agent -->
        <div class="create-agent-form">
            <h2>Créer un agent immobilier</h2>
            <form method="POST" action="create_agent.php" enctype="multipart/form-data">
                <label for="agent_nom">Nom</label>
                <input type="text" id="agent_nom" name="agent_nom" required>
                <label for="agent_prenom">Prénom</label>
                <input type="text" id="agent_prenom" name="agent_prenom" required>
                <label for="agent_email">Email</label>
                <input type="email" id="agent_email" name="agent_email" required>
                <label for="agent_telephone">Téléphone</label>
                <input type="text" id="agent_telephone" name="agent_telephone" required>
                <label for="agent_specialite">Spécialité</label>
                <input type="text" id="agent_specialite" name="agent_specialite" required>
                <label for="agent_jours_travail">Jours de travail</label>
                <input type="text" id="agent_jours_travail" name="agent_jours_travail" required>
                <label for="agent_mot_de_passe">Mot de passe</label>
                <input type="password" id="agent_mot_de_passe" name="agent_mot_de_passe" required>
                <label for="agent_photo">Photo (JPG)</label>
                <input type="file" id="agent_photo" name="agent_photo" accept=".jpg" required>
                <label for="agent_cv">CV (PDF)</label>
                <input type="file" id="agent_cv" name="agent_cv" accept=".pdf" required>
                <button type="submit">Créer un agent</button>
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
</body>
</html>
