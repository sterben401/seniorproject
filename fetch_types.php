<?php
// fetch_types.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');


$servername = "localhost"; 
$username = "project"; 
$password = "1234"; 
$dbname = "PROJECT"; 
$port = "3306";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง detec_history
$sql = "SELECT name FROM AttackGroup"; // ดึงประเภทที่ไม่ซ้ำ
$result = $conn->query($sql);

$types = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $types[] = $row['name'];
    }
}

// ปิดการเชื่อมต่อ
$conn->close();

// ส่งข้อมูลกลับเป็น JSON
echo json_encode($types);
?>

