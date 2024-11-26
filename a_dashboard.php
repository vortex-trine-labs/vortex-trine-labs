<?php
// Include the database connection
include 'db_connection.php';  // Assuming you have the db_connection.php file

// Start the session to check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's role from the session
$user_role = $_SESSION['role'];

// Initialize error message variable
$error_message = '';

// Handle search query
$search_query = "";
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_value = $_GET['search'];
    $search_query = "WHERE app_ref LIKE '%$search_value%' OR name LIKE '%$search_value%' OR number LIKE '%$search_value%' OR email LIKE '%$search_value%' OR need LIKE '%$search_value%'";
}

// Fetch service bookings based on search query
$fetch_services_query = "SELECT * FROM service_bookings $search_query";
$result = $conn->query($fetch_services_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Requests Dashboard</title>

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

        .table th, .table td {
            vertical-align: middle;
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

        .btn-logout {
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
        }

        .btn-logout:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Service Requests Dashboard</h2>

        <!-- Search form -->
        <form action="a_dashboard.php" method="GET" class="mb-4">
            <div class="d-flex justify-content-between">
                <input type="text" name="search" class="form-control w-75" placeholder="Search by App Ref, Name, Mobile, Email, or Need" value="<?php echo isset($search_value) ? $search_value : ''; ?>">
                <button type="submit" class="btn btn-primary ml-2">Search</button>
            </div>
        </form>

        <!-- Display error message if any -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add member button (only visible if role is CEO) -->
        <?php if ($user_role == 'CEO'): ?>
            <a href="add_member.php" class="btn btn-success mb-3">Add Member</a>
        <?php endif; ?>

        <!-- Table to display service requests -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>App Ref Number</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Need</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['app_ref']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['number']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['need']; ?></td>
                            <td>
                                <!-- View, Edit, Delete, Notify Actions -->
                                <a href="view_service.php?app_ref=<?php echo $row['app_ref']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit_service.php?app_ref=<?php echo $row['app_ref']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_service.php?app_ref=<?php echo $row['app_ref']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                <a href="notify_user.php?app_ref=<?php echo $row['app_ref']; ?>" class="btn btn-success btn-sm">Notify</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No service requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Logout Button -->
        <a href="logout.php" class="btn btn-logout btn-block">Logout</a>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
