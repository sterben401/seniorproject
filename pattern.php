<?php
session_start();
require_once 'config/db.php';


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
$page_title = $role === 'admin' ? '(ADMIN) A Brute Force Attack Monitoring System' : '(USER) A Brute Force Attack Monitoring System';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pattern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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

    <div class="container pattern-container">
        <div class="card">
            <div class="card-body">
                <h1 class="system">เพิ่ม/แก้ไข Pattern</h1>
                <div class="add-edit-button">
                    <a href="add_group.php" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-lg"></i> Add Group
                    </a>
                    <a href="groups_list.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-pencil"></i> Group List
                    </a>
                    

                </div>
            </div>
        </div>
		<a type="button" class="btn btn-danger btn-lg" 
			   href="<?php 
			      if (isset($_SESSION['admin_login'])) {
				  echo 'admin.php';  // Admin dashboard
			      } elseif (isset($_SESSION['user_login'])) {
				  echo 'homeuser.php';  // User dashboard
			      } else {
				  echo 'signin.php';  // Redirect to login if no session
			      }
			   ?>">
			   <i class="fas fa-home fa-icon"></i> กลับไปหน้าหลัก
			</a>
    </div>


    <!-- Bootstrap Icons CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-6Nq2E7PxeHnAFO/x9pVxTqIBwT5Gc1WQzdHb7rjAfm80+7zP+g5BX4kYG7buE3jz2" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</body>
</html>

