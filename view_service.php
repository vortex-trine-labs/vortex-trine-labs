<?php
// Include the database connection
include 'db_connection.php';

// Check if `app_ref` is provided in the URL
if (!isset($_GET['app_ref']) || empty($_GET['app_ref'])) {
    die("Application Reference Number is missing!");
}

// Retrieve the application reference number from the URL
$app_ref = $_GET['app_ref'];

// Fetch the service booking details from the database
$query = "SELECT * FROM service_bookings WHERE app_ref = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $app_ref);
$stmt->execute();
$result = $stmt->get_result();

// Check if a record is found
if ($result->num_rows === 0) {
    die("No service request found for the given Application Reference Number.");
}

$service = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Service Request</title>

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #040677; /* Dark blue background */
            color: white;
            padding-top: 20px;
        }

        .container {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            margin: auto;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .row {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="title">Service Request Details</h2>

        <div class="row">
            <div class="col-4 label">Application Ref. Number:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['app_ref']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Name:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['name']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Email:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['email']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Mobile:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['number']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Organization:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['organization']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Need:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['need']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Description:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['description']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Referral:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['referral']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Deadline:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['deadline']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Budget:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['budget']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Status:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['status']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Status Description:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['status_description']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Expected Delivery:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['expected_delivery']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Final Price:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['final_price']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Payment Status:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['payment_status']); ?></div>
        </div>
        <div class="row">
            <div class="col-4 label">Last Update:</div>
            <div class="col-8"><?php echo htmlspecialchars($service['last_update']); ?></div>
        </div>

        <div class="text-center mt-4">
            <a href="a_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
