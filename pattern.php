<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(ADMIN) A Brute Force Attack Monitoring System from Web Access Log Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .container {
            margin-top: 20px;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar a {
            color: white !important;
        }
        .pattern-container {
            background-color: #fff;
            border-radius: 0.5rem;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .add-edit-button {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .add-edit-button .btn {
            min-width: 150px;
            font-size: 16px;
        }
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        .card-body {
            text-align: center;
        }
        .btn-lg {
            font-size: 18px;
            padding: 10px 20px;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" style="font-weight: bold; color: #dc3545; font-size: 24px;">A Brute Force Attack Monitoring System from Web Access Log Files</a>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="admin.php">หน้าหลัก</a>
                    <a class="nav-item nav-link" href="#">โปรไฟล์</a>
                    <a class="nav-item">
                        <a class="btn btn-danger btn-lg" href="logout.php">ออกจากระบบ</a>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pattern-container">
        <div class="card">
           

            
            <div class="card-body">
            	<h1 class="card-title">เพิ่ม/แก้ไข Pattern</h1>
                <div class="add-edit-button">
                    <a href="add_pattern.php" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-lg"></i> เพิ่ม Pattern
                    </a>
                    <a href="edit_pattern.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-pencil"></i> แก้ไข Pattern
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 col-6 mx-auto mt-3">
        <a type="button" class="btn btn-danger btn-lg" href="admin.php">
            <i class="bi bi-arrow-left"></i> กลับ
        </a>
    </div>

    <!-- Bootstrap Icons CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-6Nq2E7PxeHnAFO/x9pVxTqIBwT5Gc1WQzdHb7rjAfm80+7zP+g5BX4kYG7buE3jz2" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</body>
</html>

