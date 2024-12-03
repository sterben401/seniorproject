<?php
header('Content-Type: application/json');

// กำหนดเส้นทางไปยังไฟล์ .class
$javaClassName = 'LogModsec5'; 
$classpath = '/home/test/Downloads/logmodSec/mysql-connector-j-9.0.0.jar:/home/test/Downloads/logmodSec'; 
$command = "java -cp $classpath $javaClassName"; 

$output = [];
$returnVar = 0;

// รันคำสั่งใน shell
exec($command . " 2>&1", $output, $returnVar);

// ตรวจสอบผลลัพธ์
if ($returnVar === 0) {
    echo json_encode(['success' => true, 'output' => implode("\n", $output)]);
} else {
    echo json_encode(['success' => false, 'error' => implode("\n", $output)]);
}
?>

