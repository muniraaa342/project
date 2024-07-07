<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}
include('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Event Management</title>
    <link rel="stylesheet" href="styles.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
        .search-form {
            display: none;
            margin-top: 20px;
        }
        .search-input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
    <script>
        function toggleSearchForm() {
            var form = document.getElementById('search-form');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>Campus Event Management</h1>
    </div>
    <div class="sidebar">
        <a href="index.php">Dashboard</a>
        <a href="javascript:void(0);" onclick="toggleSearchForm()">Search</a>
        <form id="search-form" action="search_result.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search events..." class="search-input" required>
            <button type="submit" style="display:none;"></button> <!-- Hidden submit button -->
        </form>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="main">
        <h1>Upcoming Events</h1>
        <div class="events-container">
            <?php
            // Fetch events from the database
            $sql = "SELECT * FROM events";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='event-card'>
                            <h2>" . $row['event_name'] . "</h2>
                            <p>Date: " . $row['event_date'] . "</p>
                            <p>Location: " . $row['event_location'] . "</p>
                            <p><a href='eventdetails1.php?id=" . $row['event_id'] . "'>View Details</a></p>
                          </div>";
                }
            } else {
                echo "No events found.";
            }
            ?>
        </div>
        <h1>My Registered Events</h1>
        <div id='calendar'></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    <?php
                    // Fetch registered events for the logged-in student
                    $user_id = $_SESSION['user_id']; // Assuming you have a session variable for user_id
                    $reg_sql = "SELECT events.event_name, events.event_date 
                                FROM registrations 
                                JOIN events ON registrations.event_id = events.event_id 
                                WHERE registrations.user_id = '$user_id'";
                    $reg_result = $conn->query($reg_sql);
                    if ($reg_result->num_rows > 0) {
                        while ($reg_row = $reg_result->fetch_assoc()) {
                            echo "{ title: '" . $reg_row['event_name'] . "', start: '" . $reg_row['event_date'] . "' },";
                        }
                    }
                    ?>
                ]
            });
            calendar.render();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
