<?php
    session_start();
    $_SESSION['start']="";

    require_once 'config/db.php';
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
    
    if (empty($_POST['query'])) {
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
      header("location: edit.php");
      exit();
    }
    if (isset($_POST['btn_search']) && !empty($_POST['query'])) {
        // รับค่า query จากการค้นหา
        $query = $_POST['query'];
        $_SESSION['start']="start";
        // ค้นหาข้อมูลในฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM users WHERE email LIKE :query");
        $stmt->execute(['query' => '%' . $query . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // แสดงผลลัพธ์การค้นหา
        if (count($results) > 0) {
          foreach ($results as $result) {
            
            $_SESSION['firstname'] = $result['firstname'];
            $_SESSION['lastname'] = $result['lastname'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['password'] = $result['password'];
            $_SESSION['phone'] = $result['phone'];
            $_SESSION['description'] = $result['description'];
            $_SESSION['status'] = $result['urole'];
            if (isset($_SESSION['status']) && $_SESSION['status'] == "admin"){
                $_SESSION['status2'] = "user";
            } else if (isset($_SESSION['status']) && $_SESSION['status'] == "user"){
                $_SESSION['status2'] = "admin";
            }
            $_SESSION['success'] = "นี่คือข้อมูลของ ".$_SESSION['firstname'] . " " . $_SESSION['lastname'];
            header("location: edit.php");
            
          }
        } else {
          $_SESSION['error'] = "Email นี้ไม่พบในฐานข้อมูล กรุณากรอกใหม่อีกครั้ง!";
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
          header("location: edit.php");
        }
    }
?>
