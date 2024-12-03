<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(ADMIN) A Brute Force Attack Monitoring System from Web Access Log Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
        .form-control {
            border-radius: 0.375rem; /* Make sure all input fields have the same rounded corners */
            box-shadow: none; /* Remove any box-shadow to maintain consistency */
        }
        .form-control:focus {
            border-color: #dc3545; /* Add a custom border color on focus to match the theme */
            box-shadow: none; /* Remove box-shadow on focus */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand">
                <span class="admin">(ADMIN)</span>
                <span class="system"> A Brute Force Attack Monitoring System</span>
            </a>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="admin.php"><i class="fas fa-home"></i> Home</a>
                    <a class="nav-item nav-link" href="#"><i class="fas fa-user"></i> Profile</a>
                    <a class="nav-item nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container text-center my-5">
        <h1 class="display-4">ลงทะเบียน</h1>

        <form action="signup_register.php" method="post">
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
            <?php if(isset($_SESSION['warning'])) { ?>
                <div class="alert alert-warning" role="alert">
                    <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
                </div>
            <?php } ?>

            <div class="mb-3">
                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="ชื่อจริง" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="นามสกุล" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="อีเมล" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="รหัสผ่าน" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="c_password" id="c_password" placeholder="ยืนยันรหัสผ่าน" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="phone" id="phone" placeholder="เบอร์โทร">
            </div>
            <div class="mb-3">
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="รายละเอียด"></textarea>
            </div>
            <div class="mb-3">
                <!-- reCAPTCHA Widget -->
                <div class="g-recaptcha" data-sitekey="6Lf3efMpAAAAAJUAHpOsYK3n218utU1idY57_dbP"></div>
            </div>
            <div class="text-center">
                <button type="submit" name="register" class="btn btn-primary"><i class="fa fa-edit"></i> ลงทะเบียน</button>
                <a class="btn btn-danger ms-2" href="admin.php"><i class="fa fa-home"></i> กลับไปหน้าหลัก</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq9k8w2Nf1m56w27gcsE7VVFVV8PzABnJ5RsiQ90Cn3z7y8u7v" crossorigin="anonymous"></script>
</body>
</html>

