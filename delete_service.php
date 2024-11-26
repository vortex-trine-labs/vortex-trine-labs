<?php
// Include the database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['app_ref'])) {
    $app_ref = $_GET['app_ref'];

    // Fetch service details to confirm existence
    $query = "SELECT * FROM service_bookings WHERE app_ref = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_ref);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        // If no service found, redirect back with an error
        header("Location: a_dashboard.php?error=Service not found.");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_ref'])) {
    $app_ref = $_POST['app_ref'];

    // Delete the service
    $delete_query = "DELETE FROM service_bookings WHERE app_ref = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $app_ref);

    if ($stmt->execute()) {
        // Redirect to dashboard with a success message
        header("Location: a_dashboard.php?success=Service request deleted successfully.");
        exit();
    } else {
        // Handle deletion failure
        $error_message = "Failed to delete the service request. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Service</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4>Confirm Deletion</h4>
        </div>
        <div class="card-body">
            <?php if (isset($service)): ?>
                <p>Are you sure you want to delete the following service request?</p>
                <ul>
                    <li><strong>Application Reference:</strong> <?php echo htmlspecialchars($service['app_ref']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($service['name']); ?></li>
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($service['email']); ?></li>
                    <li><strong>Need:</strong> <?php echo htmlspecialchars($service['need']); ?></li>
                </ul>
                <form method="POST" action="delete_service.php">
                    <input type="hidden" name="app_ref" value="<?php echo htmlspecialchars($service['app_ref']); ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <a href="a_dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">
                    <p>Invalid request. No service found.</p>
                    <a href="a_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
