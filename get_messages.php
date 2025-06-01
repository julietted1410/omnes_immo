<?php
// Récupérer les données POST
$agent_id = $_POST['agent_id'];
$day_of_week = $_POST['day_of_week'];
$day_of_month = $_POST['day_of_month'];
$month = $_POST['month'];
$year = $_POST['year'];

// Connexion DB
$conn = new mysqli("localhost", "root", "", "agent");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Définir les jours fériés manuellement
$jours_feries = [
    '1-1', '1-5', '8-5', '14-7', '15-8', '1-11', '11-11', '25-12'
];
$key_ferie = "$day_of_month-$month";
if (in_array($key_ferie, $jours_feries)) {
    // Si jour férié → tous les créneaux sont indisponibles
    $result = array_fill_keys(['09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
                                '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
                                '17:00', '17:30', '18:00'], false);
    echo json_encode($result);
    exit;
}

// Vérifier si l’agent travaille ce jour
$agent_sql = "SELECT jours_travail FROM agents_immobiliers WHERE agent_id = ?";
$stmt = $conn->prepare($agent_sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$stmt->bind_result($jours_travail_str);
$stmt->fetch();
$stmt->close();

$jours_travail = array_map('trim', explode(',', $jours_travail_str));
if (!in_array(ucfirst($day_of_week), $jours_travail)) {
    // L’agent ne travaille pas ce jour-là
    $result = array_fill_keys(['09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
                                '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
                                '17:00', '17:30', '18:00'], false);
    echo json_encode($result);
    exit;
}

// Initialiser tous les créneaux à disponible
$availability = array_fill_keys(['09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
                                 '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
                                 '17:00', '17:30', '18:00'], true);

// Vérifier les créneaux déjà réservés
$appointment_sql = "SELECT time FROM appointments 
                    WHERE agent_id = ? AND day_of_month = ? AND month = ? AND year = ?";
$stmt = $conn->prepare($appointment_sql);
$stmt->bind_param("iiii", $agent_id, $day_of_month, $month, $year);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $time = substr($row['time'], 0, 5); // exemple : "09:00"
    if (isset($availability[$time])) {
        $availability[$time] = false;
    }
}

echo json_encode($availability);

