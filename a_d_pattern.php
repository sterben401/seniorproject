<?php

    session_start();
    require_once 'config/db.php';
    $typeA = "";
    $_SESSION['error404']="";
    $_SESSION['nameType'] = "Pattern ที่คุณเลือกคือ :";

    if (isset($_POST['btn_select']) && !empty($_POST['typePt'])) {
        $_SESSION['type'] = $_POST['typePt'];
        $_SESSION['nameType'] = "Pattern ที่คุณเลือกคือ " . $_SESSION['type'] . " :";
        $_SESSION['error404'] = "โปรดระบุ Pattern " . $_SESSION['type'] . " ...";
        header("location: pattern.php");
        exit();
    }else{
        $_SESSION['error404'] = "โปรดระบุชนิดของ Pattern ที่ต้องการจะบันทึกตรงช่อง ระบุประเภทของ Pattern!";

        header("location: pattern.php");
    }
    
    
    
    /*
    if (isset($_POST['sql'])) {
        $_SESSION['nameType'] = "INPUT SQL Injection :";
        $_SESSION['type'] = "sql";
        $_SESSION['error404'] = "Pattern ที่คุณเลือกคือ SQL Injection";
        header("location: pattern.php");
        exit();
    }

    if (isset($_POST['rce'])) {
        $_SESSION['nameType'] = "INPUT Remote Code Execution (RCE) :";
        $_SESSION['type'] = "rce";
        $_SESSION['error404'] = "Pattern ที่คุณเลือกคือ Remote Code Execution (RCE)";
        header("location: pattern.php");
        exit();;
    }

    if (isset($_POST['xss'])) {
        $_SESSION['nameType'] = "INPUT Cross-site Scripting (XSS) :";
        $_SESSION['type'] = "xss";
        $_SESSION['error404'] = "Pattern ที่คุณเลือกคือ Cross-site Scripting (XSS)";
        header("location: pattern.php");
        exit();
    }*/

    //เมื่อกดปุ่ม Add
    if (isset($_POST['add'])) {
        $typeA = $_SESSION['type'];
    
        // Check if pattern is set
        if (!empty($_POST['pattern'])) {
            $patterns = explode("\n", $_POST['pattern']);
            foreach ($patterns as $pattern) {
                // Check if type is set
                if (!empty($typeA)) {
                    // Ensure the input pattern is not empty
                    $inputPattern = trim($pattern);
                    if (!empty($inputPattern)) {
                        $inputPatternMd5 = md5($inputPattern);
                        try {
                            // Check if pattern already exists
                            $stmt = $conn->prepare("SELECT * FROM pattern WHERE type=:type AND namePattern=:pattern");
                            $stmt->bindParam(':type', $typeA);
                            $stmt->bindParam(':pattern', $inputPattern);
                            $stmt->execute();
    
                            if ($stmt->rowCount() == 0) { // If pattern doesn't exist, insert into database
                                // Insert the MD5 hash into the 'md5' column
                                $stmt = $conn->prepare("INSERT INTO pattern (type, namePattern, md5) VALUES (:type, :pattern, :md5)");
                                $stmt->bindParam(':type', $typeA);
                                $stmt->bindParam(':pattern', $inputPattern);
                                $stmt->bindParam(':md5', $inputPatternMd5); // Store the MD5 hash
                                $stmt->execute();
                            }
                            $_SESSION['error404'] = "บันทึก Pattern สำเร็จ!";
                        } catch (PDOException $e) {
                            $_SESSION['error404'] = "พบข้อผิดพลาดในการบันทึก Pattern: " . $e->getMessage();
                            header("location: pattern.php");
                            exit();
                        }
                    } else {
                        $_SESSION['error404'] = "โปรดระบุ Pattern ที่ต้องการจะบันทึก...";
                        header("location: pattern.php");
                        exit();
                    }
                } else {
                    $_SESSION['error404'] = "โปรดระบุ Pattern ที่ต้องการจะบันทึก...";
                    header("location: pattern.php");
                    exit();
                }
            }
    
            header("location: pattern.php");
            exit();
        } else {
            $_SESSION['error404'] = "โปรดระบุชนิดของ Pattern ที่ต้องการจะบันทึกตรงช่องระบุประเภทของ Pattern!";
            header("location: pattern.php");
            exit();
        }
    }



    

    

?>
