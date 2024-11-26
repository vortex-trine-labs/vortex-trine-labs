<?php
// Include necessary PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer using Composer's autoloader or include manually
require 'vendor/autoload.php'; // If using Composer

// Include database connection file
include('db_connection.php'); 

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $organization = $_POST['organization'];
    $need = $_POST['need'];
    $description = $_POST['description'];
    $referral = $_POST['referral'];
    $deadline = $_POST['deadline'];
    $budget = $_POST['budget'];

    // Generate Application Reference Number (e.g., VTL240001, VTL240002)
    $last_id_query = "SELECT MAX(id) AS last_id FROM service_bookings";
    $result = mysqli_query($conn, $last_id_query);
    $row = mysqli_fetch_assoc($result);
    $last_id = $row['last_id'] + 1; // Increment to generate next ID
    $app_ref = "VTL24" . str_pad($last_id, 4, '0', STR_PAD_LEFT); // Format as VTL240001, VTL240002, etc.

    // Insert form data into database
    $sql = "INSERT INTO service_bookings (name, email, number, organization, need, description, referral, deadline, budget, app_ref)
            VALUES ('$name', '$email', '$number', '$organization', '$need', '$description', '$referral', '$deadline', '$budget', '$app_ref')";

    if (mysqli_query($conn, $sql)) {

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                         // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                     // Enable SMTP authentication
            $mail->Username   = 'trine.devlop@gmail.com';                   // SMTP username
            $mail->Password   = 'uase acra crqe vzmo';                    // App Password (replace with your generated app password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption
            $mail->Port       = 587;                                      // TCP port to connect to

            // Recipients
            $mail->setFrom('trine.devlop@gmail.com', 'Vortex Trine Labs');
            $mail->addAddress($email, $name);                             // Add a recipient

            // Content
            $mail->isHTML(true);                                          // Set email format to HTML
            $mail->Subject = "Service Booking Confirmation - $app_ref";
            // Add the logo image as an embedded attachment
$mail->addEmbeddedImage('assets/img/logo.png', 'logo_cid', 'logo.png');

// In the email body, reference the image using the Content-ID
$mail->Body    = "
    <html>
        <head>
            <title>Service Booking Confirmation</title>
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
                    <h2>Service Booking Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Dear <strong>$name</strong>,</p>
                    <p>We are thrilled to inform you that your request for <strong>$need</strong> has been successfully registered with the application reference number <strong>$app_ref</strong>.</p>
                    <p>Your request is important to us, and our team will review it promptly. You will receive an update once the application status changes. You can also track your request anytime on our portal.</p>
                    <p><strong>Thank you for choosing Vortex Trine Labs!</strong></p>
                    <p>We're excited to assist you with your needs and ensure the best possible service.</p>
                    <p>If you have any further questions, feel free to contact us. We are here to help!</p>
                </div>
                <div class='footer'>
                    <p>Regards, <br><strong>Vortex Trine Labs, Coimbatore</strong></p>
                    <p><i>This is a system-generated message. Please do not reply unless you have a query.</i></p>
                </div>
            </div>
        </body>
    </html>
";

            // Send email
            if ($mail->send()) {
                // Success message with redirect
                echo "<script>
                        alert('Your request has been successfully submitted. Please check your email for the confirmation.');
                        window.location.href = 'index.html';
                      </script>";
            } else {
                echo "Error in sending email. Please try again.";
            }
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        // Redirect to error page in case of a database error
        header("Location: error.html?error=database");
        exit();
    }
} else {
    // Redirect to error page if accessed without submitting the form
    header("Location: error.html?error=invalid_access");
    exit();
}
?>
