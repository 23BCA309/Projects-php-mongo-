    <?php
    function otp_Generator(){
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $length = 6;
        $otp = str_pad(random_int(0, 10**$length - 1), $length, '0', STR_PAD_LEFT);
        $secret = getenv('SECRET_KEY') ?: 'default_secret_key';
        $hash = hash_hmac('sha256', $otp, $secret);

        // Store OTP and hash in session for verification
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_hash'] = $hash;

        error_log("OTP Generated: $otp, Hash: $hash");
        return array($otp,$hash);
    }

    function otp_checker($user_otp){
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        error_log("Checking OTP: $user_otp");
        error_log("Session OTP Hash: " . ($_SESSION['otp_hash'] ?? 'NOT SET'));
        error_log("Session data: " . print_r($_SESSION, true));

        if (isset($_SESSION['otp_hash'])) {
            $secret = getenv('SECRET_KEY') ?: 'default_secret_key';
            $user_hash = hash_hmac('sha256', $user_otp, $secret);
            $isValid = hash_equals($_SESSION['otp_hash'], $user_hash);
            error_log("OTP Check Result: $isValid");
            return $isValid;
        }

        error_log("No OTP hash found in session");
        return false;
    }
?>
    
