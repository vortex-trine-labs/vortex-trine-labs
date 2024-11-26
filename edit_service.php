<?php
// Include the database connection
include 'db_connection.php';

// Check if `app_ref` is provided in the URL
if (!isset($_GET['app_ref']) || empty($_GET['app_ref'])) {
    die("Application Reference Number is missing!");
}

// Retrieve the application reference number from the URL
$app_ref = $_GET['app_ref'];

// Fetch the existing details for the service booking
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
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $status_description = $_POST['status_description'];
    $expected_delivery = $_POST['expected_delivery'];
    $final_price = $_POST['final_price'];
    $payment_status = $_POST['payment_status'];
    $status_number = $_POST['status_number'];

    // Ensure expected delivery date is after the booking date
    if (strtotime($expected_delivery) < strtotime($service['last_update'])) {
        $error_message = "Expected delivery date must be after the last update date.";
    } else {
        // Update the service booking in the database
        $update_query = "UPDATE service_bookings 
                         SET status = ?, status_description = ?, expected_delivery = ?, final_price = ?, 
                             payment_status = ?, status_number = ?, last_update = NOW() 
                         WHERE app_ref = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssdsis", $status, $status_description, $expected_delivery, $final_price, $payment_status, $status_number, $app_ref);

        if ($stmt->execute()) {
            $success_message = "Service request updated successfully!";
            // Refresh service data
            $query = "SELECT * FROM service_bookings WHERE app_ref = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $app_ref);
            $stmt->execute();
            $result = $stmt->get_result();
            $service = $result->fetch_assoc();
        } else {
            $error_message = "Error updating service request. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #040677;
            color: white;
            padding-top: 20px;
        }
        .container {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            max-width: 700px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Service Request</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <!-- Non-editable fields -->
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($service['name']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($service['email']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Mobile</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($service['number']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Organization</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($service['organization']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Need</label>
                <textarea class="form-control" disabled><?php echo htmlspecialchars($service['need']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" disabled><?php echo htmlspecialchars($service['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Referral</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($service['referral']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" value="<?php echo htmlspecialchars($service['deadline']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Budget</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($service['budget']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Application Reference</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($service['app_ref']); ?>" disabled>
            </div>

            <!-- Editable fields -->
            <div class="mb-3">
                <label class="form-label">Status</label>
                <input type="text" class="form-control" name="status" value="<?php echo htmlspecialchars($service['status']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status Description</label>
                <textarea class="form-control" name="status_description" required><?php echo htmlspecialchars($service['status_description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Expected Delivery</label>
                <input type="date" class="form-control" name="expected_delivery" value="<?php echo htmlspecialchars($service['expected_delivery']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Final Price</label>
                <input type="number" class="form-control" name="final_price" value="<?php echo htmlspecialchars($service['final_price']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Status</label>
                <select class="form-control" name="payment_status" required>
                    <option value="Pending" <?php if ($service['payment_status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Half Paid" <?php if ($service['payment_status'] === 'Half Paid') echo 'selected'; ?>>Half Paid</option>
                    <option value="Paid" <?php if ($service['payment_status'] === 'Paid') echo 'selected'; ?>>Paid</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status Number</label>
                <select class="form-control" name="status_number" required>
                    <option value="0" <?php if ($service['status_number'] == 0) echo 'selected'; ?>>Submitted</option>
                    <option value="1" <?php if ($service['status_number'] == 1) echo 'selected'; ?>>Accepted</option>
                    <option value="2" <?php if ($service['status_number'] == 2) echo 'selected'; ?>>Proposal</option>
                    <option value="3" <?php if ($service['status_number'] == 3) echo 'selected'; ?>>Payment</option>
                    <option value="4" <?php if ($service['status_number'] == 4) echo 'selected'; ?>>Working</option>
                    <option value="5" <?php if ($service['status_number'] == 5) echo 'selected'; ?>>Completed</option>
                    <option value="6" <?php if ($service['status_number'] == 6) echo 'selected'; ?>>Revision</option>
                    <option value="7" <?php if ($service['status_number'] == 7) echo 'selected'; ?>>Handed Over</option>
                    <option value="8" <?php if ($service['status_number'] == 8) echo 'selected'; ?>>Cancelled by User</option>
                    <option value="9" <?php if ($service['status_number'] == 9) echo 'selected'; ?>>Cancelled by Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
        <a href="a_dashboard.php" class="btn btn-primary">Dashboard</a>
    </div>
</body>
</html>
