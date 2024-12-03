<?php
header('Content-Type: application/json');

// กำหนดเส้นทางไปยังไฟล์ .class โดยไม่ต้องระบุเส้นทางเต็ม
$javaClassName = 'LogModsec3'; // ชื่อคลาส
$classpath = '/home/test/Downloads/logmodSec/mysql-connector-j-9.0.0.jar:/home/test/Downloads/logmodSec'; // เพิ่มเส้นทางที่มี LogModsec3.class
$command = "java -cp $classpath $javaClassName"; // ใช้ชื่อคลาสที่ถูกต้อง

$output = [];
$returnVar = 0;

// รันคำสั่งใน shell
exec($command . " 2>&1", $output, $returnVar);

// ตรวจสอบผลลัพธ์
if ($returnVar === 0) {
    // ถ้าสำเร็จ
    echo json_encode(['success' => true, 'output' => implode("\n", $output)]);
} else {
    // ถ้าล้มเหลว
    echo json_encode(['success' => false, 'error' => implode("\n", $output)]);
}
?>

