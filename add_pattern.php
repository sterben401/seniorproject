<?php
session_start();
require_once 'config/db.php';

// Fetch existing groups
$groups = getGroups($conn);

// Function to fetch all groups
function getGroups($conn) {
    $stmt = $conn->prepare("SELECT id, name FROM AttackGroup");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Add new group
if (isset($_POST['add_group'])) {
    $group_name = $_POST['group_name'];
    $group_description = $_POST['group_description'];

    $stmt = $conn->prepare("INSERT INTO AttackGroup (name, description) VALUES (:name, :description)");
    $stmt->bindParam(':name', $group_name);
    $stmt->bindParam(':description', $group_description);
    $stmt->execute();

    header("Location: add_pattern.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pattern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .form-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .group-list {
            margin-top: 20px;
        }
        .group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }
        .group-item:hover {
            background-color: #f1f1f1;
        }
        .group-item a {
            color: #007bff;
            text-decoration: none;
        }
        .group-item a:hover {
            text-decoration: underline;
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
                    	<a class="btn btn-primary mb1 bg-blue" href="pattern.php">Back</a>
                        <a class="btn btn-danger btn-lg" href="logout.php">ออกจากระบบ</a>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    </div>

    <div class="container form-container">
        <h1 class="text-center">Add Pattern</h1>

        <!-- Form to add a new group -->
        <form action="add_pattern.php" method="post">
            <div class="mb-3">
                <label for="group_name" class="form-label">New Group Name</label>
                <input type="text" name="group_name" class="form-control" id="group_name" required>
            </div>
            <div class="mb-3">
                <label for="group_description" class="form-label">Description</label>
                <textarea name="group_description" class="form-control" id="group_description" rows="3" required></textarea>
            </div>
            <button type="submit" name="add_group" class="btn btn-primary mb1 bg-green">Add Group</button>
        </form>

        <hr>

        <!-- Existing groups list -->
        <div class="group-list">
            <h2>Existing Groups</h2>
            <?php foreach ($groups as $group): ?>
                <div class="group-item">
                    <span><?php echo $group['name']; ?></span>
                    <a href="group.php?id=<?php echo $group['id']; ?>" class="btn btn-secondary">View Patterns</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

