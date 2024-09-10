<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // สร้างคำสั่ง SQL เพื่อลบข้อมูลทั้งหมดจากตาราง detec_history
    $delete_sql = "DELETE FROM detec_history";

    if ($conn->query($delete_sql) === TRUE) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'error' => $conn->error];
    }

    echo json_encode($response);
}
?>

