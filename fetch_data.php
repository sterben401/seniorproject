<?php
require_once 'config/db.php';

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // ตรวจสอบว่า $date มีรูปแบบ "YYYY-MM-DD" หรือไม่
    if (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
        $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $orange_sql = "SELECT * FROM detec_history WHERE Status = 'Orange' AND Date_detec >= '$date'";
        $red_sql = "SELECT * FROM detec_history WHERE Status = 'Red' AND Date_detec >= '$date'";

        $orange_result = $conn->query($orange_sql);
        $red_result = $conn->query($red_sql);

        $data = [
            'orange' => $orange_result->fetch_all(MYSQLI_ASSOC),
            'red' => $red_result->fetch_all(MYSQLI_ASSOC)
        ];

        echo json_encode($data);
    } else {
        echo json_encode([]); // วันที่ไม่ถูกต้อง
    }
} else {
    echo json_encode([]);
}
?>

