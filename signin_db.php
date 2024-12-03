<?php 

session_start();
require_once 'config/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['signin'])) {
     $email = $_POST['email'];
    $password = $_POST['password'];
    $captcha = $_POST['g-recaptcha-response'] ?? '';
    $status = $_POST['status'];

    // ฟังก์ชันเพื่อตั้งค่าข้อความผิดพลาดและทำการ redirect
    function setErrorAndRedirect($message) {
        $_SESSION['error'] = $message;
        header("location: signin.php");
        exit;
    }

    // กำหนด RegEx สำหรับการตรวจสอบรหัสผ่าน
    $passwordRegex = '/^[^\/\*\-]*$/'; // ไม่อนุญาตให้มี /, *, -

    // ตรวจสอบข้อมูล
    if (empty($email)) {
        setErrorAndRedirect('กรุณากรอกอีเมล');
            $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setErrorAndRedirect('รูปแบบอีเมลไม่ถูกต้อง');
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
    } else if (empty($password)) {
        setErrorAndRedirect('กรุณากรอกรหัสผ่าน');
        
    } else if (strlen($password) > 20 || strlen($password) < 5) {
        setErrorAndRedirect('รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร');
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
    } else if (!preg_match($passwordRegex, $password)) {
        setErrorAndRedirect('รหัสผ่านไม่ควรมีตัวอักษรพิเศษ /, *, -');
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
    } else if (empty($captcha)) {
        setErrorAndRedirect('กรุณายืนยันว่าคุณไม่ใช่หุ่นยนต์');
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
    }
    // ตรวจสอบ reCAPTCHA
    $secretKey = "6Lf3efMpAAAAAGjpUOYkiYlyKAIzwR55JmQ5NjaX"; // เปลี่ยนเป็น Secret Key ของคุณ
    $responseKey = $captcha;
    $userIP = $_SERVER['REMOTE_ADDR'];

    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
    $response = file_get_contents($url);
    $responseKeys = json_decode($response, true);
if (intval($responseKeys["success"]) !== 1) {
    $_SESSION['error'] = "กรุณายืนยันว่าคุณไม่ใช่หุ่นยนต์!";
    header("location: signin.php");
    exit;
} else {
    // ดีบักข้อมูลที่ได้จาก reCAPTCHA
    error_log(print_r($responseKeys, true)); // บันทึกข้อมูลการตอบสนองใน log
}

 try {
    $check_data = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $check_data->bindParam(":email", $email);
    $check_data->execute();
    $row = $check_data->fetch(PDO::FETCH_ASSOC);

    if ($check_data->rowCount() > 0) {
        if ($row['status'] === 'pending') {
            setErrorAndRedirect('บัญชีของคุณอยู่ในสถานะรอการอนุมัติ');
            $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        } else if (password_verify($password, $row['password'])) {
            if ($row['urole'] == 'admin') {
                $_SESSION['admin_login'] = $row['id'];
                header("location: admin.php");
            } else {
                $_SESSION['user_login'] = $row['id'];
                header("location: homeuser.php");
            }
        } else {
            $_SESSION['error'] = 'รหัสผ่านผิด';
            $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
            header("location: signin.php");
        }
    } else {
        $_SESSION['error'] = "ไม่มีข้อมูลในระบบ";
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("location: signin.php");
    }

} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
}
?>
