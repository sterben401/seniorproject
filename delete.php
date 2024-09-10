<?php 

    session_start();
    require_once 'config/db.php';

    if (isset($_POST['delete']) && isset($_POST['query'])) {
        $firstname = $_POST['firstnameEdit'];
        $lastname = $_POST['lastnameEdit'];
        $email = $_POST['emailEdit'];
        $password = $_POST['passwordEdit'];
        //$c_password = $_POST['c_password'];
        $phone = $_POST['phoneEdit'];
        $description = $_POST['descriptionEdit'];
        $urole = $_POST['status'];
    
        $id = "SELECT id FROM users WHERE lastname = :lastname";
        $stmtId = $conn->prepare($id);
        $stmtId->bindParam(':lastname', $lastname);
        $stmtId->execute(); // execute statement ที่ดึงค่า id ออกมาใช้ในการลบ
    
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $stmtId->fetch(PDO::FETCH_ASSOC)['id']); // bind parameter ด้วย id ที่ดึงมาจาก statement ที่ execute แล้ว
    
        if ($stmt->execute()) {
            $_SESSION['success'] = "ลบข้อมูลสำเร็จ";
            header("location: edit.php");
        } else {
            $_SESSION['error'] = "ลบข้อมูลไม่สำเร็จ ";
            header("location: edit.php");
        }
    }else {
        $_SESSION['error2'] = "โปรดระบุข้อมูล Email ตรงช่องค้นหาก่อน";
        header("location: edit.php");
    }


?>