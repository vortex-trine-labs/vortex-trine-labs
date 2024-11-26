<?php
// Include database connection
include('db_connection.php');

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Make sure you have installed PHPMailer using Composer

// Check if 'app_ref' is provided in the URL
if (isset($_GET['app_ref'])) {
    $app_ref = $_GET['app_ref'];

    // Fetch user email, name, and mobile number from the database using the app_ref
    $sql = "SELECT email, name, number FROM service_bookings WHERE app_ref = '$app_ref'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Update the status of the request to 'Request cancelled by user'
        $update_sql = "UPDATE service_bookings 
               SET status = 'Request cancelled by user', 
                   status_description = 'Request cancelled by user', 
                   status_number = 8 
               WHERE app_ref = '$app_ref'";

        if (mysqli_query($conn, $update_sql)) {
            // Initialize PHPMailer
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth   = true; // Enable SMTP authentication
                $mail->Username   = 'trine.devlop@gmail.com'; // SMTP username
                $mail->Password   = 'uase acra crqe vzmo'; // App Password (replace with your generated app password)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
                $mail->Port       = 587; // TCP port to connect to

                // Recipients
                $mail->setFrom('trine.devlop@gmail.com', 'Vortex Trine Labs');
                $mail->addAddress($user['email'], $user['name']); // Add a recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = "Service Request Cancellation - $app_ref";

                // Add the logo image as an embedded attachment
                $mail->addEmbeddedImage('assets/img/logo.png', 'logo_cid', 'logo.png');

                // Prepare email body
                $message = "
                <html>
                    <head>
                        <title>Service Request Cancellation</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f9f9f9;
                                margin: 0;
                                padding: 20px;
                                color: #333;
                            }
                            .container {
                                background-color: #fff;
                                max-width: 600px;
                                margin: 0 auto;
                                padding: 20px;
                                border-radius: 8px;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 20px;
                            }
                            .header img {
                                width: 150px;
                                margin-bottom: 10px;
                            }
                            .content {
                                line-height: 1.6;
                            }
                            .content p {
                                margin-bottom: 15px;
                            }
                            .footer {
                                margin-top: 20px;
                                text-align: center;
                                font-size: 12px;
                                color: #777;
                            }
                            .footer i {
                                color: #999;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <img src='cid:logo_cid' alt='Vortex Trine Labs Logo'>
                                <h2>Service Request Cancellation</h2>
                            </div>
                            <div class='content'>
                                <p>Dear <strong>{$user['name']}</strong>,</p>
                                <p>We are sorry to inform you that your service request with application reference number <strong>{$app_ref}</strong> has been cancelled as per your request. We deeply regret letting you go and would have loved to continue working with you.</p>
                                <p>If you ever decide to continue with the same application number and mobile number, please feel free to contact us anytime. We're always here to assist you with your needs.</p>
                                <p>If you'd like us to completely delete your data, just send an email with your application number, and we'll take care of the rest.</p>
                                <p>We hope to serve you again in the future. Thank you for your time with us!</p>
                            </div>
                            <div class='footer'>
                                <p>Regards, <br><strong>Vortex Trine Labs, Coimbatore</strong></p>
                                <p><i>This is a system-generated message. Please do not reply unless you have a query.</i></p>
                            </div>
                        </div>
                    </body>
                </html>
                ";

                // Set the email body
                $mail->Body    = $message;

                // Send email
                $mail->send();

                // Redirect to service_tracking page with success message
                header("Location: service_tracking.php?status=cancelled");
                exit();
            } catch (Exception $e) {
                echo "Error sending email notification: {$mail->ErrorInfo}";
            }
        } else {
            // Handle failure case
            echo "Error cancelling request: " . mysqli_error($conn);
        }
    } else {
        echo "User not found.";
    }
} else {
    echo "Application reference number is missing.";
}
?>
