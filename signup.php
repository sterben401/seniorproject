<?php
session_start();
require_once 'config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve form data from session
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']); // Clear session data after use
?>
<!DOCTYPE html>
<html lang="th"> <!-- Language code for Thai -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.5), rgba(0, 0, 0, 0.5));
            font-family: 'Roboto', sans-serif;
            height: 105vh;
            display: flex;
            justify-content: center;
            align-items: center;
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
        
        .btn-danger {
            border: none;
            background: none;
            padding: 0;
            position: absolute; /* Make button absolute to position correctly */
            top: 10px; /* Adjust based on design */
            right: 10px; /* Adjust based on design */
        }

        .btn-danger i {
            font-size: 24px;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
         
        <div class="logo">
            <h1>สมัครสมาชิก</h1>
        </div>
	<a href="signin.php" class="btn btn-danger">
            <i class="fas fa-times"></i>
        </a>
            <?php if (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php } ?>
            <?php if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success" role="alert">
                    <?php
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php } ?>

        <form id="signupForm" action="signup_db.php" method="post">
            <!-- Display error/success messages -->
       
            <!-- Form Fields -->
            <div class="form-group">
                <label for="firstname">ชื่อ</label>
                <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($formData['firstname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="lastname">นามสกุล</label>
                <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($formData['lastname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">อีเมล</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="phone">เบอร์โทรศัพท์</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($formData['description'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="token">Token Line</label>
                <input type="text" class="form-control" name="token" value="<?php echo htmlspecialchars($formData['token'] ?? ''); ?>">
            </div>
            <div class="g-recaptcha" data-sitekey="6Lf3efMpAAAAAJUAHpOsYK3n218utU1idY57_dbP"></div> <!-- reCAPTCHA -->
            <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
        </form>
    </div>
</body>
</html>

