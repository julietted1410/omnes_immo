<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['agent_nom'];
    $prenom = $_POST['agent_prenom'];
    $email = $_POST['agent_email'];
    $telephone = $_POST['agent_telephone'];
    $specialite = $_POST['agent_specialite'];
    $jours_travail = $_POST['agent_jours_travail'];
    $mot_de_passe = $_POST['agent_mot_de_passe'];
    $photo = $_FILES['agent_photo'];
    $cv = $_FILES['agent_cv'];

    // Sauvegarde des fichiers
    $photo_path = 'agents/' . basename($photo['name']);
    $cv_path = 'agents/' . basename($cv['name']);

    if (move_uploaded_file($photo['tmp_name'], $photo_path) && move_uploaded_file($cv['tmp_name'], $cv_path)) {
        $sql = "INSERT INTO agents_immobiliers (nom, prenom, email, telephone, specialite, jours_travail, mot_de_passe, photo_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $nom, $prenom, $email, $telephone, $specialite, $jours_travail, $mot_de_passe, $photo_id);
        if ($stmt->execute()) {
            header("Location: login.php?success=Agent créé avec succès.");
        } else {
            echo "Erreur lors de la création de l'agent.";
        }
        $stmt->close();
    } else {
        echo "Erreur lors du téléchargement des fichiers.";
    }
}

$conn->close();
?>