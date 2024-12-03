<?php
session_start();
if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data']; // ใช้ข้อมูลฟอร์มในเซสชัน
    unset($_SESSION['form_data']); // เคลียร์ข้อมูลหลังการใช้งาน
} else {
    $formData = []; // ค่าเริ่มต้น
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A Brute Force Attack Monitoring System from Web Access Log Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.5), rgba(0, 0, 0, 0.5)); /* ไล่สีจากสีเหลืองไปสีดำ */
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-size: cover; /* ปรับขนาดให้เต็มพื้นที่ */
            margin: 0;
        }
         
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8.5px);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            position: relative;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }
        .container h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 10px;
        }
        .form-label {
            color: #333;
            font-weight: 500;
        }
        .form-control {
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #ddd;
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background-color: #e74c3c;
            color: #fff;
        }
        .alert-success {
            background-color: #27ae60;
            color: #fff;
        }
        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .text-center a {
            text-decoration: none;
            color: #3498db;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <img src="img/logo project.png" alt="Logo">
    </div>
    <h1 class="text-center">A Brute Force Attack Monitoring System</h1>
    <form action="signin_db.php" method="post">
        <?php if(isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php } ?>
        <?php if(isset($_SESSION['success'])) { ?>
            <div class="alert alert-success" role="alert">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php } ?>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
             <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>

        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="g-recaptcha" data-sitekey="6Lf3efMpAAAAAJUAHpOsYK3n218utU1idY57_dbP"></div> 
        <button type="submit" name="signin" class="btn btn-primary">Sign In</button>
        <div class="text-center mt-4">
<p>ยังไม่ได้สมัครสมาชิก? <a href="signup.php" style="color:#3498db;">สมัครสมาชิกที่นี่</a></p>

        </div>
    </form>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('signup-link').addEventListener('click', function() {
        $('#signupModal').modal('show');
    });
</script>
</body>
</html>

