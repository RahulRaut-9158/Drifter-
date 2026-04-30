<?php
/**
 * Email Service
 * Handles all email notifications
 */
class EmailService {
    private static $fromEmail;
    private static $fromName = 'Drifter';

    public static function init($fromEmail, $fromName = 'Drifter') {
        self::$fromEmail = $fromEmail;
        self::$fromName = $fromName;
    }

    public static function send($to, $subject, $htmlBody, $textBody = null) {
        if (!self::$fromEmail) {
            Logger::warning("Email service not initialized");
            return false;
        }

        $textBody = $textBody ?? strip_tags($htmlBody);
        $headers = self::buildHeaders();

        try {
            $result = mail($to, $subject, $htmlBody, $headers);
            
            if ($result) {
                Logger::info("Email sent", ['to' => $to, 'subject' => $subject]);
            } else {
                Logger::error("Email failed to send", ['to' => $to, 'subject' => $subject]);
            }

            return $result;
        } catch (\Exception $e) {
            Logger::exception($e);
            return false;
        }
    }

    public static function bookingConfirmation($to, $userName, $bookingDetails) {
        $subject = "Booking Confirmation - Drifter";
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Inter, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2563EB, #06b6d4); color: white; padding: 20px; border-radius: 8px; }
                .details { margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .btn { display: inline-block; background: #2563EB; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✓ Booking Confirmed!</h1>
                </div>
                <p>Hi $userName,</p>
                <p>Your booking has been confirmed. Here are your details:</p>
                <div class='details'>
                    <div class='detail-row'><strong>Booking ID:</strong> <span>" . ($bookingDetails['id'] ?? 'N/A') . "</span></div>
                    <div class='detail-row'><strong>Pickup:</strong> <span>" . ($bookingDetails['pickup_location'] ?? 'N/A') . "</span></div>
                    <div class='detail-row'><strong>Dropoff:</strong> <span>" . ($bookingDetails['drop_location'] ?? 'N/A') . "</span></div>
                    <div class='detail-row'><strong>Date:</strong> <span>" . ($bookingDetails['date'] ?? 'N/A') . "</span></div>
                    <div class='detail-row'><strong>Total Cost:</strong> <span>₹" . ($bookingDetails['total_cost'] ?? '0') . "</span></div>
                </div>
                <p>You can track your booking in your dashboard.</p>
                <a href='" . BASE . "/front/dashboard_customer.php' class='btn'>View Booking</a>
                <p style='margin-top: 30px; color: #999; font-size: 12px;'>This is an automated email, please do not reply.</p>
            </div>
        </body>
        </html>
        ";

        return self::send($to, $subject, $htmlBody);
    }

    public static function bookingCancellation($to, $userName, $bookingDetails) {
        $subject = "Booking Cancelled - Drifter";
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Inter, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #c0392b; color: white; padding: 20px; border-radius: 8px; }
                .details { margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Booking Cancelled</h1>
                </div>
                <p>Hi $userName,</p>
                <p>Your booking has been cancelled.</p>
                <div class='details'>
                    <div class='detail-row'><strong>Booking ID:</strong> <span>" . ($bookingDetails['id'] ?? 'N/A') . "</span></div>
                    <div class='detail-row'><strong>Reason:</strong> <span>" . ($bookingDetails['cancel_reason'] ?? 'No reason provided') . "</span></div>
                </div>
                <p>If you have any questions, please contact our support team.</p>
                <p style='margin-top: 30px; color: #999; font-size: 12px;'>This is an automated email, please do not reply.</p>
            </div>
        </body>
        </html>
        ";

        return self::send($to, $subject, $htmlBody);
    }

    public static function welcomeEmail($to, $userName, $role) {
        $subject = "Welcome to Drifter!";
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Inter, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2563EB, #06b6d4); color: white; padding: 20px; border-radius: 8px; }
                .btn { display: inline-block; background: #2563EB; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to Drifter!</h1>
                </div>
                <p>Hi $userName,</p>
                <p>Thank you for signing up as a <strong>" . ucfirst($role) . "</strong>. We're excited to have you on board!</p>
                <p>Get started by logging into your dashboard and exploring all the features Drifter has to offer.</p>
                <a href='" . BASE . "/front/login.php' class='btn'>Go to Dashboard</a>
                <p style='margin-top: 30px; color: #999; font-size: 12px;'>This is an automated email, please do not reply.</p>
            </div>
        </body>
        </html>
        ";

        return self::send($to, $subject, $htmlBody);
    }

    private static function buildHeaders() {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
        $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
        return $headers;
    }
}
?>
