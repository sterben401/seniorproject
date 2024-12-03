<?php
header('Content-Type: application/json');

// กำหนดเส้นทางไปยังไฟล์ .java และ .class
$javaFileName = '/home/test/Downloads/logmodSec/LogModsec3.java'; // ชื่อไฟล์ .java
$javaClassName = 'LogModsec3'; // ชื่อคลาส
$classpath = '/home/test/Downloads/logmodSec/mysql-connector-j-9.0.0.jar:/home/test/Downloads/logmodSec'; // เส้นทาง .class และ jar

$output = [];
$returnVar = 0;

// คอมไพล์ไฟล์ .java ก่อนด้วย javac
$compileCommand = "javac -cp $classpath $javaFileName";
exec($compileCommand . " 2>&1", $output, $returnVar);

if ($returnVar !== 0) {
    // ถ้าคอมไพล์ล้มเหลว
    echo json_encode(['success' => false, 'error' => 'Compilation failed', 'details' => implode("\n", $output)]);
    exit;
}

// ถ้าคอมไพล์สำเร็จ, รันคลาส Java ที่คอมไพล์แล้ว
$command = "java -cp $classpath $javaClassName";
$output = []; // เคลียร์ output ก่อนรันคำสั่งใหม่
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

