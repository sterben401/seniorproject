<?php
// ปิด error reporting เพื่อไม่ให้มี warning หรือ notice ออกมาก่อนส่งข้อมูล JSON
error_reporting(0);

$servername = "localhost"; 
$username = "project"; 
$password = "1234"; 
$dbname = "PROJECT"; 
$port = "3306";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าช่วงวันที่
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

// ตรวจสอบให้แน่ใจว่า endDate ใช้ 23:59:59
$endDate .= ' 23:59:59';

// ตรวจสอบรูปแบบวันที่ที่ได้รับ
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2} 23:59:59$/', $endDate)) {
    die("Invalid date format");
}

// ตั้งค่าโซนเวลา
date_default_timezone_set('Asia/Bangkok');

// เตรียมคำสั่ง SQL ป้องกัน SQL Injection
$stmt = $conn->prepare("SELECT * FROM detec_history WHERE date_detec BETWEEN ? AND ? AND status IN ('ORANGE', 'RED')");
$stmt->bind_param("ss", $startDate, $endDate);

// รันคำสั่ง SQL
$stmt->execute();
$result = $stmt->get_result();

$data = array();
if ($result->num_rows > 0) {
    // ดึงข้อมูลแต่ละแถว
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$stmt->close();
$conn->close();

// ส่งข้อมูลกลับเป็น JSON
header('Content-Type: application/json');
echo json_encode($data);
?>

