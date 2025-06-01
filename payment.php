<?php
session_start();

if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['client_id'];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agent";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carte_type = $_POST['carte_type'];
    $carte_numero = $_POST['carte_numero'];
    $carte_nom = $_POST['carte_nom'];
    $carte_expiration = $_POST['carte_expiration'];
    $carte_code_securite = $_POST['carte_code_securite'];

    $sql = "UPDATE clients SET carte_type = ?, carte_numero = ?, carte_nom = ?, carte_expiration = ?, carte_code_securite = ? WHERE client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $carte_type, $carte_numero, $carte_nom, $carte_expiration, $carte_code_securite, $client_id);

    if ($stmt->execute()) {
        echo "<script>alert('Informations de paiement mises à jour avec succès');</script>";
    } else {
        echo "<script>alert('Erreur lors de la mise à jour des informations de paiement');</script>";
    }
}

$sql = "SELECT * FROM clients WHERE client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations de Paiement - Omnes Immobilier</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .payment-form {
            width: 50%;
            margin: auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .payment-form h2 {
            text-align: center;
        }
        .payment-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .payment-form input[type="text"],
        .payment-form input[type="date"],
        .payment-form select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
        }
        .payment-form button {
            width: 100%;
            padding: 10px;
            background-color: #e74c3c;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        .payment-form button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Informations de Paiement</h1>
        </header>

        <form class="payment-form" method="POST" action="payment.php">
            <h2>Mettre à jour vos informations de paiement</h2>

            <label for="carte_type">Type de carte</label>
            <select id="carte_type" name="carte_type" required>
                <option value="Visa" <?php echo ($client['carte_type'] === 'Visa') ? 'selected' : ''; ?>>Visa</option>
                <option value="MasterCard" <?php echo ($client['carte_type'] === 'MasterCard') ? 'selected' : ''; ?>>MasterCard</option>
                <option value="American Express" <?php echo ($client['carte_type'] === 'American Express') ? 'selected' : ''; ?>>American Express</option>
                <option value="PayPal" <?php echo ($client['carte_type'] === 'PayPal') ? 'selected' : ''; ?>>PayPal</option>
            </select>

            <label for="carte_numero">Numéro de la carte</label>
            <input type="text" id="carte_numero" name="carte_numero" value="<?php echo htmlspecialchars($client['carte_numero']); ?>" required>

            <label for="carte_nom">Nom affiché sur la carte</label>
            <input type="text" id="carte_nom" name="carte_nom" value="<?php echo htmlspecialchars($client['carte_nom']); ?>" required>

            <label for="carte_expiration">Date d'expiration</label>
            <input type="date" id="carte_expiration" name="carte_expiration" value="<?php echo htmlspecialchars($client['carte_expiration']); ?>" required>

            <label for="carte_code_securite">Code de sécurité</label>
            <input type="text" id="carte_code_securite" name="carte_code_securite" value="<?php echo htmlspecialchars($client['carte_code_securite']); ?>" required>

            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
