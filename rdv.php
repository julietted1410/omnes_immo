<?php
session_start();

if (!isset($_GET['agent_id']) || !isset($_SESSION['client_id']) || !isset($_GET['bien_id'])) {
    die("Veuillez vous connecter.");
}

$agent_id = $_GET['agent_id'];
$client_id = $_SESSION['client_id'];
$bien_id = $_GET['bien_id'];

$conn = new mysqli("localhost", "root", "", "agent");
if ($conn->connect_error) {
    die("Erreur");
}

function getAgentDetails($conn, $agent_id) {
    $stmt = $conn->prepare("SELECT * FROM agents_immobiliers WHERE agent_id = ?");
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc();
}

function bookAppointment($conn, $agent_id, $client_id, $bien_id, $day_of_week, $time, $day_of_month, $month, $year) {
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE agent_id = ? AND day_of_week = ? AND time = ? AND day_of_month = ? AND month = ? AND year = ?");
    $stmt->bind_param("issiii", $agent_id, $day_of_week, $time, $day_of_month, $month, $year);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO appointments (agent_id, client_id, bien_id, day_of_week, time, day_of_month, month, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissiii", $agent_id, $client_id, $bien_id, $day_of_week, $time, $day_of_month, $month, $year);
        $stmt->execute();
        echo "<script>alert('RDV pris');</script>";
    } else {
        echo "<script>alert('Créneau non dispo');</script>";
    }
}

function isHoliday($day, $month) {
    $jours = ['1-5'];
    return in_array("$day-$month", $jours);
}

$agent = getAgentDetails($conn, $agent_id);
$jours = explode(', ', $agent['jours_travail']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day_of_week'])) {
    $day_of_week = $_POST['day_of_week'];
    $time = $_POST['time'];
    $day = $_POST['day_of_month'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    if (!isHoliday($day, $month)) {
        bookAppointment($conn, $agent_id, $client_id, $bien_id, $day_of_week, $time, $day, $month, $year);
        echo "<script>window.location.href='rdv.php?agent_id=$agent_id&bien_id=$bien_id';</script>";
    } else {
        echo "<script>alert('Jour férié');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous avec <?php echo htmlspecialchars($agent_details['prenom']) . ' ' . htmlspecialchars($agent_details['nom']); ?> - Omnes Immobilier</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
        .agent-details {
            display: flex;
            align-items: center;
        }
        .agent-photo {
            margin-right: 20px;
        }
        .availability-table {
            width: 100%;
            border-collapse: collapse;
        }
        .availability-table th, .availability-table td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }
        .availability-table th {
            background-color: #f2f2f2;
        }
        .available {
            background-color: #d3d3d3;
            cursor: pointer;
        }
        .not-available {
            background-color: #707070;
            color: #fff;
        }
        .datepicker {
            margin: 20px 0;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .button {
            padding: 15px 25px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            color: inherit;
        }
        .btn-appointment {
            background-color: #d4edda;
        }
        .btn-communicate {
            background-color: #cce5ff;
        }
        .btn-cv {
            background-color: #fff3cd;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Rendez-vous avec <?php echo htmlspecialchars($agent_details['prenom']) . ' ' . htmlspecialchars($agent_details['nom']); ?> - Omnes Immobilier</h1>
        </header>

        <nav>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="toutparcourir.php">Tout Parcourir</a></li>
                <li><a href="recherche.php">Recherche</a></li>
                <li><a href="mesrdv.php">Rendez-vous</a></li>

                <?php if(isset($_SESSION['user_nom']) && isset($_SESSION['user_prenom'])): ?>
                    <li><a href="login.php"><?php echo htmlspecialchars($_SESSION['user_nom'] . ' ' . $_SESSION['user_prenom']); ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <section>
            <h2>Détails de l'agent</h2>
            <div class="agent-details">
                <div class="agent-card">
                    <img src="agents/<?php echo htmlspecialchars($agent_details['photo_id']); ?>.jpg" alt="<?php echo htmlspecialchars($agent_details['nom']) . ' ' . htmlspecialchars($agent_details['prenom']); ?>" class="agent-photo">
                </div>
                <div>
                    <h3><?php echo htmlspecialchars($agent_details['prenom']) . ' ' . htmlspecialchars($agent_details['nom']); ?></h3>
                    <p><strong>Agent immobilier agréé</strong></p>
                    <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($agent_details['telephone']); ?></p>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($agent_details['email']); ?>"><?php echo htmlspecialchars($agent_details['email']); ?></a></p>
                </div>
            </div>

            <h3>Disponibilité</h3>
            <div class="datepicker">
                <label for="appointment_date">Choisissez une date :</label>
                <input type="text" id="appointment_date" name="appointment_date">
            </div>
            <table class="availability-table">
                <thead>
                    <tr>
                        <th>Heure</th>
                        <th>09:00</th>
                        <th>09:30</th>
                        <th>10:00</th>
                        <th>10:30</th>
                        <th>11:00</th>
                        <th>11:30</th>
                        <th>14:00</th>
                        <th>14:30</th>
                        <th>15:00</th>
                        <th>15:30</th>
                        <th>16:00</th>
                        <th>16:30</th>
                        <th>17:00</th>
                        <th>17:30</th>
                        <th>18:00</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les créneaux horaires seront mis à jour ici -->
                </tbody>
            </table>

            <div class="button-container">
                <button class="button btn-appointment"><a href="rdv.php?agent_id=<?php echo $agent_id; ?>&bien_id=<?php echo $bien_id; ?>">Prendre un RDV</a></button>
                <button class="button btn-communicate"><a href="chat.php?agent_id=<?php echo $agent_id; ?>">Communiquer avec l'agent immobilier</a></button>
                <?php if (!empty($agent_details['cv_id'])): ?>
                    <button class="button btn-cv">
                        <a href="agents/<?php echo htmlspecialchars($agent_details['cv_id']); ?>.pdf" target="_blank">Voir son CV</a>
                    </button>
                <?php else: ?>
                    <p>Aucun CV disponible pour cet agent.</p>
                <?php endif; ?>
            </div>
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

    <form id="bookingForm" method="POST" style="display: none;">
        <input type="hidden" name="day_of_week" id="day_of_week">
        <input type="hidden" name="time" id="time">
        <input type="hidden" name="day_of_month" id="day_of_month">
        <input type="hidden" name="month" id="month">
        <input type="hidden" name="year" id="year">
    </form>

    <script>
        $(function() {
            $("#appointment_date").datepicker({
                dateFormat: "dd-mm-yy",
                firstDay: 1,
                dayNamesMin: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
                monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
                onSelect: function(dateText) {
                    const [day_of_month, month, year] = dateText.split('-').map(Number);
                    const day_of_week = new Date(year, month - 1, day_of_month).toLocaleDateString('fr-FR', { weekday: 'long' });

                    document.getElementById('day_of_month').value = day_of_month;
                    document.getElementById('month').value = month;
                    document.getElementById('year').value = year;
                    document.getElementById('day_of_week').value = day_of_week;

                    // Envoyer une requête Ajax pour obtenir les disponibilités
                    $.ajax({
                        url: 'get_availability.php',
                        type: 'POST',
                        data: {
                            agent_id: '<?php echo $agent_id; ?>',
                            day_of_week: day_of_week,
                            day_of_month: day_of_month,
                            month: month,
                            year: year
                        },
                        success: function(response) {
                            const availability = JSON.parse(response);
                            updateAvailabilityTable(availability);
                        }
                    });
                }
            });
        });

        function updateAvailabilityTable(availability) {
            const timeSlots = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00'];

            const tableBody = document.querySelector('.availability-table tbody');
            tableBody.innerHTML = ''; // Effacer le contenu actuel

            const row = document.createElement('tr');
            const timeCell = document.createElement('td');
            const dayOfWeek = new Date(document.getElementById('year').value, document.getElementById('month').value - 1, document.getElementById('day_of_month').value).toLocaleDateString('fr-FR', { weekday: 'long' });
            timeCell.textContent = dayOfWeek.charAt(0).toUpperCase() + dayOfWeek.slice(1);

            row.appendChild(timeCell);

            timeSlots.forEach(time => {
                const cell = document.createElement('td');
                const isAvailable = availability[time];

                if (isAvailable) {
                    cell.textContent = 'Disponible';
                    cell.classList.add('available');
                    cell.onclick = () => bookSlot(time);
                } else {
                    cell.textContent = 'Non disponible';
                    cell.classList.add('not-available');
                }

                row.appendChild(cell);
            });

            tableBody.appendChild(row);
        }

        function bookSlot(time) {
            const date = $("#appointment_date").val();
            if (date) {
                document.getElementById('time').value = time;
                document.getElementById('bookingForm').submit();
            } else {
                alert('Veuillez sélectionner une date avant de choisir un créneau horaire.');
            }
        }
    </script>
</body>
</html>
