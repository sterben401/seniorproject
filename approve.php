<?php
session_start();
require_once 'config/db.php';

// ตรวจสอบการเข้าสู่ระบบของ admin
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit();
}

try {
    // เชื่อมต่อกับฐานข้อมูล
    $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ตรวจสอบว่ามีการส่ง id ของผู้ใช้หรือไม่
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // อัปเดตสถานะของผู้ใช้เป็น active
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $id); // ใช้ id ที่เป็นตัวเลข (integer)

        if ($stmt->execute()) {
            $_SESSION['success'] = 'ผู้ใช้ได้รับการอนุมัติแล้ว!';
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอนุมัติผู้ใช้!';
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้!';
    }

    // เปลี่ยนเส้นทางกลับไปที่หน้า edit.php
    header('location: approvePage.php');
    exit();

} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

