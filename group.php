<?php
session_start();
require_once 'config/db.php';

// รับ id ของกลุ่มจาก URL
$group_id = isset($_GET['id']) ? $_GET['id'] : 0;

// ดึงข้อมูลกลุ่มและ pattern ที่เกี่ยวข้อง
$group = getGroupById($conn, $group_id);
$patterns = getPatternsByGroupId($conn, $group_id);

function getGroupById($conn, $group_id) {
    $stmt = $conn->prepare("SELECT * FROM AttackGroup WHERE id = :id");
    $stmt->bindParam(':id', $group_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPatternsByGroupId($conn, $group_id) {
    $stmt = $conn->prepare("SELECT * FROM Pattern WHERE group_id = :group_id");
    $stmt->bindParam(':group_id', $group_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle delete selected patterns
if (isset($_POST['delete_patterns'])) {
    $pattern_ids = $_POST['pattern_ids'];

    if (!empty($pattern_ids)) {
        $stmt = $conn->prepare("DELETE FROM Pattern WHERE id IN (" . implode(',', array_fill(0, count($pattern_ids), '?')) . ")");
        $stmt->execute($pattern_ids);
    }

    header("Location: group.php?id=" . $group_id);
    exit();
}

// Handle delete single pattern
if (isset($_POST['delete_pattern'])) {
    $pattern_id = $_POST['pattern_id'];

    $stmt = $conn->prepare("DELETE FROM Pattern WHERE id = :id");
    $stmt->bindParam(':id', $pattern_id);
    $stmt->execute();

    header("Location: group.php?id=" . $group_id);
    exit();
}

// Handle edit pattern
if (isset($_POST['edit_pattern'])) {
    $pattern_id = $_POST['pattern_id'];
    $new_name = $_POST['new_name'];

    $stmt = $conn->prepare("UPDATE Pattern SET namePattern = :namePattern WHERE id = :id");
    $stmt->bindParam(':namePattern', $new_name);
    $stmt->bindParam(':id', $pattern_id);
    $stmt->execute();

    header("Location: group.php?id=" . $group_id);
    exit();
}

// Handle add pattern
if (isset($_POST['add_pattern'])) {
    $new_pattern_text = $_POST['new_pattern_text'];

    $patterns = explode("\n", $new_pattern_text);

    $stmt = $conn->prepare("INSERT INTO Pattern (group_id, namePattern, type, md5) VALUES (:group_id, :namePattern, :type, :md5)");
    foreach ($patterns as $pattern_name) {
        $pattern_name = trim($pattern_name);
        if (!empty($pattern_name)) {
            $md5 = md5($pattern_name);

            $stmt->bindParam(':group_id', $group_id);
            $stmt->bindParam(':namePattern', $pattern_name);
            $stmt->bindParam(':type', $group['name']); // ชื่อของกลุ่มเป็น type
            $stmt->bindParam(':md5', $md5);
            $stmt->execute();
        }
    }

    header("Location: group.php?id=" . $group_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Patterns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar a {
            color: white !important;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #dc3545;
            color: white;
            border-radius: 15px 15px 0 0;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .list-group-item {
            border: none;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 10px;
            position: relative;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-group {
            margin-top: 20px;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="#" style="font-weight: bold; font-size: 24px;">Brute Force Attack Monitoring System</a>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-item nav-link" href="add_pattern.php">Back</a>
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

    <div class="container">
        <div class="card">
            <div class="card-header">
                Group: <?php echo htmlspecialchars($group['name']); ?>
            </div>
            <div class="card-body">
                <p class="text-center"><?php echo htmlspecialchars($group['description']); ?></p>

                <!-- Form to add new patterns -->
                <form action="group.php?id=<?php echo $group_id; ?>" method="post">
                    <div class="mb-3">
                        <label for="new_pattern_text" class="form-label">Add New Patterns (one per line)</label>
                        <textarea name="new_pattern_text" class="form-control" id="new_pattern_text" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="add_pattern" class="btn btn-primary">Add Patterns</button>
                </form>

                <hr>

                <!-- Form for deleting selected patterns -->
                <form action="group.php?id=<?php echo $group_id; ?>" method="post">
                    <div class="pattern-list">
                        <h3 class="text-center">Patterns</h3>
                        <ul class="list-group">
                            <?php if (empty($patterns)): ?>
                                <li class="list-group-item text-center">No patterns found</li>
                            <?php else: ?>
                                <?php foreach ($patterns as $pattern): ?>
                                    <li class="list-group-item">
                                        <input type="checkbox" name="pattern_ids[]" value="<?php echo $pattern['id']; ?>" class="form-check-input me-2">
                                        <?php echo htmlspecialchars($pattern['namePattern']); ?>
                                        <!-- Button to edit pattern -->
                                        <button type="button" class="btn btn-warning btn-sm float-end ms-2" data-bs-toggle="modal" data-bs-target="#editPatternModal" data-id="<?php echo $pattern['id']; ?>" data-name="<?php echo htmlspecialchars($pattern['namePattern']); ?>">Edit</button>
                                        <!-- Button to delete pattern -->
                                        <form action="group.php?id=<?php echo $group_id; ?>" method="post" class="float-end">
                                            <input type="hidden" name="pattern_id" value="<?php echo $pattern['id']; ?>">
                                            <button type="submit" name="delete_pattern" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <!-- Button to delete selected patterns -->
                        <div class="text-center mt-3">
                            <button type="submit" name="delete_patterns" class="btn btn-danger">Delete Selected Patterns</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing pattern -->
    <div class="modal fade" id="editPatternModal" tabindex="-1" aria-labelledby="editPatternModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPatternModalLabel">Edit Pattern</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="group.php?id=<?php echo $group_id; ?>" method="post">
                        <input type="hidden" name="pattern_id" id="pattern_id">
                        <div class="mb-3">
                            <label for="new_name" class="form-label">New Pattern Name</label>
                            <input type="text" name="new_name" class="form-control" id="new_name" required>
                        </div>
                        <button type="submit" name="edit_pattern" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var editPatternModal = document.getElementById('editPatternModal');
        editPatternModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var patternId = button.getAttribute('data-id');
            var patternName = button.getAttribute('data-name');
            
            var modalTitle = editPatternModal.querySelector('.modal-title');
            var inputPatternName = editPatternModal.querySelector('#new_name');
            var inputPatternId = editPatternModal.querySelector('#pattern_id');

            modalTitle.textContent = 'Edit Pattern ID ' + patternId;
            inputPatternName.value = patternName;
            inputPatternId.value = patternId;
        });
    </script>
</body>
</html>

