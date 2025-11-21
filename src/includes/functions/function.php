 <?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once __DIR__ . '/../../../vendor/autoload.php';

    function getPackageDetails(){
        global $conn;

        if (!isset($_GET['packageId']) || empty($_GET['packageId'])) {
            http_response_code(404);
            echo json_encode(['error' => 'PackageID is required']);
            exit;
        }

        $packageId = $_GET['packageId'];

        $inclusion = $conn->prepare("SELECT * FROM inclusion WHERE packageID = ?");
        $inclusion->bind_param('s', $packageId);
        $inclusion->execute();
        $inc = $inclusion->get_result();


        $addons = $conn->prepare("SELECT * FROM addons WHERE packageID = ?");
        $addons->bind_param('s', $packageId);
        $addons->execute();
        $ad = $addons->get_result();


        $data = [
            "inclusions" => [],
            "addons" => []

        ];

        while ($inclusions = $inc->fetch_assoc()) {
            $data['inclusions'][] = $inclusions['Description'];
        }

        while ($add = $ad->fetch_assoc()) {
            $data['addons'][] = [
                'Description' => $add['Description'],
                'Price' => $add['Price']
            ];
        }

        $inclusion->close();
        $addons->close();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    function sendVerificationEmailWithCode($email, $code){
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['SMTP_PORT'];

            // Recipients
            $mail->setFrom($_ENV['SMTP_USERNAME'], 'Aperture');
            $mail->addAddress($email);

            // Content with luxury design matching the brand
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email - Aperture';
            $mail->Body    = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
                <!-- Header with gold accent -->
                <div style='background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); padding: 40px 30px; text-align: center;'>
                    <h1 style='font-family: \"Playfair Display\", Georgia, serif; font-size: 2.5rem; font-weight: 300; letter-spacing: 3px; color: #d4af37; margin: 0;'>APERTURE</h1>
                    <p style='color: #ffffff; font-size: 0.9rem; margin-top: 10px; opacity: 0.8;'>Photography & Videography</p>
                </div>

                <!-- Main Content -->
                <div style='padding: 50px 40px; background-color: #f8f9fa;'>
                    <h2 style='font-size: 1.5rem; font-weight: 300; color: #1a1a1a; margin-bottom: 20px;'>Welcome to Aperture!</h2>
                    <p style='font-size: 1rem; color: #4a4a4a; line-height: 1.6; margin-bottom: 30px;'>Thank you for registering. Please verify your email address using the code below:</p>

                    <!-- Verification Code Box -->
                    <div style='background: #ffffff; border: 2px solid #d4af37; border-radius: 8px; padding: 30px; text-align: center; margin: 30px 0;'>
                        <p style='font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px; color: #999; margin-bottom: 15px;'>Your Verification Code</p>
                        <div style='font-family: \"Courier New\", monospace; font-size: 2rem; font-weight: 600; letter-spacing: 8px; color: #d4af37; margin: 20px 0;'>$code</div>
                        <p style='font-size: 0.8rem; color: #666; margin-top: 15px;'>This code will expire in 5 minutes</p>
                    </div>

                    <p style='font-size: 0.9rem; color: #4a4a4a; line-height: 1.6; margin-top: 30px;'>Enter this code on the verification page to complete your registration and start booking our services.</p>

                    <!-- Security Notice -->
                    <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 30px; border-radius: 4px;'>
                        <p style='font-size: 0.85rem; color: #856404; margin: 0; line-height: 1.5;'><strong>Security Notice:</strong> If you didn't create an account with Aperture, please ignore this email. Never share this code with anyone.</p>
                    </div>
                </div>

                <!-- Footer -->
                <div style='background-color: #1a1a1a; padding: 30px; text-align: center;'>
                    <p style='font-size: 0.8rem; color: #999; margin: 0; line-height: 1.6;'>
                        This is an automated message from Aperture.<br>
                        For assistance, please contact us through our website.
                    </p>
                    <p style='font-size: 0.75rem; color: #666; margin-top: 15px;'>
                        © 2025 Aperture. All rights reserved.
                    </p>
                </div>
            </div>
        ";

            $mail->AltBody = "Welcome to Aperture!\n\nYour verification code is: $code\n\nThis code will expire in 5 minutes.\n\nIf you didn't create an account, please ignore this email.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
            return false;
        }
    }


    function saveUserProfile($userId, $firstName, $lastName, $fullName, $contact) {
        global $conn;

        $query = $conn->prepare("UPDATE users SET FirstName = ?, LastName = ?, FullName = ?, contactNo = ?, profileCompleted = true WHERE userID = ?");
        $query->bind_param('sssss', $firstName, $lastName, $fullName, $contact, $userId);
     
        if($query->execute()){
            $query->close();
            return true;
        } 
        $query->close();
        return false;
    }

    function sendForgotPasswordWithCode($email, $code){
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['SMTP_PORT'];

            // Recipients
            $mail->setFrom($_ENV['SMTP_USERNAME'], 'Aperture');
            $mail->addAddress($email);

            // Content with luxury design matching the brand
            $mail->isHTML(true);
            $mail->Subject = 'Forgot password - Aperture';
            $mail->Body    = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
                <!-- Header with gold accent -->
                <div style='background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); padding: 40px 30px; text-align: center;'>
                    <h1 style='font-family: \"Playfair Display\", Georgia, serif; font-size: 2.5rem; font-weight: 300; letter-spacing: 3px; color: #d4af37; margin: 0;'>APERTURE</h1>
                    <p style='color: #ffffff; font-size: 0.9rem; margin-top: 10px; opacity: 0.8;'>Photography & Videography</p>
                </div>

                <!-- Main Content -->
                <div style='padding: 50px 40px; background-color: #f8f9fa;'>
                    <h2 style='font-size: 1.5rem; font-weight: 300; color: #1a1a1a; margin-bottom: 20px;'>Forgot Password</h2>
                    <p style='font-size: 1rem; color: #4a4a4a; line-height: 1.6; margin-bottom: 30px;'>Please verify your email address using the code below:</p>

                    <!-- Verification Code Box -->
                    <div style='background: #ffffff; border: 2px solid #d4af37; border-radius: 8px; padding: 30px; text-align: center; margin: 30px 0;'>
                        <p style='font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px; color: #999; margin-bottom: 15px;'>Your Verification Code</p>
                        <div style='font-family: \"Courier New\", monospace; font-size: 2rem; font-weight: 600; letter-spacing: 8px; color: #d4af37; margin: 20px 0;'>$code</div>
                        <p style='font-size: 0.8rem; color: #666; margin-top: 15px;'>This code will expire in 5 minutes</p>
                    </div>

                    <p style='font-size: 0.9rem; color: #4a4a4a; line-height: 1.6; margin-top: 30px;'>Enter this code on the verification page to confirm your account and create a new password.</p>

                    <!-- Security Notice -->
                    <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 30px; border-radius: 4px;'>
                        <p style='font-size: 0.85rem; color: #856404; margin: 0; line-height: 1.5;'><strong>Security Notice:</strong> If you didn't request this code, please ignore this email. Never share this code with anyone.</p>
                    </div>
                </div>

                <!-- Footer -->
                <div style='background-color: #1a1a1a; padding: 30px; text-align: center;'>
                    <p style='font-size: 0.8rem; color: #999; margin: 0; line-height: 1.6;'>
                        This is an automated message from Aperture.<br>
                        For assistance, please contact us through our website.
                    </p>
                    <p style='font-size: 0.75rem; color: #666; margin-top: 15px;'>
                        © 2025 Aperture. All rights reserved.
                    </p>
                </div>
            </div>
        ";

            $mail->AltBody = "Welcome to Aperture!\n\nYour verification code is: $code\n\nThis code will expire in 5 minutes.\n\nIf you didn't create an account, please ignore this email.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
