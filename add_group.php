<?php
session_start();
require_once 'config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Add new group

// Check if admin or user is logged in
if (isset($_SESSION['admin_login'])) {
    // Admin is logged in
    $role = 'admin';
} elseif (isset($_SESSION['user_login'])) {
    // User is logged in
    $role = 'user';
} else {
    // No one is logged in
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit();
}

if (isset($_POST['add_group'])) {
    $group_name = $_POST['group_name'];
    $group_description = $_POST['group_description'];
    $threshold = $_POST['threshold']; // Get the Threshold value

    // ตรวจสอบค่า Threshold ให้อยู่ในช่วง 1-1000
    if (!is_numeric($threshold) || $threshold < 1 || $threshold > 1000) {
        $_SESSION['error'] = "Threshold ต้องเป็นตัวเลขระหว่าง 1 ถึง 1000!";
        header("Location: add_group.php");
        exit();
    }
    

    // ตรวจสอบว่าชื่อกลุ่มมีอยู่ในฐานข้อมูลหรือไม่
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM AttackGroup WHERE name = ?");
    $stmt_check->execute([$group_name]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        $_SESSION['error'] = "ชื่อกลุ่มนี้มีอยู่แล้ว!";
        header("Location: add_group.php");
        exit();
    }
     if (preg_match('/[^a-zA-Z0-9ก-ฮาเ-์]/u', $group_name)) {
        $_SESSION['error'] = "ชื่อกลุ่มไม่ควรมีอักขระพิเศษ!";
        header("Location: add_group.php");
        exit();
    }


    // Prepare the SQL statement to insert the new group
    $stmt = $conn->prepare("INSERT INTO AttackGroup (name, description, Threshold) VALUES (:name, :description, :threshold)");

    // Bind parameters
    $stmt->bindValue(':name', $group_name);
    $stmt->bindValue(':description', $group_description);
    $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT); // ระบุว่าเป็นตัวเลข

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['message'] = "Group added successfully!";
    } else {
        $_SESSION['message'] = "Error adding group. Please try again.";
    }

    // Redirect back to the add_group.php page
    header("Location: add_group.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
         body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.5), rgba(0, 0, 0, 0.5)); /* ไล่สีจากสีเหลืองไปสีดำ */
            background-size: cover; /* ปรับขนาดให้เต็มพื้นที่ */
            margin: 0; /* ลบ margin ของ body */
            height: 100vh; /* ให้ความสูงของ body เป็น 100% ของ viewport */

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

        .card:hover {
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
         <a class="navbar-brand">
   <span class="role" style="color: #FFD700;">
      <?php 
         if (isset($_SESSION['admin_login'])) {
             echo '(ADMIN)';  // Display ADMIN if admin is logged in
         } elseif (isset($_SESSION['user_login'])) {
             echo '(USER)';   // Display USER if regular user is logged in
         } 
      ?>
   </span>
   <span class="system">A Brute Force Attack Monitoring System</span>
</a>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="admin.php"><i class="fas fa-home fa-icon"></i>Home</a>
                <a class="nav-item nav-link" href="profile.php"><i class="fas fa-user fa-icon"></i>Profile</a>
                <a class="nav-item nav-link" href="logout.php"><i class="fas fa-sign-out-alt fa-icon"></i>Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container form-container">

    <h1 class="text-center">Add Group</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Form to add a new group -->
    <form action="add_group.php" method="post">
        <div class="mb-3">
            <label for="group_name" class="form-label">New Group Name</label>
            <input type="text" name="group_name" class="form-control" id="group_name" required>
        </div>
        <div class="mb-3">
            <label for="group_description" class="form-label">Description</label>
            <textarea name="group_description" class="form-control" id="group_description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="threshold" class="form-label">Threshold</label>
            <input type="number" name="threshold" class="form-control" id="threshold" required>
        </div>
        <button type="submit" name="add_group" class="btn btn-primary">Add Group</button>
        <a type="button" class="btn btn-danger" href="pattern.php">
            <i class="fas fa-angle-left"></i> กลับ
        </a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

