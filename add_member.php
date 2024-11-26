<?php
// Include the database connection
include 'db_connection.php';  // Assuming you have the db_connection.php file

// Start the session to check if the user is logged in and their role
session_start();

// Check if the user is logged in and has the 'CEO' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'CEO') {
    header("Location: a_dashboard.php");
    exit();
}

// Initialize error message variable
$error_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input values
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];

    // Check if username or email already exists
    $check_user_query = "SELECT * FROM admin WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_user_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Username or Email already exists. Please choose a different one.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $insert_query = "INSERT INTO admin (username, password, email, full_name, role) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssss", $username, $hashed_password, $email, $full_name, $role);
        
        if ($stmt->execute()) {
            // Redirect to the dashboard or any other page after successful registration
            header("Location: a_dashboard.php");
            exit();
        } else {
            $error_message = "There was an error adding the member. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Member</title>

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
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            border-radius: 5px;
        }

        .btn-danger {
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Add New Member</h2>

        <!-- Display error message if any -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add Member Form -->
        <form action="add_member.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="superadmin">Super Admin</option>
                    <option value="CEO">CEO</option>
                    <option value="Developer">Developer</option>
                    <option value="HR">HR</option>
                    <option value="Computer Admin">Computer Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Add Member</button>
        </form>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
