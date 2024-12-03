<?php
session_start();



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

?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการโจมตี</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        .custom-btn {
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        .custom-btn:hover {
            background-color: #F5C300;
        }

        /* ปรับขนาดปฏิทิน */
        .flatpickr-input {
            width: 200px; /* กำหนดความกว้าง */
            padding: 10px; /* กำหนด padding */
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
    <h1 class="text-center">ประวัติการโจมตี</h1>

    <div class="btn-group mb-3" role="group" aria-label="Date Filter">
        <label for="dateRange" class="form-label">เลือกช่วงวันที่:</label>
        <input type="text" class="form-control" id="dateRange" placeholder="เลือกช่วงวันที่" aria-label="เลือกช่วงวันที่">
        
        <button type="button" class="btn btn-danger" id="deleteAllRecords"><i class="fas fa-trash fa-icon"></i>ลบข้อมูลทั้งหมด</button>
     <!-- <button type="button" class="btn custom-btn btn-warning" onclick="filterData()">กรองข้อมูล</button>

        <!-- ปุ่มฟังก์ชัน 3 วัน, 7 วัน, 1 เดือน -->
        <button type="button" class="btn custom-btn btn-info" onclick="setDateRange(3)">3 วัน</button>
        <button type="button" class="btn custom-btn btn-info" onclick="setDateRange(7)">7 วัน</button>
        <button type="button" class="btn custom-btn btn-info" onclick="setDateRange(30)">1 เดือน</button>
    </div>

    <!-- Monitor for ORANGE Status -->
    <div class="monitor" id="orangeMonitor">
        <strong>Monitor (สถานะ: <span style="color: orange;">ORANGE</span>):</strong>
        <div style="overflow: auto; max-height: 400px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>วันที่และเวลา</th>
                        <th>Type</th>
                        <th>Count</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="orangeTableBody">
                    <!-- Data will be inserted here via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monitor for RED Status -->
    <div class="monitor" id="redMonitor">
        <strong>Monitor (สถานะ: <span style="color: red;">RED</span>):</strong>
        <div style="overflow: auto; max-height: 400px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>วันที่และเวลา</th>
                        <th>Type</th>
                        <th>Count</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="redTableBody">
                    <!-- Data will be inserted here via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

     	<a type="button" class="btn btn-danger btn-lg" 
			   href="<?php 
			      if (isset($_SESSION['admin_login'])) {
				  echo 'admin.php';  // Admin dashboard
			      } elseif (isset($_SESSION['user_login'])) {
				  echo 'homeuser.php';  // User dashboard
			      } else {
				  echo 'signin.php';  // Redirect to login if no session
			      }
			   ?>">
			   <i class="fas fa-home fa-icon"></i> กลับไปหน้าหลัก
		</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const dateRangeInput = document.getElementById("dateRange");
    
    flatpickr(dateRangeInput, {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [new Date(), new Date()],
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                fetchNewDataAndRender(selectedDates[0], selectedDates[1]);
            }
        }
    });

    document.getElementById("deleteAllRecords").addEventListener("click", function() { deleteAllRecords(); });

    function deleteAllRecords() {
        if (confirm('คุณแน่ใจหรือว่าต้องการลบข้อมูลทั้งหมด?')) {
            fetch('delete_all.php', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchNewDataAndRender();
                    } else {
                        alert('เกิดข้อผิดพลาดในการลบข้อมูล');
                    }
                })
                .catch(error => {
                    console.error('เกิดข้อผิดพลาดในการเรียกบริการลบข้อมูล:', error);
                });
        }
    }
    
    
    function filterData() {
    // ดึงค่าจาก input fields
    const startDateInput = document.getElementById('startDate').value; // วันที่เริ่มต้น
    const endDateInput = document.getElementById('endDate').value; // วันที่สิ้นสุด
    const typeInput = document.getElementById('typeInput').value; // ประเภท

    // ตรวจสอบว่ามีการกรอกวันที่หรือไม่
    if (!startDateInput || !endDateInput) {
        alert("กรุณากรอกวันที่เริ่มต้นและวันที่สิ้นสุด");
        return;
    }

    // กำหนดค่า endDate ให้รวมถึง 23:59:59
    const endDate = `${endDateInput} 23:59:59`;

    console.log("Start Date:", startDateInput);
    console.log("End Date:", endDate);
    console.log("Type:", typeInput); // แสดงประเภท

    // สร้าง query string สำหรับการเรียกข้อมูล
    const queryString = `?start=${startDateInput}&end=${endDate}&type=${typeInput}`;

    fetch(`fetch_data.php${queryString}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log("Parsed data:", data);
            renderData(data); // ฟังก์ชันที่ใช้ในการอัปเดต UI
        })
        .catch(error => {
            console.error('เกิดข้อผิดพลาดในการดึงข้อมูล:', error);
        });
}


function setDateRange(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(endDate.getDate() - days);
    fetchNewDataAndRender(startDate, endDate);
    dateRangeInput._flatpickr.setDate([startDate, endDate]);
}

function fetchNewDataAndRender(startDate = null, endDate = null) {
    if (startDate) {
        // หากมีการเลือก startDate ให้ตั้งเป็นเวลาตั้งแต่ 00:00:00
        startDate.setHours(0, 0, 0, 0);
    } else {
        // หากไม่มีการเลือก startDate ให้ตั้งค่าเป็นวันที่ปัจจุบัน
        startDate = new Date();
        startDate.setHours(0, 0, 0, 0); // ตั้งเวลาเป็น 00:00:00
    }

    // ตั้งค่า endDate เป็นเวลา 23:59:59
    if (endDate) {
        endDate.setHours(23, 59, 59, 999);
    } else {
        endDate = new Date();
        endDate.setHours(23, 59, 59, 999); // ตั้งค่าให้เป็นเวลา 23:59:59 ของวันปัจจุบัน
    }

    // แสดงวันที่เริ่มต้นและสิ้นสุดใน console
    console.log("Start Date:", startDate);
    console.log("End Date:", endDate);

    const queryString = `?start=${startDate.toISOString().split('T')[0]}&end=${endDate.toISOString().split('T')[0]}`;
    
    fetch(`fetch_data.php${queryString}`)
        .then(response => response.text())
        .then(text => {
            console.log("Raw response:", text);
            try {
                const data = JSON.parse(text);
                console.log("Parsed data:", data);
                renderData(data);
            } catch (e) {
                console.error("Error parsing JSON:", e);
            }
        })
        .catch(error => {
            console.error('เกิดข้อผิดพลาดในการดึงข้อมูล:', error);
        });
}




function renderData(data) {
    console.log("Data to render:", data); // Log ข้อมูลที่จะแสดงผล
    const orangeTableBody = document.getElementById("orangeTableBody");
    const redTableBody = document.getElementById("redTableBody");
    orangeTableBody.innerHTML = '';
    redTableBody.innerHTML = '';

    data.forEach(item => {
        const row = `<tr>
            <td>${item.date_detec}</td>
            <td>${item.type}</td>
            <td>${item.count}</td>
            <td style="color: ${item.status === 'ORANGE' ? 'orange' : 'red'};">${item.status}</td>
            <td style="text-align: center;">
                <button class="btn btn-info btn-sm" onclick="readFile('${item.id}')"><i class="fas fa-file-alt"></i> Read File</button>
            </td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="deleteRecord(${item.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;

        if (item.status === 'ORANGE') {
            orangeTableBody.insertAdjacentHTML('beforeend', row);
        } else if (item.status === 'RED') {
            redTableBody.insertAdjacentHTML('beforeend', row);
        }
    });

    // หากไม่มีข้อมูลแสดงข้อความว่าไม่พบข้อมูล
    if (data.length === 0) {
        orangeTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">ไม่พบข้อมูล</td></tr>';
    }
}

function readFile(id) {
    fetch(`read_file.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(content => {
            alert(`เนื้อหาไฟล์:\n${content}`); // Display the file content in an alert box
        })
        .catch(error => {
            console.error('เกิดข้อผิดพลาดในการอ่านไฟล์:', error);
        });
}


function deleteRecord(id) {
    if (confirm('คุณแน่ใจหรือว่าต้องการลบรายการนี้?')) {
        fetch(`delete_record.php?id=${id}`, { method: 'DELETE' })
            .then(response => response.json())  // Parse JSON response
            .then(data => {
                if (data.success) {
                    fetchNewDataAndRender();  // Reload or refresh data after deletion
                } else {
                    alert(data.message || 'เกิดข้อผิดพลาดในการลบรายการ');
                }
            })
            .catch(error => {
                console.error('เกิดข้อผิดพลาดในการลบรายการ:', error);
            });
    }
}

    // Fetch initial data
    fetchNewDataAndRender();
</script>
</body>
</html>

