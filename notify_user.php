<?php
// Include the database connection
include 'db_connection.php';

// Include PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

// Check if app_ref is provided
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['app_ref'])) {
    $app_ref = $_GET['app_ref'];

    // Retrieve the service booking details from the database
    $query = "SELECT * FROM service_bookings WHERE app_ref = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_ref);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();

        // Send notification email after retrieving the data
        sendNotificationEmail($service['name'], $service['email'], $service['need'], $service['status'], $service['status_description'], $service['expected_delivery'], $service['final_price'], $service['payment_status'], $service['status_number'], $app_ref);
        
        // Redirect to the dashboard with a success message
        header("Location: a_dashboard.php?success=Notification sent successfully.");
        exit();
    } else {
        // Redirect if service not found
        header("Location: a_dashboard.php?error=Service request not found.");
        exit();
    }
} else {
    // Redirect if no app_ref is provided
    header("Location: a_dashboard.php?error=Invalid request.");
    exit();
}

// Function to send the notification email
function sendNotificationEmail($name, $email, $need, $status, $status_description, $expected_delivery, $final_price, $payment_status, $status_number, $app_ref) {
    // Prepare email content using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'trine.devlop@gmail.com';  // Replace with your email
        $mail->Password = 'uase acra crqe vzmo';    // Replace with your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('trine.devlop@gmail.com', 'Vortex Trine Labs');
        $mail->addAddress($email, $name);

        $mail->addEmbeddedImage('assets/img/logo.png', 'logo_cid', 'logo.png');
        $mail->isHTML(true);
        $mail->Subject = "Service Booking Status Update - $app_ref";
        $mail->Body = "
            <html>
            <head>
                <title>Service Booking Status Update</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; color: #333; }
                    .container { background-color: #fff; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
                    .header { text-align: center; margin-bottom: 20px; }
                    .header img { width: 150px; margin-bottom: 10px; }
                    .content { line-height: 1.6; }
                    .content p { margin-bottom: 15px; }
                    .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #777; }
                    .footer i { color: #999; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <img src='cid:logo_cid' alt='Vortex Trine Labs Logo'>
                        <h2>Service Booking Status Update</h2>
                    </div>
                    <div class='content'>
                        <p>Dear <strong>$name</strong>,</p>
                        <p>Your service booking with the reference number <strong>$app_ref</strong> has been updated. Here are the updated details:</p>
                        <ul>
                            <li><strong>Status:</strong> $status</li>
                            <li><strong>Status Description:</strong> $status_description</li>
                            <li><strong>Expected Delivery:</strong> $expected_delivery</li>
                            <li><strong>Final Price:</strong> $final_price</li>
                            <li><strong>Payment Status:</strong> $payment_status</li>
                            <li><strong>Status Number:</strong> $status_number</li>
                        </ul>
                        <p>If you have any questions, feel free to reach out.</p>
                    </div>
                    <div class='footer'>
                        <p>Thank you for choosing Vortex Trine Labs. We look forward to serving you!</p>
                        <p><i>&copy; 2024 Vortex Trine Labs</i></p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>
