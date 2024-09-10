<?php
session_start();
require_once 'config/db.php';

if (isset($_POST['edit'])) {
    $firstname = $_POST['firstnameEdit'];
    $lastname = $_POST['lastnameEdit'];
    $email = $_POST['emailEdit'];
    $password = $_POST['passwordEdit'];
    $phone = $_POST['phoneEdit'];
    $urole = $_POST['status'];
    $description = $_POST['descriptionEdit'];

    // ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่
    if (empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($urole)) {
        $_SESSION['error'] = "โปรดระบุข้อมูลให้ครบถ้วน";
        header("location: edit.php?email=".$email);
        exit();
    }

    // ตรวจสอบว่า email ถูกต้องหรือไม่
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลให้ถูกต้อง";
        header("location: edit.php?email=".$email);
        exit();
    }

    // ทำการบันทึกข้อมูล
    try {
        $idQuery = "SELECT id FROM users WHERE email = :email";
        $stmtId = $conn->prepare($idQuery);
        $stmtId->bindParam(':email', $email);
        $stmtId->execute();
        $id = $stmtId->fetchColumn();

        if ($id) {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, password = :hashedPassword, phone = :phone, description = :description, urole = :urole WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':hashedPassword', $hashedPassword);
            } else {
                $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, phone = :phone, description = :description, urole = :urole WHERE id = :id";
                $stmt = $conn->prepare($sql);
            }

            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':urole', $urole);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $_SESSION['success'] = "แก้ไขข้อมูลเรียบร้อยแล้ว!";
            } else {
                $_SESSION['error'] = "เกิดข้อผิดพลาด!";
            }
        } else {
            $_SESSION['error'] = "ไม่พบผู้ใช้ที่มีอีเมลนี้";
        }

        header("location: edit.php?email=".$email);
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        header("location: edit.php?email=".$email);
    }
}
?>

