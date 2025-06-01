<?php
$conn = new mysqli("localhost", "root", "", "agent");
if ($conn->connect_error) {
    die("Erreur connexion : " . $conn->connect_error);
}

$sql = "SELECT e.enchere_id, b.lieu, b.description, b.photo_id, e.prix_depart, e.date_fin,
        MAX(o.montant) AS meilleur_offre, a.nom AS agent_nom, a.prenom AS agent_prenom
        FROM encheres e
        JOIN biens_a_vendre b ON e.bien_id = b.bien_id
        JOIN agents_immobiliers a ON b.agent_id = a.agent_id
        LEFT JOIN offres_enchere o ON o.enchere_id = e.enchere_id
        GROUP BY e.enchere_id";

$res = $conn->query($sql);
$encheres = [];
while ($row = $res->fetch_assoc()) {
    $encheres[] = $row;
}

header('Content-Type: application/json');
echo json_encode($encheres);
?>
