<?php
session_start();
require_once 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_pattern'])) {
    $pattern_id = $_POST['pattern_id'];
    $new_name = $_POST['new_name'];

    // ป้องกันการโจมตี XSS
    $new_name = htmlspecialchars($new_name);

    // การอัปเดตข้อมูลในฐานข้อมูล
    $updateQuery = "UPDATE patterns SET namePattern = :namePattern WHERE id = :id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute(['namePattern' => $new_name, 'id' => $pattern_id]);

    // ตรวจสอบผลลัพธ์
    if ($updateStmt->rowCount() > 0) {
        $_SESSION['success'] = 'แก้ไข Pattern สำเร็จ';
    } else {
        $_SESSION['error'] = 'ไม่สามารถแก้ไข Pattern ได้';
    }

    // เปลี่ยนเส้นทางกลับไปยังหน้าที่แสดง Pattern
    header("Location: your_pattern_page.php");
    exit();
}

?>

<!--<!DOCTYPE html>
<html lang="en">
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
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .edit-pattern-form {
            margin-top: 20px;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" style="font-weight: bold; color: #dc3545; font-size: 24px;">A Brute Force Attack Monitoring System from Web Access Log Files</a>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-item nav-link" href="pattern.php">Back</a>
                        <a class="nav-item nav-link" href="#">Home</a>
                        <a class="nav-item nav-link" href="#">Profile</a>
                        <a class="nav-item">
                            <a class="btn btn-danger btn-lg" href="logout.php">Logout</a>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="container pattern-container">
        <h1 class="text-center">แก้ไข Pattern</h1>

        <!-- Type selection dropdown -->
        <div class="mb-3">
            <form action="edit_pattern.php" method="post">
                <label for="type" class="form-label">เลือกประเภทของ Pattern</label>
                <select name="selected_type" class="form-select" id="type" onchange="this.form.submit()">
                    <?php foreach ($types as $typeItem): ?>
                        <option value="<?php echo $typeItem['type']; ?>" <?php if ($selected_type == $typeItem['type']) echo 'selected'; ?>>
                            <?php echo $typeItem['type']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- Edit pattern form -->
        <form action="edit_pattern.php" method="post">
            <div class="mb-3">
                <label for="patterns" class="form-label">รายการ Pattern</label>
                <textarea name="patterns" class="form-control" id="patterns" rows="5">
                    <?php foreach ($patterns as $patternItem): echo $patternItem['namePattern'] . "\n"; endforeach; ?>
                </textarea>
            </div>
            <button type="submit" name="update_patterns" class="btn btn-primary" onclick="return confirm('คุณต้องการอัพเดตรายการ Pattern ทั้งหมดใช่หรือไม่?')">อัพเดต Pattern</button>
            <div class="d-grid gap-2 col-6 mx-auto">
                <a type="button" class="btn btn-danger btn-lg" href="pattern.php">Back</a>
            </div>
        </form>

        <!-- Success message -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success mt-4">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+vpQlZO4vI" crossorigin="anonymous"></script>
</body>
</html>-->

