<?php
    //session_start();
    require_once 'config/db.php';
?>
   
<script>
    setInterval(function() {
        <?php $_SESSION['countSQL'] = 0; ?>
        <?php $_SESSION['countRCE'] = 0; ?>
        <?php $_SESSION['countXSS'] = 0; ?>
    }, 1000); // รีเฟรชทุกๆ 1 วินาที
</script>

<?php 
    $sql = "SELECT COUNT(namePattern) as total, type FROM pattern GROUP BY type";
    // ส่ง query ไปยังฐานข้อมูลโดยใช้ PDO prepare statement
    $stmt = $conn->prepare($sql);
    // ประมวลผล query และดึงข้อมูลมาใส่ในตัวแปร $result
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // นับจำนวนข้อมูลแต่ละประเภทและเก็บไว้ใน $_SESSION
    foreach ($result as $row) {
        $type = $row['type'];
        $count = $row['total'];
        if ($type == 'sql') {
            $_SESSION['countSQL'] = $count;
        } elseif ($type == 'rce') {
            $_SESSION['countRCE'] = $count;
        } elseif ($type == 'xss') {
            $_SESSION['countXSS'] = $count;
        }
    }
?>