<?php
function readLog() {
    $logFilePath = '/var/LogFile/PatternDetect/fileDetec-2024-09-21-04.log'; // ระบุพาธของไฟล์ log

    if (!file_exists($logFilePath)) {
        return ['error' => 'ไม่พบไฟล์ log'];
    }

    $fileContent = file_get_contents($logFilePath);
    $lines = explode("\n", $fileContent);
    
    $data = [];
    foreach ($lines as $line) {
        // ตรวจสอบรูปแบบของบรรทัด log
        if (preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}).*Type : (\w+).*Patterns : (.*)/', $line, $matches)) {
            $date = $matches[1];
            $type = $matches[2];
            $pattern = trim($matches[3]);

            // นับจำนวน pattern ซ้ำ
            if (!isset($data[$pattern])) {
                $data[$pattern] = ['date' => $date, 'type' => $type, 'count' => 1];
            } else {
                $data[$pattern]['count']++;
            }
        }
    }

    return array_values($data); // ส่งกลับข้อมูลทั้งหมด
}
?>

