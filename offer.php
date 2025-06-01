<?php
session_start();
$conn = new mysqli("localhost", "root", "", "agent");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Simuler un client connecté
if (!isset($_SESSION['client_id'])) {
    die("Vous devez être connecté en tant que client pour faire une offre.");
}

$client_id = $_SESSION['client_id'];
$enchere_id = $_POST['enchere_id'];
$montant = $_POST['montant'];

// Vérifier que l'enchère est encore ouverte
$sql_check = "SELECT date_fin, statut FROM encheres WHERE enchere_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $enchere_id);
$stmt_check->execute();
$stmt_check->bind_result($date_fin, $statut);
$stmt_check->fetch();
$stmt_check->close();

if ($statut !== 'ouverte' || strtotime($date_fin) < time()) {
    die("L'enchère est terminée.");
}

// Vérifier la meilleure offre actuelle
$sql_max = "SELECT MAX(montant) FROM offres_enchere WHERE enchere_id = ?";
$stmt_max = $conn->prepare($sql_max);
$stmt_max->bind_param("i", $enchere_id);
$stmt_max->execute();
$stmt_max->bind_result($meilleur_offre);
$stmt_max->fetch();
$stmt_max->close();

if ($meilleur_offre !== null && $montant <= $meilleur_offre) {
    die("Votre offre doit être supérieure à l'offre actuelle.");
}

// Enregistrer l'offre
$sql_insert = "INSERT INTO offres_enchere (enchere_id, client_id, montant) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("iid", $enchere_id, $client_id, $montant);
$stmt_insert->execute();
$stmt_insert->close();

// Redirection ou message
header("Location: toutparcourir.php");
exit();
?>
