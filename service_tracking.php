<?php
// Include your database connection file
include 'db_connection.php';

// Initialize error messages
$error_message = '';
$success_message = '';

// Start session to store the application number securely
session_start();

// Process the form if it's submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $app_number = trim($_POST['app_number']);
    $mobile_number = trim($_POST['mobile_number']);
    
    // Validate application request number and mobile number
    if (empty($app_number) || empty($mobile_number)) {
        $error_message = 'Both fields are required.';
    } else {
        // Check if the application number exists in the database
        $sql = "SELECT * FROM service_bookings WHERE app_ref = ? AND number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $app_number, $mobile_number); // Bind parameters

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $success_message = 'Application Request and Mobile Number match!';
            // Store the application reference number in session
            $_SESSION['app_number'] = $app_number;
            $_SESSION['mobile_number'] = $mobile_number;
            // Redirect to view_status.php
            header("Location: view_status.php");
            exit;
        } else {
            $error_message = 'No matching record found for the given Application Request Number and Mobile Number.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Track Service - Vortex Trine Labs</title>

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        body {
    color: #444444;
    background-color: #040677;
    font-family: "Roboto", sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    max-width: 600px;
    width: 100%;
    padding: 20px;
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}


        h1 {
            color: #040677;
            text-align: center;
        }

        form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        form input,
        form select,
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        form button {
            padding: 10px 20px;
            border: none;
            background: #1acc8d;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        form button:hover {
            background: #0fa05e;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .links {
            text-align: center;
            margin-top: 15px;
        }

        .links a {
            color: #1acc8d;
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Track Service Request</h1>

        <form action="" method="POST" onsubmit="return validateForm();">
            <label for="app_number">Application Request Number:</label>
            <input type="text" id="app_number" name="app_number" required>

            <label for="mobile_number">Mobile Number:</label>
            <input type="tel" id="mobile_number" name="mobile_number" pattern="[0-9]{10}" required>

            <button type="submit" class="btn btn-success" style="background-color: green; color: white;">Validate Request</button>
        </form>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="links">
            <a href="index.html">Back to Home</a>
            <a href="service_tracking.php">Track Request</a>
        </div>
    </div>

    <script>
        function validateForm() {
            const appNumber = document.getElementById('app_number').value;
            const mobileNumber = document.getElementById('mobile_number').value;

            // Check if both fields are not empty
            if (appNumber === '' || mobileNumber === '') {
                alert('Both Application Request Number and Mobile Number are required.');
                return false;
            }

            // Validate Mobile Number Format
            const mobilePattern = /^[0-9]{10}$/;
            if (!mobilePattern.test(mobileNumber)) {
                alert('Please enter a valid 10-digit mobile number.');
                return false;
            }

            return true;
        }
    </script>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>
