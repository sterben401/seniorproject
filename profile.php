<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit();
}

try {
    $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_SESSION['admin_login'])) {
        $admin_id = $_SESSION['admin_login'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .card-header {
            background-color: #FFD700;
            color: #212121;
            font-weight: bold;
            font-size: 20px;
            border-bottom: 2px solid #212121;
            border-radius: 15px 15px 0 0;
        }
        .card-body {
            padding: 30px;
        }
        .btn-primary {
            background-color: #FFD700;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: #212121;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #F5C300;
        }
        .text-primary {
            color: #FFD700 !important;
        }
        .card-text strong {
            color: #212121;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-primary">Profile</h3>
        <div class="card">
            <div class="card-header">
                ข้อมูลผู้ใช้
            </div>
            <div class="card-body">
                <p class="card-text"><strong>ชื่อ: </strong><?php echo htmlspecialchars($row['firstname']); ?></p>
                <p class="card-text"><strong>นามสกุล: </strong><?php echo htmlspecialchars($row['lastname']); ?></p>
                <p class="card-text"><strong>อีเมล: </strong><?php echo htmlspecialchars($row['email']); ?></p>
                <!-- เพิ่มข้อมูลอื่นๆตามที่ต้องการ -->
                <a href="admin.php" class="btn btn-primary">กลับไปหน้าหลัก</a>
            </div>
        </div>
    </div>
</body>
</html>

