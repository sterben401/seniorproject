<?php
session_start();
require_once 'config/db.php';

// Determine user role based on session
if (isset($_SESSION['admin_login'])) {
    $role = 'admin';
    $user_id = $_SESSION['admin_login']; // Store admin ID for later use
} elseif (isset($_SESSION['user_login'])) {
    $role = 'user';
    $user_id = $_SESSION['user_login']; // Store user ID for later use
} else {
    // No one is logged in
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit();
}
$page_title = $role === 'admin' ? '(ADMIN) A Brute Force Attack Monitoring System' : '(USER) A Brute Force Attack Monitoring System';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title> <!-- Dynamically set title -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #A9A9A9, #FFD700); /* ไล่สีพื้นหลัง */
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #212529; /* สีพื้นหลังของ Navbar เป็นสีเข้ม */
            border-bottom: 3px solid #FFD700; /* ขอบล่างสีทอง */
        }

        .navbar-brand {
            color: #212121; /* สีข้อความของ Navbar */
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-brand .admin {
            color: #FFD700; /* สีทองเข้มสำหรับ (ADMIN) */
        }

        .navbar-brand .system {
            color: #212121; /* สีเทาเข้มสำหรับ A Brute Force Attack Monitoring System */
        }

        .navbar-nav .nav-link {
            color: #FFD700; /* สีข้อความลิงค์ใน Navbar */
            font-weight: bold;
            background-color: #343a40; /* สีพื้นหลังของลิงค์ใน Navbar */
            border-radius: 5px; /* มุมโค้งมน */
            padding: 10px 15px; /* ขนาดของปุ่ม */
            transition: background-color 0.3s; /* เปลี่ยนสีพื้นหลังเมื่อ Hover */
        }

        .navbar-nav .nav-link.active {
            color: #FFFFFF; /* สีข้อความลิงค์ที่เลือกใน Navbar */
            background-color: #FFD700; /* สีพื้นหลังของลิงค์ที่เลือก */
        }

        .navbar-nav .nav-link:hover {
            background-color: #FFD700; /* สีพื้นหลังเมื่อ Hover */
            color: #343a40; /* สีข้อความเมื่อ Hover */
        }

        .container {
            margin-top: 30px;
            background-color: #FFFFFF; /* สีพื้นหลังของการ์ด */
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* เงาของการ์ด */
        }

        .card {
            border-radius: 15px;
            border: 1px solid #DCDCDC; /* เส้นขอบการ์ดสีเทาอ่อน */
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {•••••••••
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-weight: bold;
            color: #FFD700; /* สีหัวข้อการ์ด */
        }

        .btn-lg {
            margin-top: 10px;
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-warning {
            background-color: #FFD700; /* สีพื้นหลังปุ่ม Warning */
        }

        .btn-warning:hover {
            background-color: #F5C300; /* สีพื้นหลังปุ่ม Warning เมื่อ Hover */
        }

        .btn-success {
            background-color: #A9A9A9; /* สีพื้นหลังปุ่ม Success */
        }

        .btn-success:hover {
            background-color: #6C6C6C; /* สีพื้นหลังปุ่ม Success เมื่อ Hover */
        }

        .btn-primary {
            background-color: #FFD700; /* สีพื้นหลังปุ่ม Primary */
        }

        .btn-primary:hover {
            background-color: #F5C300; /* สีพื้นหลังปุ่ม Primary เมื่อ Hover */
        }

        .text-primary {
            color: #FFD700 !important;
        }

        h3 {
            font-weight: bold;
        }

        .fa-icon {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand"><span class="admin">(USER)</span> <span class="system">A Brute Force Attack Monitoring System</span></a>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
               <a class="nav-item nav-link active" 
   href="<?php 
      if (isset($_SESSION['admin_login'])) {
          echo 'admin.php';  // Admin dashboard
      } elseif (isset($_SESSION['user_login'])) {
          echo 'homeuser.php';  // User dashboard
      } else {
          echo 'signin.php';  // Redirect to login if no session
      }
   ?>">
   <i class="fas fa-home fa-icon"></i> Home
</a> 
                <a class="nav-item nav-link" href="profile.php"><i class="fas fa-user fa-icon"></i>Profile</a>
                <a class="nav-item nav-link" href="logout.php"><i class="fas fa-sign-out-alt fa-icon"></i>Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <?php 
    session_start(); // Start the session
require_once 'config/db.php'; // Include your database connection

        if (isset($_SESSION['user_login'])) {
            $admin_id = $_SESSION['user_login'];
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    ?>
    <div class="text-center">
        <h3 class="system">Welcome User,<?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></h3>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4 mt-4">
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="system">เพิ่ม/ลบ Pattern</h5>
                    <a href="pattern.php" class="btn btn-primary btn-lg"><i class="fas fa-cogs fa-icon"></i> คลิ๊กที่นี่</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="system">ประวัติการโจมตี</h5>
                    <a href="history.php" class="btn btn-warning btn-lg"><i class="fas fa-history fa-icon"></i> คลิ๊กที่นี่</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="system">Run Pattern Detected</h5>
                    <a href="checkScript.php" class="btn btn-primary btn-lg"><i class="fas fa-cogs fa-icon"></i> คลิ๊กที่นี่</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq9k8w2Nf1m56w27gcsE7VVFVV8PzABnJ5RsiQ90Cn3z7y8u7v" crossorigin="anonymous"></script>
</body>
</html>

