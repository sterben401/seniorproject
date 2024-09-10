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
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(ADMIN) Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-item nav-link active" href="#"><i class="fas fa-edit fa-icon"></i>Edit User</a>
                    <a class="nav-item nav-link" href="profile.php"><i class="fas fa-user fa-icon"></i>Profile</a>
                    <a class="nav-item nav-link" href="logout.php"><i class="fas fa-sign-out-alt fa-icon"></i>Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center text-primary">แก้ไขข้อมูลผู้ใช้</h1>

        <!-- Search User by Email -->
        <form action="edit.php" method="get" class="mb-5">
            <div class="input-group">
                <input type="email" class="form-control" name="email" placeholder="ค้นหาผู้ใช้โดยอีเมล" required>
                <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-icon"></i> ค้นหา</button>
            </div>
        </form>

        <?php if(isset($_GET['email'])) {
            $email = $_GET['email'];
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user) { ?>
                <form action="update.php" method="post">
                    <?php if(isset($_SESSION['error'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php } ?>
                    <?php if(isset($_SESSION['success'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php } ?>

                    <input type="hidden" name="emailEdit" value="<?php echo htmlspecialchars($user['email']); ?>">

                    <div class="mb-3">
                        <input type="text" class="form-control" name="firstnameEdit" value="<?php echo htmlspecialchars($user['firstname']); ?>" placeholder="ชื่อจริง" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="lastnameEdit" value="<?php echo htmlspecialchars($user['lastname']); ?>" placeholder="นามสกุล" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="passwordEdit" placeholder="รหัสผ่าน (เว้นว่างหากไม่ต้องการเปลี่ยน)">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="phoneEdit" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="เบอร์โทร">
                    </div>
                    <div class="mb-3">
                        <select class="form-control" name="status" required>
                            <option value="admin" <?php if ($user['urole'] == 'admin') echo 'selected'; ?>>Admin</option>
                            <option value="user" <?php if ($user['urole'] == 'user') echo 'selected'; ?>>User</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save fa-icon"></i> บันทึก</button>
                    </div>
                </form>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">
                    ไม่พบผู้ใช้ที่มีอีเมลนี้
                </div>
            <?php } 
        } ?>

        <!-- Back to Home Button -->
        <div class="text-center mt-5">
            <a href="admin.php" class="btn btn-primary btn-lg"><i class="fas fa-home fa-icon"></i> กลับไปหน้าหลัก</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

