<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}
include('config.php');

// Fetch event details based on event_id
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $sql = "SELECT * FROM events WHERE event_id = $event_id";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found.";
        exit();
    }
} else {
    echo "Event ID not specified.";
    exit();
}

// Handle event registration form submission
$registration_success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_event'])) {
    $user_id = $_SESSION['user_id']; // Assuming you have a session variable for user_id
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $matric_no = mysqli_real_escape_string($conn, $_POST['matric_no']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $programme = mysqli_real_escape_string($conn, $_POST['programme']);

    // Insert registration data into database
    $register_sql = "INSERT INTO registrations (user_id, event_id, name, matric_no, faculty, programme)
                     VALUES ('$user_id', '$event_id', '$name', '$matric_no', '$faculty', '$programme')";
    
    if (mysqli_query($conn, $register_sql)) {
        $registration_success = true;
    } else {
        echo "Error: " . $register_sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h1>Event Details</h1>
    </div>
    <div class="sidebar">
        <a href="index.php">Dashboard</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="main">
        <?php if (!empty($event['event_poster'])): ?>
            <img src="<?php echo $event['event_poster']; ?>" alt="Event Poster" style="max-width: 100%; height: auto;">
        <?php endif; ?>

        <h2><?php echo $event['event_name']; ?></h2>
        <p>Date: <?php echo $event['event_date']; ?></p>
        <p>Location: <?php echo $event['event_location']; ?></p>
        <p>Description: <?php echo $event['event_description']; ?></p>

        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#registerModal">Register for Event</button>

        <!-- Registration Modal -->
        <div id="registerModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
        
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Register for Event</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="matric_no">Matric No:</label>
                                <input type="text" class="form-control" id="matric_no" name="matric_no" required>
                            </div>
                            <div class="form-group">
                                <label for="faculty">Faculty:</label>
                                <select class="form-control" id="faculty" name="faculty" required>
                                    <option value="">Select Faculty</option>
                                    <option value="FST">FST</option>
                                    <option value="FKAB">FKAB</option>
                                    <option value="FEM">FEM</option>
                                    <option value="FPQS">FPQS</option>
                                    <option value="FPBU">FPBU</option>
                                    <option value="FKP">FKP</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="programme">Programme:</label>
                                <input type="text" class="form-control" id="programme" name="programme" required>
                            </div>
                            <button type="submit" name="register_event" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
        
            </div>
        </div>

        <?php if ($registration_success): ?>
            <div class="alert alert-success" role="alert">
                Registration successful!
            </div>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php
mysqli_close($conn);
?>
