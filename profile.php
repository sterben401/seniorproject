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

try {
    // Establish database connection
    $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle POST request for updating user information
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $phone = $_POST['phone']; // Phone number input
        $description = $_POST['description'];
        $token = $_POST['token']; // Token input
	 if (!preg_match('/^\d{10}$/', $phone)) {
            $_SESSION['error'] = 'หมายเลขโทรศัพท์ต้องมี 10 หลัก';
            header("Location: profile.php");
            exit();
        }
        // Update user information in the database
        $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, phone = ?, description = ?, token = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $firstname, $lastname, $phone,$description, $token, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'อัปเดตข้อมูลเสร็จสิ้น!';
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล!';
        }

        $stmt->close();
    }

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.5), rgba(0, 0, 0, 0.5)); /* ไล่สีจากสีเหลืองไปสีดำ */
            background-size: cover; /* ปรับขนาดให้เต็มพื้นที่ */
            margin: 0; /* ลบ margin ของ body */
            height: 100vh; /* ให้ความสูงของ body เป็น 100% ของ viewport */

	font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .card-header {
            background-color: #FFD700;
            color: #212121;
            font-weight: bold;
            font-size: 20px;
            border-bottom: 2px solid #212121;
            border-radius: 15px 15px 0 0;
        }
        .card-body {
            padding: 30px;
        }
        .btn-primary {
            background-color: #FFD700;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: #212121;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #F5C300;
        }
        .text-primary {
            color: #FFD700 !important;
        }
        .card-text strong {
            color: #212121;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-primary">Profile</h3>

        <!-- แสดงข้อความสำเร็จหรือข้อผิดพลาด -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" role="alert">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                ข้อมูลผู้ใช้
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">ชื่อ</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">หมายเลขโทรศัพท์</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">description</label>
                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($row['description']); ?>">
                    </div>
                    
                     <div class="mb-3">
                        <label for="token" class="form-label">Token Line</label>
                        <input type="text" class="form-control" id="token" name="token" value="<?php echo htmlspecialchars($row['token']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">อัปเดตข้อมูล</button>
                </form>

                <a type="button" class="btn btn-primary mt-3" 
			   href="<?php 
			      if (isset($_SESSION['admin_login'])) {
				  echo 'admin.php';  // Admin dashboard
			      } elseif (isset($_SESSION['user_login'])) {
				  echo 'homeuser.php';  // User dashboard
			      } else {
				  echo 'signin.php';  // Redirect to login if no session
			      }
			   ?>"
			   <i class="fas fa-home fa-icon"></i> กลับไปหน้าหลัก
			</a>
            </div>
        </div>
    </div>
</body>
</html>

