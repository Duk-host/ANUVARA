<?php
// Email configuration
$recipient_email = "emersonelgin1111@gmail.com";
$site_name = "ProjectHub";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $category = isset($_POST['category']) ? sanitize_input($_POST['category']) : '';
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($category) || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    // Create email subject
    $subject = "New Project Inquiry from " . $name . " - " . ucfirst($category);
    
    // Create email body
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; }
            .header { background-color: #6366f1; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
            .content { background-color: white; padding: 20px; }
            .field { margin: 15px 0; }
            .label { font-weight: bold; color: #6366f1; }
            .footer { background-color: #f0f0f0; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 8px 8px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Project Inquiry</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Name:</span><br>
                    " . htmlspecialchars($name) . "
                </div>
                <div class='field'>
                    <span class='label'>Email:</span><br>
                    " . htmlspecialchars($email) . "
                </div>
                <div class='field'>
                    <span class='label'>Phone:</span><br>
                    " . (!empty($phone) ? htmlspecialchars($phone) : 'Not provided') . "
                </div>
                <div class='field'>
                    <span class='label'>Category:</span><br>
                    " . htmlspecialchars(ucfirst($category)) . "
                </div>
                <div class='field'>
                    <span class='label'>Message:</span><br>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
                <hr>
                <p><strong>Reply to:</strong> " . htmlspecialchars($email) . "</p>
            </div>
            <div class='footer'>
                <p>This email was sent from ProjectHub website</p>
                <p>" . date('Y-m-d H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Set email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    
    // Send email
    $mail_sent = mail($recipient_email, $subject, $email_body, $headers);
    
    if ($mail_sent) {
        // Also send confirmation email to user
        $user_subject = "We received your inquiry - ProjectHub";
        $user_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; }
                .header { background-color: #6366f1; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
                .content { background-color: white; padding: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Thank You for Your Inquiry!</h2>
                </div>
                <div class='content'>
                    <p>Hi " . htmlspecialchars($name) . ",</p>
                    <p>We have received your inquiry and appreciate your interest in ProjectHub.</p>
                    <p>Our team will review your message and get back to you as soon as possible.</p>
                    <p><strong>Your contact details:</strong></p>
                    <p>Email: " . htmlspecialchars($email) . "<br>
                    Phone: " . (!empty($phone) ? htmlspecialchars($phone) : 'Not provided') . "</p>
                    <p>Best regards,<br><strong>ProjectHub Team</strong></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $user_headers = "MIME-Version: 1.0" . "\r\n";
        $user_headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $user_headers .= "From: " . $recipient_email . "\r\n";
        
        mail($email, $user_subject, $user_body, $user_headers);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Message sent successfully! Check your email for confirmation.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
    }
    
    exit;
}

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// If not POST request, return error
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>
