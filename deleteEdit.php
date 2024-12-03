<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // ลบข้อมูลผู้ใช้ตาม ID
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = 'ลบข้อมูลผู้ใช้สำเร็จ!';
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการลบข้อมูลผู้ใช้';
        }

        header('location: edit.php');
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = 'การเชื่อมต่อผิดพลาด: ' . $e->getMessage();
        header('location: edit.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'ไม่มี ID ผู้ใช้ที่ระบุ';
    header('location: edit.php');
    exit();
}
?>
