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

    function sendVerificationEmail($email, $token){
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

            // Content
            $verificationLink = $_ENV['APP_URL'] . "/src/verify.php?token=$token";

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email - Aperture';
            $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #e9eef6; border-radius: .5rem;'>
                <h2>Welcome to Aperture!</h2>
                <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
                <a href='$verificationLink' style='display: inline-block; background-color: #212529; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0;'>Verify Email</a>
                <p>Or copy this link: <a href='$verificationLink'>$verificationLink</a></p>
                <p>This link will expire in 1 hour.</p>
                <p><small>If you didn't create an account, please ignore this email.</small></p>
            </div>
        ";

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
