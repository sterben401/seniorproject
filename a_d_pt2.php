<?php

    session_start();
    require_once 'config/db.php';
    $typeA = "";
    $_SESSION['error404']="";
    $_SESSION['patterns'] = array();
    $_SESSION['notificationPattern']="";
    $_SESSION['delPattern']="";
    $_SESSION['del_start']="";
    $_SESSION['nameType2']="";

    if (isset($_POST['btn_clear'])){
        $_SESSION['patterns'] = array();
    }

    //ปุ่ม ค้นหา
    if (isset($_POST['btn_search']) && !empty($_POST['query2'])) {
        $_SESSION['type'] = $_POST['query2'];
        $_SESSION['nameType2'] = "นี่คือ Pattern " . $_SESSION['type'] . " ทั้งหมดที่มีอยู่ในฐานข้อมูล :";
        //$_SESSION['error404'] = "โปรดระบุ Pattern " . $_SESSION['type'] . " ...";
        $_SESSION['del_start']="start";
        $typeA = $_SESSION['type'];
        
    
        try {
            $stmt = $conn->prepare("SELECT namePattern FROM pattern WHERE type=:type");
            $stmt->bindParam(':type', $typeA);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $patterns = array();
            foreach ($result as $row) {
                $patterns[] = $row['namePattern'];
            }
    
            $_SESSION['patterns'] = $patterns;

            
            /*
            echo "<ul style='list-style-type: none;'>";
            foreach ($patterns as $pattern) {
                echo "<li>" . $pattern . "</li>";
            }
            echo "</ulstyle=>";
            */
    
            header("location: fun_del_pt.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error404'] = "พบข้อผิดพลาดในการดึง Pattern: " . $e->getMessage();
            header("location: fun_del_pt.php.php");
            exit();
        }
        exit();
    }else {
        $_SESSION['del_start']="stop";
        $_SESSION['error404'] = "โปรดระบุชนิดของ Pattern ที่ต้องการจะบันทึกตรงช่องระบุประเภทของ Pattern!"; 
    }
   
    // ปุ่ม Clear
    if (isset($_POST['btn_search']) && empty($_POST['query2'])){
        $_SESSION['error404'] = "โปรดระบุชนิดของ Pattern ที่ต้องการจะบันทึกตรงช่องระบุประเภทของ Pattern!";
        $_SESSION['nameType2'] = "";
        header("location: fun_del_pt.php");
    }


    //กดปุ่ม Delete
    if (isset($_POST['btn_delete']) && !empty($_POST['input_del'])) {
        $_SESSION['type'] = $_POST['input_del'];
        //$_SESSION['nameType'] = "นี่คือ Pattern " . $_SESSION['type'] . " ทั้งหมดที่มีอยู่ในฐานข้อมูล :";
        
        // Get input pattern value with leading/trailing whitespace and line breaks removed
        $inputPattern = trim($_POST['input_del']);
        $inputPattern = str_replace(array("\r", "\n"), '', $inputPattern);
        
        //$typeA = $_SESSION['type'];
    
        try {
            $stmt = $conn->prepare("SELECT namePattern FROM pattern WHERE type=:type AND namePattern=:namePattern");
            $stmt->bindParam(':type', $typeA);
            $stmt->bindParam(':namePattern', $inputPattern);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $patterns = array();
            foreach ($result as $row) {
                $patterns[] = $row['namePattern'];
            }
    
            $_SESSION['patterns'] = $patterns;
    
            // Delete all rows with matching type and namePattern
            $stmt = $conn->prepare("DELETE FROM pattern WHERE type=:type AND namePattern=:namePattern");
            $stmt->bindParam(':type', $typeA);
            $stmt->bindParam(':namePattern', $inputPattern);
            $stmt->execute();
    
            header("location: fun_del_pt.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error404'] = "พบข้อผิดพลาดในการดึง Pattern: " . $e->getMessage();
            header("location: fun_del_pt.php");
            exit();
        }
    } else {
        $_SESSION['error404'] = "โปรดระบุชนิดของ Pattern ที่ต้องการจะบันทึกตรงช่องระบุประเภทของ Pattern!";
        header("location: fun_del_pt.php");
    }

?>
