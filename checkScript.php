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
    <title>Pattern Detected</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: #FFD700;
            color: #212529;
        }
        .btn-custom:hover {
            background-color: #F5C300;
        }
           body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.5), rgba(0, 0, 0, 0.5)); /* ไล่สีจากสีเหลืองไปสีดำ */
            background-size: cover; /* ปรับขนาดให้เต็มพื้นที่ */
            margin: 0; /* ลบ margin ของ body */
            height: 100vh; /* ให้ความสูงของ body เป็น 100% ของ viewport */

	font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #212529; /* สีพื้นหลังของ Navbar เป็นสีเข้ม */
            border-bottom: 3px solid #FFD700; /* ขอบล่างสีทอง */
        }

        .navbar-brand {
            color: #212121; /* สีข้อความของ Navbar */
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-brand .admin {
            color: #FFD700; /* สีทองเข้มสำหรับ (ADMIN) */
        }

        .navbar-brand .system {
            color: #212121; /* สีเทาเข้มสำหรับ A Brute Force Attack Monitoring System */
        }

        .navbar-nav .nav-link {
            color: #FFD700; /* สีข้อความลิงค์ใน Navbar */
            font-weight: bold;
            background-color: #343a40; /* สีพื้นหลังของลิงค์ใน Navbar */
            border-radius: 5px; /* มุมโค้งมน */
            padding: 10px 15px; /* ขนาดของปุ่ม */
            transition: background-color 0.3s; /* เปลี่ยนสีพื้นหลังเมื่อ Hover */
        }

        .navbar-nav .nav-link.active {
            color: #FFFFFF; /* สีข้อความลิงค์ที่เลือกใน Navbar */
            background-color: #FFD700; /* สีพื้นหลังของลิงค์ที่เลือก */
        }

        .navbar-nav .nav-link:hover {
            background-color: #FFD700; /* สีพื้นหลังเมื่อ Hover */
            color: #343a40; /* สีข้อความเมื่อ Hover */
        }

        .container {
            margin-top: 30px;
            background-color: #FFFFFF; /* สีพื้นหลังของการ์ด */
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* เงาของการ์ด */
        }

        .card {
            border-radius: 15px;
            border: 1px solid #DCDCDC; /* เส้นขอบการ์ดสีเทาอ่อน */
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {•••••••••
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-weight: bold;
            color: #FFD700; /* สีหัวข้อการ์ด */
        }

        .btn-lg {
            margin-top: 10px;
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-warning {
            background-color: #FFD700; /* สีพื้นหลังปุ่ม Warning */
        }

        .btn-warning:hover {
            background-color: #F5C300; /* สีพื้นหลังปุ่ม Warning เมื่อ Hover */
        }

        .btn-success {
            background-color: #A9A9A9; /* สีพื้นหลังปุ่ม Success */
        }

        .btn-success:hover {
            background-color: #6C6C6C; /* สีพื้นหลังปุ่ม Success เมื่อ Hover */
        }

        .btn-primary {
            background-color: #FFD700; /* สีพื้นหลังปุ่ม Primary */
        }

        .btn-primary:hover {
            background-color: #F5C300; /* สีพื้นหลังปุ่ม Primary เมื่อ Hover */
        }

        .text-primary {
            color: #FFD700 !important;
        }

        h3 {
            font-weight: bold;
        }

        .fa-icon {
            margin-right: 10px;
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
    <h1 class="text-center">การเรียกใช้งาน Pattern Detected</h1>
    <div class="text-center">
        <button class="btn btn-warning btn-lg" id="runScript">รัน Pattern Detected</button>
    </div>
    
    <!-- ตัวนับเวลาจะแสดงที่นี่ -->
    <div id="countdownDisplay" class="mt-4 text-center" style="font-size: 1.5em; color: red;"></div>

    <div id="output" class="mt-4">
        <h4>ผลลัพธ์:</h4>
        <pre id="resultText" style="background: #e9ecef; padding: 10px; border-radius: 5px;"></pre>
    </div>
</div>
<script>
let isButtonDisabled = false; // ตัวแปรเพื่อควบคุมสถานะปุ่ม
let countdown; // ตัวแปรสำหรับเก็บการนับถอยหลัง

// โหลดสถานะจาก localStorage
if (localStorage.getItem('countdown') && localStorage.getItem('isButtonDisabled')) {
    countdown = parseInt(localStorage.getItem('countdown'));
    isButtonDisabled = localStorage.getItem('isButtonDisabled') === 'true';
    console.log('Loaded state:', { countdown, isButtonDisabled }); // แสดงสถานะที่โหลด
    updateUI();
}

// ฟังก์ชันอัปเดต UI ตามสถานะ
function updateUI() {
    const button = document.getElementById("runScript");
    button.disabled = isButtonDisabled; // ปรับสถานะของปุ่ม

    console.log('Update UI:', { isButtonDisabled, countdown }); // แสดงสถานะก่อนอัปเดต UI

    if (isButtonDisabled) {
        showLoader();
        const minutesLeft = Math.floor(countdown / 60);
        const secondsLeft = countdown % 60;
        document.getElementById("countdownDisplay").innerText = `กรุณารอ ${minutesLeft} นาที ${secondsLeft} วินาที`;
        startCountdown(); // เริ่มนับถอยหลัง
    } else {
        hideLoader();
    }
}

document.getElementById("runScript").addEventListener("click", function() {
    if (!isButtonDisabled) {
        console.log('Button clicked, running script.'); // แสดงข้อความเมื่อปุ่มถูกกด
        runLogModsec3();
        //startCooldown(1); // เริ่มตัวนับเวลา 1 นาที
    } else {
        const minutesLeft = Math.floor(countdown / 60);
        const secondsLeft = countdown % 60;
        document.getElementById("countdownDisplay").innerText = `กรุณารอ ${minutesLeft} นาที ${secondsLeft} วินาที`;
    }
});

function runLogModsec3() {
    showLoader(); // แสดง loader
    console.log('Running log modsec3 script...'); // แสดงข้อความเมื่อเริ่มรันสคริปต์
    fetch('run_log_modsec3.php')
        .then(response => response.json())
        .then(data => {
            hideLoader(); // ซ่อน loader
            if (data.success) {
                document.getElementById("resultText").innerText = data.output; // Show output
                console.log('Script output:', data.output); // แสดงผลลัพธ์ของสคริปต์
            } else {
                document.getElementById("resultText").innerText = 'เกิดข้อผิดพลาด: ' + data.error; // Show error
                console.error('Script error:', data.error); // แสดงข้อผิดพลาด
            }
        })
        .catch(error => {
            hideLoader(); // ซ่อน loader
            console.error('เกิดข้อผิดพลาดในการเรียกใช้สคริปต์:', error); // แสดงข้อผิดพลาดในการเรียกใช้สคริปต์
            document.getElementById("resultText").innerText = 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์';
        });
}

function showLoader() {
    const button = document.getElementById("runScript");
    button.innerHTML = 'กำลังรัน...'; // เปลี่ยนข้อความของปุ่ม
    button.disabled = true; // ปิดปุ่ม
    localStorage.setItem('isButtonDisabled', 'true'); // เก็บสถานะปุ่ม
    console.log('Loader shown, button disabled.'); // แสดงข้อความเมื่อโหลดเสร็จ
}

function hideLoader() {
    const button = document.getElementById("runScript");
    button.innerHTML = 'รัน LogModsec4.java'; // คืนค่าข้อความปุ่ม
    button.disabled = false; // เปิดปุ่ม
    localStorage.setItem('isButtonDisabled', 'false'); // เก็บสถานะปุ่ม
    console.log('Loader hidden, button enabled.'); // แสดงข้อความเมื่อซ่อนโหลด
}

function startCooldown(minutes) {
    isButtonDisabled = true; // ปิดการใช้งานปุ่ม
    countdown = minutes * 60; // ตั้งเวลานับถอยหลังเป็นวินาที
    localStorage.setItem('countdown', countdown); // เก็บค่าตัวนับเวลา
    console.log('Cooldown started for', minutes, 'minutes.'); // แสดงข้อความเมื่อเริ่มตัวนับเวลา
    startCountdown(); // เริ่มนับถอยหลัง
}

function startCountdown() {
    if (countdown > 0) {
        const minutesLeft = Math.floor(countdown / 60);
        const secondsLeft = countdown % 60;

        document.getElementById("countdownDisplay").innerText = `กรุณารอ ${minutesLeft} นาที ${secondsLeft} วินาที`;
        countdown--; // ลดค่าตัวนับถอยหลัง
        localStorage.setItem('countdown', countdown); // อัปเดตค่าตัวนับเวลา

        console.log('Countdown running:', { countdown }); // แสดงค่าตัวนับถอยหลังในแต่ละวินาที

        setTimeout(startCountdown, 1000); // เรียกใช้งานฟังก์ชันอีกครั้งหลังจาก 1 วินาที
    } else {
        isButtonDisabled = false; // เปิดการใช้งานปุ่ม
        localStorage.setItem('isButtonDisabled', 'false'); // เก็บสถานะปุ่ม
        document.getElementById("countdownDisplay").innerText = ''; // เคลียร์ข้อความเมื่อหมดเวลา
        localStorage.removeItem('countdown'); // ลบข้อมูล countdown เมื่อหมดเวลา
        console.log('Countdown finished, button enabled.'); // แสดงข้อความเมื่อการนับถอยหลังเสร็จสิ้น
    }
}
</script>

</body>
