<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
}

try {
    // เชื่อมต่อฐานข้อมูล
    $conn = new mysqli($servername, $username, $password, $dbname = "PROJECT");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Monitor for ORANGE Status
    $orange_sql = "SELECT * FROM detec_history WHERE Status = 'Orange'";
    $orange_result = $conn->query($orange_sql);

    // Monitor for RED Status
    $red_sql = "SELECT * FROM detec_history WHERE Status = 'Red'";
    $red_result = $conn->query($red_sql);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(User) A Brute Force Attack Monitoring System from Web Access Log Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #333;
        }

        .monitor {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            border-radius: 5px;
        }
.monitor strong {
            font-size: 1.2em;
            color: #333;
        }

        .monitor p {
            color: #555;
        }

        .monitor a {
            text-decoration: none;
            color: #d9534f;
        }

        .monitor a:hover {
            color: #a94442;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>ประวัติการโจมตี</h1>

        <!-- ปุ่มกรองตามวันที่ -->
        <div class="btn-group mb-3" role="group" aria-label="Date Filter">
            <button type="button" class="btn btn-secondary" id="today">วันนี้</button>
            <button type="button" class="btn btn-secondary" id="3days">3 วันที่ผ่านมา</button>
            <button type="button" class="btn btn-secondary" id="7days">7 วันที่ผ่านมา</button>
            <button type="button" class="btn btn-secondary" id="1month">1 เดือนที่ผ่านมา</button>
            <button type="button" class="btn btn-danger" id="deleteAllRecords">ลบข้อมูลทั้งหมด</button>
        </div>

        <!-- Monitor for ORANGE Status -->
<div class="monitor" id="orangeMonitor">
            <strong>Monitor (สถานะ: <span style="color: orange;">ORANGE</span>):</strong>
            <div style="overflow: auto; max-height: 200px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>วันที่และเวลา</th>
                            <th>Pattern</th>
                            <th>Count</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($orange_result->num_rows > 0) {
                            // แสดงข้อมูลจากแต่ละแถว
                            while ($row = $orange_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['Date_detec'] . "</td>";
                                echo "<td>" . $row['Patterns'] . "</td>";
                                echo "<td>" . $row['Count'] . "</td>";
                                echo "<td><span style='color: orange;'>ORANGE</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>ไม่พบข้อมูลใน detec_history สถานะ ORANGE</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monitor for RED Status -->
<div class="monitor" id="redMonitor">
            <strong>Monitor (สถานะ: <span style="color: red;">RED</span>):</strong>
            <div style="overflow: auto; max-height: 200px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>วันที่และเวลา</th>
                            <th>Pattern</th>
                            <th>Count</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($red_result->num_rows > 0) {
                            // แสดงข้อมูลจากแต่ละแถว
                            while ($row = $red_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['Date_detec'] . "</td>";
                                echo "<td>" . $row['Patterns'] . "</td>";
                                echo "<td>" . $row['Count'] . "</td>";
                                echo "<td><span style='color: red;'>RED</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>ไม่พบข้อมูลใน detec_history สถานะ RED</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <a type="button" class="btn btn-danger" href="signin.php">Logout</a>
    </div>

    <script>
document.getElementById("today").addEventListener("click", function() {
            filterByDate('today');
        });

        document.getElementById("3days").addEventListener("click", function() {
            filterByDate('3days');
        });

        document.getElementById("7days").addEventListener("click", function() {
            filterByDate('7days');
        });

        document.getElementById("1month").addEventListener("click", function() {
            filterByDate('1month');
        });

        document.getElementById("deleteAllRecords").addEventListener("click", function() {
            deleteAllRecords();
        });

        function filterByDate(filter) {
            let orangeMonitor = document.getElementById("orangeMonitor");
            let redMonitor = document.getElementById("redMonitor");

            let today = new Date();
            let targetDate = new Date();

            if (filter === '3days') {
                targetDate.setDate(today.getDate() - 3);
            } else if (filter === '7days') {
                targetDate.setDate(today.getDate() - 7);
            } else if (filter === '1month') {
                targetDate.setMonth(today.getMonth() - 1);
            }

            // แปลงวันที่ให้เป็น "YYYY-MM-DD"
            let dateStr = targetDate.toISOString().split('T')[0];
fetchNewDataAndRender(orangeMonitor, redMonitor, dateStr);
        }

        function deleteAllRecords() {
            if (confirm('คุณแน่ใจหรือว่าต้องการลบข้อมูลทั้งหมด?')) {
                fetch('delete_all.php', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetchNewDataAndRender(document.getElementById("orangeMonitor"), document.getElementById("redMonitor"), '');
                        } else {
                            alert('เกิดข้อผิดพลาดในการลบข้อมูล');
                        }
                    })
                    .catch(error => {
                        console.error('เกิดข้อผิดพลาดในการเรียกบริการลบข้อมูล:', error);
                    });
            }
        }

        function fetchNewDataAndRender(orangeMonitor, redMonitor, date) {
            fetch('fetch_data.php?date=' + date)
                .then(response => response.json())
                .then(data => {
                    orangeMonitor.innerHTML = '';
                    redMonitor.innerHTML = '';

                    let orangeTable = createTable(data.orange);
                    orangeMonitor.appendChild(orangeTable);

                    let redTable = createTable(data.red);
                    redMonitor.appendChild(redTable);
                });
        }

        function createTable(data) {
            let table = document.createElement('table');
            table.classList.add('table', 'table-bordered');

            let thead = document.createElement('thead');
            thead.innerHTML = '<tr><th>วันที่และเวลา</th><th>Pattern</th><th>Count</th><th>Status</th></tr>';
            table.appendChild(thead);

            let tbody = document.createElement('tbody');

            if (data.length > 0) {
                data.forEach(row => {
                    let tr = document.createElement('tr');
                    tr.innerHTML = `<td>${row.Date_detec}</td><td>${row.Patterns}</td><td>${row.Count}</td><td>${row.Status}</td>`;
                    tbody.appendChild(tr);
                });
            } else {
                let tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="4">ไม่พบข้อมูล</td>';
                tbody.appendChild(tr);
            }

            table.appendChild(tbody);
            return table;
        }
</script>
</body>

</html>

