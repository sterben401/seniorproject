<?php
session_start();
include 'config/db.php'; // Include your database configuration file
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $status = 'pending';
    $urole = 'user';
    $token = '';
    $confirm_password = $_POST['confirm_password']; // รับค่าการยืนยันรหัสผ่าน

    // ตรวจสอบความถูกต้องของข้อมูล
    if (!preg_match("/^[a-zA-Z\s]+$/", $firstname)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อด้วยตัวอักษรเท่านั้น';
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("Location: signup.php");
        exit();
    }
    if (!preg_match("/^[a-zA-Z\s]+$/", $lastname)) {
        $_SESSION['error'] = 'กรุณากรอกนามสกุลด้วยตัวอักษรเท่านั้น';
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("Location: signup.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'กรุณากรอกอีเมลในรูปแบบที่ถูกต้อง';
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("Location: signup.php");
        exit();
    }
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน';
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("Location: signup.php");
        exit();
    }
    if (!preg_match("/^\d{10}$/", $phone)) {
        $_SESSION['error'] = 'กรุณากรอกหมายเลขโทรศัพท์ให้ครบ 10 หลัก';
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("Location: signup.php");
        exit();
    }
    
    
    // ตรวจสอบว่า reCAPTCHA ส่งค่ามาหรือไม่
    if (isset($_POST['g-recaptcha-response'])) {
        $captcha = $_POST['g-recaptcha-response'];
    } else {
        $captcha = '';
    }

    // ตรวจสอบว่า reCAPTCHA ไม่ว่าง
    if (empty($captcha)) {
        $_SESSION['error'] = 'กรุณายืนยันว่าคุณไม่ใช่หุ่นยนต์';
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("Location: signup.php");
        exit();
    }

    // ตรวจสอบ reCAPTCHA กับ Google
    $secretKey = "6Lf3efMpAAAAAGjpUOYkiYlyKAIzwR55JmQ5NjaX"; // นี่คือ Secret Key ของคุณจาก Google
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $captcha;
    $response = file_get_contents($url);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        $_SESSION['error'] = 'การตรวจสอบ reCAPTCHA ล้มเหลว';
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
        header("Location: signup.php");
        exit();
    } else {
        // ตรวจสอบว่าผู้ใช้มีอยู่แล้วหรือไม่
        $checkUserQuery = "SELECT * FROM users WHERE email = :email";
        $checkUserStmt = $conn->prepare($checkUserQuery);
        $checkUserStmt->execute(['email' => $email]);

        if ($checkUserStmt->rowCount() > 0) {
            $_SESSION['error'] = "ผู้ใช้มีอยู่แล้ว!";
            $_SESSION['form_data'] = $_POST; // เก็บข้อมูลฟอร์มในเซสชัน
            header("Location: signup.php");
            exit();
        } else {
            // ส่งข้อมูลไปยัง API เพื่อทำการสมัครสมาชิก
            $url = 'http://' . getLocalIP() . ':3023/routes/register';
            $data = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => $password,
                'urole' => $urole,
                'phone' => $phone,
                'description' => $description,
                'token' => $token,
                'status' => $status
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 201) {
                $_SESSION['success'] = "สมัครสมาชิกสำเร็จ! กรุณารอการอนุมัติ";
                header("Location: signin.php");
                exit();
            } else {
                $responseData = json_decode($response, true);
                $_SESSION['error'] = $responseData['message'] ?? 'เกิดข้อผิดพลาด';
                header("Location: signin.php");
                exit();
            }
        }
    }
}

// ฟังก์ชันเพื่อดึงที่อยู่ IP
function getLocalIP() {
    // ใช้คำสั่ง hostname -I เพื่อดึง IP Address
    $output = shell_exec('hostname -I');
    
    // แยก IP Address ออกจาก string ที่ได้
    $ipArray = explode(' ', trim($output));
    
    // ส่งคืน IP Address แรกในกรณีที่มีหลายค่า
    return $ipArray[0] ?? null;
}

// เรียกใช้ฟังก์ชันเพื่อดึงที่อยู่ IP
$localIP = getLocalIP();
?>

