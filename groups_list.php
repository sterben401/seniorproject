<?php
session_start();
require_once 'config/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Check if admin or user is logged in
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


// Fetch existing groups
$groups = getGroups($conn);

// Function to fetch all groups
function getGroups($conn) {
    $stmt = $conn->prepare("SELECT id, name FROM AttackGroup");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to delete group by ID
if (isset($_GET['delete'])) {
    $groupId = (int)$_GET['delete']; 

 


    $stmt = $conn->prepare("DELETE FROM AttackGroup WHERE id = ?");
    
    if ($stmt->execute([$groupId])) {
        // Set a success message
        $_SESSION['message'] = "Group and related patterns deleted successfully!";
    } else {
        // Optionally handle the error
        $_SESSION['message'] = "Failed to delete the group.";
    }

    header("Location: groups_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom CSS */
        body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.5), rgba(0, 0, 0, 0.5)); /* ไล่สีจากสีเหลืองไปสีดำ */
            background-size: cover; /* ปรับขนาดให้เต็มพื้นที่ */
            margin: 0; /* ลบ margin ของ body */
            height: 100vh; /* ให้ความสูงของ body เป็น 100% ของ viewport */

	font-family: 'Arial', sans-serif;
        }
        .navbar { background-color: #212529; border-bottom: 3px solid #FFD700; }
        .navbar-brand { color: #212121; font-size: 24px; font-weight: bold; }
        .navbar-brand .admin { color: #FFD700; }
        .navbar-brand .system { color: #212121; }
        .navbar-nav .nav-link { color: #FFD700; font-weight: bold; background-color: #343a40; border-radius: 5px; padding: 10px 15px; transition: background-color 0.3s; }
        .navbar-nav .nav-link:hover { background-color: #FFD700; color: #343a40; }
        .container { margin-top: 30px; background-color: #FFFFFF; border-radius: 15px; padding: 20px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); }
        .card { border-radius: 15px; transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); }
        .btn-lg { margin-top: 10px; border-radius: 25px; padding: 10px 20px; color: white; transition: background-color 0.3s ease; }
        .btn-warning { background-color: #FFD700; }
        .btn-warning:hover { background-color: #F5C300; }
        .btn-danger { background-color: #DC3545; }
        .btn-danger:hover { background-color: #C82333; }
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

<div class="container mt-4">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="text-center mb-4">
        <h1 class="display-4" style="color: #616161;">Groups List</h1>
    </div>
    
    <div class="row">
        <?php foreach ($groups as $group): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-light">
                    <div class="card-body">
                        <h5 class="system"><?php echo $group['name']; ?></h5>
                        <a href="group.php?id=<?php echo $group['id']; ?>" class="btn btn-warning text-dark">View Patterns</a>
                        <a href="groups_list.php?delete=<?php echo $group['id']; ?>" class="btn btn-danger text-light"
                           onclick="return confirm('Are you sure you want to delete this group?');">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <a type="button" class="btn btn-danger" href="pattern.php">
        <i class="fas fa-angle-left"></i> กลับ
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

