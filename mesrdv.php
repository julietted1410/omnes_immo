<?php
session_start();

if (!isset($_GET['agent_id']) || !isset($_SESSION['client_id']) || !isset($_GET['bien_id'])) {
    die("Agent ID, Client ID, and Bien ID are required. Please log in.");
}

$agent_id = $_GET['agent_id'];
$client_id = $_SESSION['client_id'];
$bien_id = $_GET['bien_id'];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agent";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Fonction pour obtenir les détails de l'agent
function getAgentDetails($conn, $agent_id) {
    $sql = "SELECT * FROM agents_immobiliers WHERE agent_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fonction pour réserver un rendez-vous
function bookAppointment($conn, $agent_id, $client_id, $bien_id, $day_of_week, $time, $day_of_month, $month, $year) {
    // Vérifier si l'agent est disponible
    $sql = "SELECT * FROM appointments WHERE agent_id = ? AND day_of_week = ? AND time = ? AND day_of_month = ? AND month = ? AND year = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issiii", $agent_id, $day_of_week, $time, $day_of_month, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Insérer le rendez-vous
        $sql = "INSERT INTO appointments (agent_id, client_id, bien_id, day_of_week, time, day_of_month, month, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiissiii", $agent_id, $client_id, $bien_id, $day_of_week, $time, $day_of_month, $month, $year);
        $stmt->execute();

        echo "<script>alert('Rendez-vous pris avec succès !');</script>";
    } else {
        echo "<script>alert('Le créneau sélectionné n\'est pas disponible. Veuillez choisir un autre créneau.');</script>";
    }
}

// Fonction pour vérifier si une date est un jour férié
function isHoliday($day_of_month, $month) {
    $holidays = [
        '1-5' => 'Fête du Travail' // Ajouter d'autres jours fériés au besoin
    ];

    return isset($holidays["$day_of_month-$month"]);
}

$agent_details = getAgentDetails($conn, $agent_id);
$working_days = explode(', ', $agent_details['jours_travail']);

// Traiter la demande de réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day_of_week'])) {
    $day_of_week = $_POST['day_of_week'];
    $time = $_POST['time'];
    $day_of_month = $_POST['day_of_month'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    if (!isHoliday($day_of_month, $month)) {
        bookAppointment($conn, $agent_id, $client_id, $bien_id, $day_of_week, $time, $day_of_month, $month, $year);
        echo "<script>window.location.href='rdv.php?agent_id={$agent_id}&bien_id={$bien_id}';</script>";
    } else {
        echo "<script>alert('Le jour sélectionné est un jour férié. Veuillez choisir un autre jour.');</script>";
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
                <p>Pour toute question ou demande d'information, n'hésitez pas à nous contacter :</p>
                <p>Email : info@omnesimmobilier.com</p>
                <p>Téléphone : +123 456 789</p>
                <p>Adresse : 123 Rue des Immobilier, Ville, Pays</p>
                <br>
                <p>&copy; 2024 Omnes Immobilier. Tous droits réservés.</p>
            </div>
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5413.220596222361!2d-2.4065150240548543!3d47.282872010277124!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48055ca3e95a9abd%3A0xef3cffeeafc73d53!2s22%20Av.%20Drevet%2C%2044500%20La%20Baule-Escoublac!5e0!3m2!1sfr%2sfr!4v1716901768269!5m2!1sfr%2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
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

