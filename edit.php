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

    // Pagination setup
    $limit = 10; // จำนวนผู้ใช้ต่อหน้า
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // ดึงข้อมูลผู้ใช้ที่อยู่ในระบบ
    $stmt = $conn->prepare("SELECT * FROM users WHERE status = 'active' LIMIT ?, ?");
    $stmt->bind_param("ii", $offset, $limit);
    $stmt->execute();
    $existing_users = $stmt->get_result();

    // นับจำนวนผู้ใช้ทั้งหมดสำหรับการแบ่งหน้า
    $total_stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE status = 'active'");
    $total_stmt->execute();
    $total_users = $total_stmt->get_result()->fetch_row()[0];
    $total_pages = ceil($total_users / $limit);

    // ดึงข้อมูลผู้ใช้ที่สมัครแต่ต้องการการอนุมัติ
    $stmt_pending = $conn->prepare("SELECT * FROM users WHERE status = 'pending' LIMIT ?, ?");
    $stmt_pending->bind_param("ii", $offset, $limit);
    $stmt_pending->execute();
    $pending_users = $stmt_pending->get_result();

} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(ADMIN) Edit User</title>
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
            <a class="navbar-brand"><span class="admin">(ADMIN)</span> <span class="system">A Brute Force Attack Monitoring System</span></a>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="admin.php"><i class="fas fa-home fa-icon"></i>Home</a>
                    <a class="nav-item nav-link" href="profile.php"><i class="fas fa-user fa-icon"></i>Profile</a>
                    <a class="nav-item nav-link" href="logout.php"><i class="fas fa-sign-out-alt fa-icon"></i>Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <!-- โค้ดที่เพิ่มเข้ามา -->
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success" role="alert">
                <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php } ?>

        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php } ?>

<div class="container mt-5">
    <h1 class="text-center">แก้ไขข้อมูลผู้ใช้</h1>

    <!-- Card สำหรับผู้ใช้ที่อยู่ในระบบ -->
   <div class="card mb-4">
    <div class="card-header">
        <h3>ผู้ใช้ที่อยู่ในระบบ</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>อีเมล</th>
                    <th>ชื่อจริง</th>
                    <th>นามสกุล</th>
                    <th>เบอร์โทร</th>
                    <th>สถานะ</th>
                    <th>แก้ไข</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $existing_users->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['urole']); ?></td>
                        <td>
                            <a href="update.php?id=<?php echo urlencode($user['id']); ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                        </td>
                        <td>
                            <a href="deleteEdit.php?id=<?php echo urlencode($user['id']); ?>" class="btn btn-danger" onclick="return confirm('คุณต้องการลบผู้ใช้นี้หรือไม่?');">
                                <i class="fas fa-trash"></i> ลบ
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Pagination สำหรับผู้ใช้ที่อยู่ในระบบ -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>
    <!-- Card สำหรับผู้ใช้ที่รอการอนุมัติ -->


</body>
</html>

