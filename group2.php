<?php
session_start();
require_once 'config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

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


// รับ id ของกลุ่มจาก URL
$group_id = isset($_GET['id']) ? $_GET['id'] : 0;

// ดึงข้อมูลกลุ่มและ pattern ที่เกี่ยวข้อง
$group = getGroupById($conn, $group_id);
$total_patterns = count(getPatternsByGroupId($conn, $group_id));
$patterns_per_page = 10;
$total_pages = ceil($total_patterns / $patterns_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $patterns_per_page;

$patterns = getPatternsByGroupIdWithPagination($conn, $group_id, $offset, $patterns_per_page);

function getThresholdByGroupId($conn, $group_id) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT threshold FROM AttackGroup WHERE id = :id");
    
    // Bind the group ID to the query
    $stmt->bindParam(':id', $group_id);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch the result
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Return the threshold value if available, or an empty string if not found
    return $group['threshold'] ?? '';
}

// ฟังก์ชั่นต่าง ๆ
function getGroupById($conn, $group_id) {
    $stmt = $conn->prepare("SELECT * FROM AttackGroup WHERE id = :id");
    $stmt->bindParam(':id', $group_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPatternsByGroupId($conn, $group_id) {
    $stmt = $conn->prepare("SELECT * FROM pattern_table WHERE group_id = :group_id");
    $stmt->bindParam(':group_id', $group_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPatternsByGroupIdWithPagination($conn, $group_id, $offset, $limit) {
    $stmt = $conn->prepare("SELECT * FROM pattern_table WHERE group_id = :group_id LIMIT :offset, :limit");
    $stmt->bindParam(':group_id', $group_id);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle delete selected patterns
if (isset($_POST['delete_patterns'])) {
    $pattern_ids = $_POST['pattern_ids'];

    // ตรวจสอบว่ามี ID ของแพทเทิร์นที่ถูกเลือกหรือไม่
    if (!empty($pattern_ids)) {
        $placeholders = implode(',', array_fill(0, count($pattern_ids), '?'));
        $stmt = $conn->prepare("DELETE FROM pattern_table WHERE id IN ($placeholders)");
        
        // ลบแพทเทิร์น
        $stmt->execute($pattern_ids);
        $_SESSION['success'] = "ลบแพทเทิร์นเรียบร้อยแล้ว!";
    } else {
        $_SESSION['error'] = "ไม่มีแพทเทิร์นที่ถูกเลือก!";
    }

    header("Location: group.php?id=" . $group_id);
    exit();
}

// Handle delete single pattern
if (isset($_POST['delete_single_pattern'])) {
    $pattern_id = $_POST['pattern_id'];

    $stmt = $conn->prepare("DELETE FROM pattern_table WHERE id = :id");
    $stmt->bindParam(':id', $pattern_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "ลบแพทเทิร์นเรียบร้อยแล้ว!";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบแพทเทิร์น!";
    }

    header("Location: group.php?id=" . $group_id);
    exit();
}

// Handle edit pattern
if (isset($_POST['edit_pattern'])) {
    $pattern_id = $_POST['pattern_id'];
    $new_name = $_POST['new_name'];

    // ตรวจสอบค่าก่อนอัปเดต
    if (!empty($new_name)) {
        $stmt = $conn->prepare("UPDATE pattern_table SET namePattern = :new_name WHERE id = :pattern_id");
        $stmt->bindParam(':new_name', $new_name);
        $stmt->bindParam(':pattern_id', $pattern_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "อัปเดตชื่อแพทเทิร์นเรียบร้อยแล้ว!";
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดตชื่อแพทเทิร์น!";
        }
    } else {
        $_SESSION['error'] = "ชื่อแพทเทิร์นไม่สามารถเป็นค่าว่าง!";
    }

    header("Location: group.php?id=" . $group_id);
    exit();
}

if (strlen($group['name']) > 255) { // สมมติว่า VARCHAR(255)
    $_SESSION['error'] = "ประเภทไม่ถูกต้อง! ชื่อกลุ่มยาวเกินไป";
    header("Location: group.php?id=" . $group_id);
    exit();
}


// Handle add pattern

if (isset($_POST['add_pattern'])) {
    // Retrieve the input from the form
    $new_pattern_text = $_POST['new_pattern_text'];
    $patterns = explode("\n", $new_pattern_text); // Split the input into an array

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO pattern_table (group_id, namePattern,  md5) VALUES (:group_id, :namePattern, :md5)");
    
    foreach ($patterns as $pattern_name) {
        $pattern_name = trim($pattern_name); // Trim whitespace

        if (!empty($pattern_name)) {
            $md5 = md5($pattern_name);

            // Check if the pattern already exists
            $check_stmt = $conn->prepare("SELECT COUNT(*) FROM pattern_table WHERE md5 = :md5");
            $check_stmt->bindParam(':md5', $md5);
            $check_stmt->execute();
            $exists = $check_stmt->fetchColumn();

            if ($exists == 0) {
                // Check if group name exists and is valid
                if (isset($group['name']) && !empty($group['name'])) {
                    // Validate the length of group name
                    if (strlen($group['name']) > 255) {
                        $_SESSION['error'] = "Invalid type! Group name is too long.";
                        header("Location: group.php?id=" . $group_id);
                        exit();
                    }

                    // Validate the length of the pattern name
                    if (strlen($pattern_name) > 255) {
                        $_SESSION['error'] = "Pattern name is too long!";
                        header("Location: group.php?id=" . $group_id);
                        exit();
                    }

                    // Bind the parameters and execute the statement
                    $stmt->bindParam(':group_id', $group_id);
                    $stmt->bindParam(':namePattern', $pattern_name);
                    $stmt->bindParam(':md5', $md5);

                    if (!$stmt->execute()) {
                        error_log("SQL Error: " . implode(", ", $stmt->errorInfo())); // Log any SQL errors
                        $_SESSION['error'] = "An error occurred while adding the pattern!";
                    } else {
                        $_SESSION['success'] = "Pattern added successfully!";
                    }
                } else {
                    $_SESSION['error'] = "Invalid type!";
                }
            }
        }
    }

    // Redirect back to the group page
    header("Location: group.php?id=" . $group_id);
    exit();
}



// Handle update threshold
if (isset($_POST['update_threshold'])) {
    $threshold = $_POST['threshold'];
	
    // ตรวจสอบว่าค่า threshold อยู่ในช่วง 1-1000
    if (is_numeric($threshold) && $threshold >= 1 && $threshold <= 1000) {
        $stmt = $conn->prepare("UPDATE AttackGroup SET threshold = :threshold WHERE id = :id");
        $stmt->bindParam(':threshold', $threshold);
        $stmt->bindParam(':id', $group_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "อัปเดตเรียบร้อยแล้ว!";
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดต!";
        }
    } else {
        $_SESSION['error'] = "กรุณาใส่ค่า threshold ที่ถูกต้อง (1-1000)";
    }

    header("Location: group.php?id=" . $group_id);
    exit();
}
$threshold_value = getThresholdByGroupId($conn, $group_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Patterns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
            background-color: #212529;
            border-bottom: 3px solid #FFD700;
        }

        .navbar-brand {
            color: #212121;
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-brand .admin {
            color: #FFD700;
        }

        .navbar-brand .system {
            color: #212121;
        }

        .navbar-nav .nav-link {
            color: #FFD700;
            font-weight: bold;
            background-color: #343a40;
            border-radius: 5px;
            padding: 10px 15px;
            transition: background-color 0.3s;
        }

        .navbar-nav .nav-link.active {
            color: #FFFFFF;
            background-color: #FFD700;
        }

        .navbar-nav .nav-link:hover {
            background-color: #FFD700;
            color: #343a40;
        }

        .container {
            margin-top: 30px;
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        .monitor {
            margin-top: 20px;
        }

        .table th, .table td {
            text-align: center;
        }

        .btn-lg {
            margin-top: 10px;
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-warning {
            background-color: #FFD700;
        }

        .btn-warning:hover {
            background-color: #F5C300;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .fa-icon {
            margin-right: 5px;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
    </style>
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

<div class="container">
  <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <h2>Group: <?php echo htmlspecialchars($group['name']); ?></h2>

    <!-- Update Threshold -->
    <form method="post" class="mb-3">
        <div class="input-group">
            <input type="number" name="threshold" class="form-control" placeholder="Set Threshold" value="<?php echo htmlspecialchars($threshold_value); ?>" required>
            <button type="submit" name="update_threshold" class="btn btn-warning btn-lg">Update Threshold</button>
        </div>
    </form>

    <!-- Add Pattern -->
    <form method="post" class="mb-3">
        <textarea name="new_pattern_text" rows="5" class="form-control" placeholder="Enter patterns (one per line)" required></textarea>
        <button type="submit" name="add_pattern" class="btn btn-warning btn-lg">Add Patterns</button>
    </form>

    <!-- Display Patterns -->
    <form method="post" class="mb-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Pattern</th>
                    <th>Eidt</th>
                    <th>Delete</th>
                </tr>
            </thead>
<tbody>
    <?php foreach ($patterns as $pattern): ?>
    <tr>
        <td><input type="checkbox" name="pattern_ids[]" value="<?php echo $pattern['id']; ?>"></td>
        <td>
            <!-- Form for Editing Pattern Inline -->
         <form method="post" style="display: inline; width: 100%;">
                        <input type="hidden" name="pattern_id" value="<?php echo $pattern['id']; ?>">
                        <input type="text" name="new_name" value="<?php echo htmlspecialchars($pattern['namePattern']); ?>" required style="width:100%;">

                    </form>
        </td>
        <td>
       <button type="submit" name="edit_pattern" class="btn btn-warning">
    <i class="fas fa-save"></i> <!-- ใช้ไอคอนบันทึก -->
</button>
        </td>
        <td>
            <!-- Delete Single Pattern -->
            <form method="post" style="display: inline;">
                <input type="hidden" name="pattern_id" value="<?php echo $pattern['id']; ?>">
               <button type="submit" name="delete_single_pattern" class="btn btn-danger">
    <i class="fas fa-trash"></i> <!-- ใช้ไอคอนถังขยะสำหรับลบ -->
</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
        </table>

        <div class="btn-group">
            <button type="submit" name="delete_patterns" class="btn btn-danger btn-lg">Delete Selected</button>
        <a type="button" class="btn btn-danger btn-lg" href="groups_list.php">
           <i class="fas fa-angle-left"></i> กลับ
        </a>
        </div>

    </form>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $current_page === $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?id=<?php echo $group_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-fdW4hj30AK/fZgrb6Tg2A3QEOgA1t5kLBrD3XAt5Vh1uY0NOpCAwMf9IbK2dkkEG" crossorigin="anonymous"></script>
<script>
    // Select/Deselect all checkboxes
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="pattern_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
</body>
</html>

