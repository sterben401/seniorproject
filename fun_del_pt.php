<?php 

    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['admin_login'])) {
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: signin.php');
    }
    
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(ADD/DELETE) A Brute Force Attack Monitoring System from Web Access Log Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
</head>
<body>
    <div class="container">
        <form action="a_d_pt2.php" method="POST">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                        <h1 class="nav-link active" href="#">Pattern</h1>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                                <div class="navbar-nav">
                                    <h3 class="nav-link active" aria-current="page" href="#">Home</h3>
                                    <h3 class="nav-link active" aria-current="page" href="#">Profile</h3>
                                    <a class="btn btn-danger" aria-current="page" href="logout.php">Logout</a>
                                        
                                </div>
                            </div>
                    </div>
            </nav><hr>
        
            <label for="text">ค้นหา :</label>
            <input type="text" name="query2" placeholder="กรอกประเภท Pattern เท่านั้น..." style="width: 255px">
            <button name="btn_search" type="submit">ค้นหา</button>
            
            <div class="mb-3">
                <strong  class="me-auto"><?php if (isset($_SESSION['nameType2'])) echo $_SESSION['nameType2'];?>  </strong>
                <textarea name="pattern" class="form-control" id="exampleFormControlTextarea1" rows="20" placeholder=" <?php echo isset($_SESSION['error404']) ? 'โปรดระบุชนิดของ Pattern ที่ต้องการจะบันทึกตรงช่องระบุประเภทของ Pattern!' : '';?>">
                <?php
                    if (isset($_SESSION['del_start']) == 'start') {
                        foreach ($_SESSION['patterns'] as $key => $pattern) {
                            if ($key == 0) {
                                $pattern = preg_replace('/^\s+/', '', $pattern);
                            }
                            $trimmedPattern = preg_replace('/^\s+/', '', $pattern);
                            echo $trimmedPattern . "\n";
                        }
                    }
                    if (isset($_POST['btn_search']) && empty($_POST['query2'])){
                        echo "โปรดระบุชนิดของ Pattern ที่ต้องการจะบันทึกตรงช่องระบุประเภทของ Pattern!";
                    }
                ?>
                </textarea>
            </div>
            <hr>
        
            <div class="form-outline" style="display: flex; align-items: center;">
                <div style="display: inline-block;">
                    <textarea name="input_del" class="form-control" id="textAreaExample1" rows="4" style="font-size: 14px; height: 80px; width: 550px" placeholder="<?php echo $_SESSION['delPattern']; ?>"></textarea>
                    <label class="form-label" for="textAreaExample">***คำแนะนำ***  <br>copy pattern จากด้านบนมาวางไว้ในช่อง delete เมื่อเสร็จสิ้นให้กดปุ่ม Delete สีแดง</label>
                </div>
                <button name="btn_delete" class="btn btn-danger" style="text-align: left; margin-left: 16px;" type="submit">Delete</button>
            </div>

            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                
                <button name = "btn_clear" class="btn btn-light me-md-2" type="submit">Clear</button>
                <button name = "btn_save"class="btn btn-primary" type="submit">Save</button>
            </div>
            


            <hr>
            <div class="d-grid gap-2 col-6 mx-auto">
                <a type="button" class="btn btn-danger btn-lg" href="pattern.php" >Back</a>
            </div>
        </form>
    </div>
    
</body>
</html>