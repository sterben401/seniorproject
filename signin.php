<?php
session_start();
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

    <!-- Custom CSS -->
    <style>
        /* ฟอนต์หลัก */
        body {
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        /* กล่องฟอร์ม */
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8.5px);
            -webkit-backdrop-filter: blur(8.5px);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            position: relative;
        }

        /* โลโก้ */
        .logo {
            text-align: center;
            margin-bottom: 20px;
            transition: transform 0.6s ease;
        }

        .logo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            transition: transform 0.6s ease;
        }

        .logo img:hover {
            transform: rotate(360deg);
        }

        /* หัวข้อ */
        .container h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 10px;
        }

        .container h3 {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        /* ป้ายชื่อฟอร์ม */
        .form-label {
            color: #333;
            font-weight: 500;
        }

        /* อินพุตฟิลด์ */
        .form-control {
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 10px rgba(52, 152, 219, 0.2);
        }

        /* ปุ่ม Sign In */
        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(41, 128, 185, 0.3);
        }

        .btn-disabled {
            background-color: #ccc !important;
            cursor: not-allowed;
        }

        /* การแจ้งเตือน */
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .alert-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .alert-success {
            background-color: #27ae60;
            color: #fff;
        }

        /* รักษาระยะห่างของฟอร์ม */
        .form-group {
            margin-bottom: 20px;
        }

        /* ปุ่ม Google reCAPTCHA */
        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        /* เอฟเฟกต์การเปิดฟอร์ม */
        .container {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* สื่อสารกับอุปกรณ์ขนาดเล็ก */
        @media (max-width: 576px) {
            .container {
                padding: 20px;
            }

            .logo img {
                width: 80px;
                height: 80px;
            }

            .container h1 {
                font-size: 1.5rem;
            }

            .container h3 {
                font-size: 1rem;
            }
        }
    </style>

    <!-- Custom JavaScript for Button Disabling and Hover Effect -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="password"]');
            const loginButton = document.querySelector('button[name="signin"]');

            function isFormValid() {
                return emailInput.value.trim() !== '' && passwordInput.value.trim() !== '';
            }

            function updateLoginButton() {
                if (isFormValid()) {
                    loginButton.classList.remove('btn-disabled');
                    loginButton.disabled = false;
                    loginButton.style.backgroundColor = '#3498db'; // เมื่อเปิดใช้งาน
                } else {
                    loginButton.classList.add('btn-disabled');
                    loginButton.disabled = true;
                    loginButton.style.backgroundColor = '#ccc'; // เมื่อปิดใช้งาน
                }
            }

            emailInput.addEventListener('input', updateLoginButton);
            passwordInput.addEventListener('input', updateLoginButton);

            loginButton.addEventListener('mouseenter', function () {
                if (!loginButton.disabled) {
                    loginButton.style.backgroundColor = '#2980b9';
                }
            });

            loginButton.addEventListener('mouseleave', function () {
                if (!loginButton.disabled) {
                    loginButton.style.backgroundColor = '#3498db';
                }
            });
        });
    </script>
</head>
<body>

<div class="container">
    <div class="logo">
        <img src="img/logo project.png" alt="Logo">
    </div>
    <h1 class="text-center">A Brute Force Attack Monitoring System</h1>
    <h3 class="text-center">from Web Access Log Files</h3>
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
            <input type="email" class="form-control" name="email" aria-describedby="email" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        
        <div class="g-recaptcha" data-sitekey="6Lf3efMpAAAAAJUAHpOsYK3n218utU1idY57_dbP"></div>

        <button type="submit" name="signin" class="btn btn-primary btn-disabled" disabled>Sign In</button>
    </form>
</div>

</body>
</html>

