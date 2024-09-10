<?php 

    session_start();
    require_once 'config/db.php';

    if (empty($_POST['clear'])) {
        $_SESSION['error2'] = "กรุณากรอกข้อมูลในช่องค้นหา";
        //$_SESSION['error'] = "Email นี้ไม่พบในฐานข้อมูล กรุณากรอกใหม่อีกครั้ง!";
        $_SESSION['firstname'] = " ";
        $_SESSION['lastname'] = " ";
        $_SESSION['email'] = "";
        $_SESSION['password'] = "";
        $_SESSION['phone'] = "";
        $_SESSION['description'] = "";
        $_SESSION['status'] = $result['urole'];
        if (isset($_SESSION['status']) && $_SESSION['status'] == "admin"){
          $_SESSION['status2'] = " ";
        } else if (isset($_SESSION['status']) && $_SESSION['status'] == "user"){
          $_SESSION['status2'] = " ";
        } else {
          $_SESSION['status2'] = " ";
        }
        $_SESSION['error'] = null;
        $_SESSION['error2'] = null;
        header("location: edit.php");
        exit();
    }


?>