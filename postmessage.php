<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'client' && $_SESSION['role'] !== 'agent')) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agent";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$agent_id = $_POST['agent_id'];
$client_id = $_POST['client_id'];
$message = $_POST['message'];
$sender = $_POST['sender'];

$sql = "INSERT INTO chats (agent_id, client_id, message, sender) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $agent_id, $client_id, $message, $sender);
$stmt->execute();

$stmt->close();
$conn->close();
?>
