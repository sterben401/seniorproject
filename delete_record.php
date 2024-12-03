<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json'); // Ensure the response is JSON

if (!isset($_SESSION['admin_login'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ!']);
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'การเชื่อมต่อล้มเหลว']);
            exit();
        }

        // ลบข้อมูลจากตารางประวัติ (history) ตาม ID
        $stmt = $conn->prepare("DELETE FROM detec_history WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'ลบข้อมูลประวัติสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลประวัติ']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่มี ID ประวัติที่ระบุ']);
}
?>
