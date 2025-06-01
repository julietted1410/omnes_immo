<?php
$agent_id = $_POST['agent_id'];
$day_of_week = $_POST['day_of_week'];
$day_of_month = $_POST['day_of_month'];
$month = $_POST['month'];
$year = $_POST['year'];

$conn = new mysqli("localhost", "root", "", "agent");
if ($conn->connect_error) {
    die("Erreur");
}

$jours_feries = ['1-1', '1-5', '8-5', '14-7', '15-8', '1-11', '11-11', '25-12'];
$key = "$day_of_month-$month";
if (in_array($key, $jours_feries)) {
    echo json_encode(array_fill_keys(['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00'], false));
    exit;
}

$stmt = $conn->prepare("SELECT jours_travail FROM agents_immobiliers WHERE agent_id = ?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$stmt->bind_result($jours_str);
$stmt->fetch();
$stmt->close();

$jours = array_map('trim', explode(',', $jours_str));
if (!in_array(ucfirst($day_of_week), $jours)) {
    echo json_encode(array_fill_keys(['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00'], false));
    exit;
}

$dispo = array_fill_keys(['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00'], true);

$sql = "SELECT time FROM appointments WHERE agent_id = ? AND day_of_month = ? AND month = ? AND year = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $agent_id, $day_of_month, $month, $year);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $h = substr($row['time'], 0, 5);
    if (isset($dispo[$h])) $dispo[$h] = false;
}
echo json_encode($dispo);
