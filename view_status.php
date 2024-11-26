<?php
// Start the session to access session variables
session_start();

// Include your database connection file
include 'db_connection.php';

// Initialize variables
$app_ref = '';
$error_message = '';
$need = $status = $status_description = $expected_delivery = $last_update = $final_price = $payment_status = $name = $organization = $email = $phone = $status_number = '';

// Get the application reference number from session
if (isset($_SESSION['app_number'])) {
    $app_ref = $_SESSION['app_number'];

    // Query to fetch the service request details by app_ref
    $sql = "SELECT * FROM service_bookings WHERE app_ref = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $app_ref);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Store values from the database to variables
        $need = $row['need'];
        $status = $row['status'];
        $status_description = $row['status_description'];
        $expected_delivery = $row['expected_delivery'];
        $last_update = $row['last_update'];
        $final_price = $row['final_price'];
        $payment_status = $row['payment_status'];
        $name = $row['name'];
        $organization = $row['organization'];
        $email = $row['email'];
        $phone = $row['number'];
        $status_number = $row['status_number'];
    } else {
        $error_message = 'No service request found with this Application Reference Number.';
    }

    $stmt->close();
} else {
    $error_message = 'Application Reference Number not found in session.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Track Service - Vortex Trine Labs</title>
    <meta name="description" content="Service Request Tracking">
    <meta name="keywords" content="Vortex Trine Labs, service request, tracking">

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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
            max-width: 1000px;
            width: 100%;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: auto;
            max-height: 100vh;
        }

        h1 {
            color: #040677;
            text-align: center;
        }

        .status-container, .project-details, .user-details {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }

        .status.waiting {
            background-color: orange;
        }

        .status.pending {
            background-color: yellow;
            color: black;
        }

        .status.processing {
            background-color: blue;
        }

        .status.completed {
            background-color: green;
        }

        .status.canceled {
            background-color: red;
        }

        .timeline {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 0;
            list-style-type: none;
            opacity: 0;
            animation: fadeInSlide 1s forwards;
        }

        @keyframes fadeInSlide {
            0% {
                opacity: 0;
                transform: translateX(-20px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .timeline li {
            position: relative;
            width: 12%;
            text-align: center;
        }

        .timeline li .icon {
            font-size: 24px;
            padding: 10px;
            background-color: #ddd;
            border-radius: 50%;
            margin-bottom: 5px;
        }

        .timeline li.completed .icon {
            background-color: green;
            color: white;
        }

        .timeline li.pending .icon {
            background-color: orange;
        }

        .timeline li .label {
            display: block;
            margin-top: 5px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
        }

        .timeline li .label {
            color: #040677;
        }

        .active-status {
            color: blue !important;
        }

        .cancel-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: red;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        /* Mobile View */
        @media (max-width: 576px) {
            .timeline {
                flex-direction: column;
            }

            .timeline li {
                width: 100%;
                margin-bottom: 10px;
            }

            .user-details .col-md-6 {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
<div class="container">
    <h1>Track Service Request</h1>

    <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php else: ?>
        <div class="user-details row">
            <h3>Service Request Details</h3>

            <div class="row">
                <div class="col-md-6">
                    <div class="field">
                        <p><strong>Application Reference Number:</strong> <?php echo htmlspecialchars($app_ref); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field">
                        <p><strong>Need:</strong> <?php echo htmlspecialchars($need); ?></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="field">
                        <p><strong>Status:</strong> 
                            <span class="status <?php 
                                if (stripos($status, 'waiting') !== false) echo 'waiting';
                                elseif (stripos($status, 'pending') !== false) echo 'pending';
                                elseif (stripos($status, 'processing') !== false) echo 'processing';
                                elseif (stripos($status, 'completed') !== false) echo 'completed';
                                elseif (stripos($status, 'canceled') !== false) echo 'canceled';
                                elseif (stripos($status, 'handovered') !== false) echo 'handovered';
                                else echo 'default'; // Default class for unmatched status
                            ?>"><?php echo htmlspecialchars($status); ?></span>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field">
                        <p><strong>Status Description:</strong> <?php echo htmlspecialchars($status_description); ?></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="field">
                        <p><strong>Last Update:</strong> <?php echo htmlspecialchars($last_update); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php ?>

<style>

    .status.default {
        background-color: orangered; 
        color: black;
    }

    .status.waiting {
        background-color: orange;
        color: white;
    }

    .status.pending {
        background-color: yellow;
        color: black;
    }

    .status.processing {
        background-color: blue;
        color: white;
    }

    .status.completed {
        background-color: green;
        color: white;
    }

    .status.canceled {
        background-color: red;
        color: white;
    }

    .status.handovered {
        background-color: purple;
        color: white;
    }

    /* Row and column adjustments */
    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .col-md-6 {
        width: 48%;
        margin-bottom: 15px;
    }

    /* Adjust for mobile view */
    @media (max-width: 768px) {
        .col-md-6 {
            width: 100%;
        }
    }
</style>


<div class="user-details row">
    <h3>Project Details</h3>
    <div class="col-md-6">
    <div class="field">
        <p><strong>Quoted Budget:</strong> ₹<?php echo number_format((float)$final_price, 2); ?></p>
    </div>
    </div>
    <div class="col-md-6">
    <div class="field">
        <p><strong>Final Price:</strong> ₹<?php echo number_format((float)$final_price, 2); ?></p>
    </div>
    </div>
    <div class="col-md-6">
    <div class="field">
        <p><strong>Given Deadline:</strong> <?php echo htmlspecialchars($expected_delivery); ?></p>
    </div>
    </div>
    <div class="col-md-6">
    <div class="field">
        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($payment_status); ?></p>
    </div>
    </div>
</div>

<div class="user-details row">
<h3>User Details</h3>
    <div class="col-md-6">
        <div class="field">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="field">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="field">
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="field">
            <p><strong>Organization:</strong> <?php echo htmlspecialchars($organization); ?></p>
        </div>
    </div>
</div>

            <!-- Timeline -->
            <?php if ($status_number != 8 && $status_number != 9): ?>
                <ul class="timeline">
                    <?php 
                        $steps = [
                            'Submitted',
                            'Accepted',
                            'Proposal',
                            'Payment',
                            'Working',
                            'Completed',
                            'Revision',
                            'Handovered',
                        ];

                        for ($i = 0; $i <= 7; $i++) {
                            $class = '';
                            if ($status_number == $i) {
                                $class = 'active-status';
                            }

                            // Adjust the colors based on the status
                            if ($status_number >= 5) {
                                if ($i <= 4) {
                                    $class .= ' completed';
                                } else {
                                    $class .= ' pending';
                                }
                            } else {
                                $class .= $i == 0 ? ' completed' : ' pending';
                            }

                            echo "<li class='$class'><div class='icon'>" . ($i == 8 || $i == 9 ? '' : '✓') . "</div><span class='label'>{$steps[$i]}</span></li>";
                        }
                    ?>
                </ul>
            <?php endif; ?>

            <!-- Cancel Button -->
            <?php if ($status_number == 0): ?>
                <a href="cancel_request.php?app_ref=<?php echo $app_ref; ?>" class="cancel-button">Cancel Request</a>
            <?php endif; ?>

        <?php endif; ?>

        <div class="links">
      <div class="btn-group">
        <button class="btn btn-danger" style="background-color: red; color: white;" onclick="window.location.href='index.html'">Back to Home</button>
      </div>
    </div>
        <div class="footer-top">
        <div class="d-flex justify-content-center align-items-center py-3">
          <div class="d-flex align-items-center">
            <a href="https://www.instagram.com/vortex_trine_labs/" class="social-icon me-2"><i class="bi bi-instagram"></i></a>
            <span class="text-black">Vortex Trine Labs - "Innovating Your Ideas, One Step at a Time"</span>
          </div>
        </div>
      </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>
