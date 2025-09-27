<?php 
      use PHPMailer\PHPMailer\PHPMailer;
      use PHPMailer\PHPMailer\Exception;
    function Email_sender($otp, $email, $subject = 'Your OTP Code'){

        require 'vendor/autoload.php';
        
        $mail = new PHPMailer(true);
        
        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'vishalvarma0987654321+@gmail.com';
            $mail->Password   = 'eiawzpinzrjpvicw'; // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            
            // Recipients
            $mail->setFrom('vishalvarma0987654321+@gmail.com', 'Sunrise Yoga');
            $mail->addAddress($email); // user email
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = "<h2>Sunrise Yoga - OTP Verification</h2>
                              <p>Your OTP is <b style='font-size: 24px; color: #4a7c59;'>$otp</b></p>
                              <p>This OTP will expire in 5 minutes.</p>
                              <p>If you didn't request this, please ignore this email.</p>
                              <br><p>Best regards,<br>Sunrise Yoga Team</p>";
            $mail->AltBody = "Sunrise Yoga - Your OTP is $otp. It will expire in 5 minutes.";
            
            $mail->send();
            error_log("OTP email sent successfully to $email with subject: $subject");
            return true;
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
        
    } 
?>