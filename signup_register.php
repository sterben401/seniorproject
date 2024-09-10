<?php
session_start();
require_once 'config/db.php';

if (isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $c_password = $_POST['c_password'];
    $phone = $_POST['phone'];
    $description = $_POST['description'];
    $urole = 'user';

    // Verify reCAPTCHA
    $recaptcha_secret = "6Lf3efMpAAAAAGjpUOYkiYlyKAIzwR55JmQ5NjaX";
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    $recaptcha = file_get_contents($recaptcha_url . "?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
    $recaptcha_result = json_decode($recaptcha, true);

    if (!$recaptcha_result['success']) {
        $_SESSION['error'] = 'กรุณายืนยัน reCAPTCHA';
        header("location: register.php");
        exit();
    }

    // Your existing validation and processing code
    if (empty($firstname)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อ';
        header("location: register.php");
    } else if (empty($lastname)) {
        $_SESSION['error'] = 'กรุณากรอกนามสกุล';
        header("location: register.php");
    } else if (empty($email)) {
        $_SESSION['error'] = 'กรุณากรอกอีเมล';
        header("location: register.php");
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
        header("location: register.php");
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
        header("location: register.php");
    } else if (strlen($password) > 20 || strlen($password) < 6) {
        $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 6 ถึง 20 ตัวอักษร';
        header("location: register.php");
    } else if (empty($c_password)) {
        $_SESSION['error'] = 'กรุณายืนยันรหัสผ่าน';
        header("location: register.php");
    } else if ($password != $c_password) {
        $_SESSION['error'] = 'รหัสผ่านไม่ตรงกัน';
        header("location: register.php");
    } else if (empty($phone)) {
        $_SESSION['error'] = 'กรุณากรอกเบอร์โทรศัพท์';
        header("location: register.php");
    } else {
        try {
            $check_email = $conn->prepare("SELECT email FROM users WHERE email = :email");
            $check_email->bindParam(":email", $email);
            $check_email->execute();
            $row = $check_email->fetch(PDO::FETCH_ASSOC);

            if ($row['email'] == $email) {
                $_SESSION['warning'] = "มีอีเมลนี้อยู่ในระบบแล้ว <a href='signin.php'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                header("location: register.php");
            } else if (!isset($_SESSION['error'])) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users(firstname, lastname, email, password, phone, description, urole) 
                                        VALUES(:firstname, :lastname, :email, :password, :phone, :description, :urole)");
                $stmt->bindParam(":firstname", $firstname);
                $stmt->bindParam(":lastname", $lastname);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $passwordHash);
                $stmt->bindParam(":urole", $urole);
                $stmt->bindParam(":phone", $phone);
                $stmt->bindParam(":description", $description);
                $stmt->execute();
                $_SESSION['success'] = "สมัครสมาชิกเรียบร้อยแล้ว!";
                header("location: register.php");
            } else {
                $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                header("location: register.php");
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>

