<?php
    require_once('model/connect.php');
    
    // Xử lý form submit TRỰC TIẾP trên cùng trang này
    // (Cách này tiện hơn và tránh lỗi chuyển hướng)
    if (isset($_POST['sendcontact'])) 
    {
        $namect = $_POST['contact-name'] ?? '';
        $emailct = $_POST['contact-email'] ?? '';
        $subject = $_POST['contact-subject'] ?? '';
        $contentct = $_POST['contact-content'] ?? '';

        if (!empty($namect) && !empty($emailct) && !empty($subject) && !empty($contentct)) {
            try {
                $sql = "INSERT INTO contacts(name, email, title, contents, created) 
                        VALUES(:name, :email, :title, :contents, NOW())";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':name', $namect, PDO::PARAM_STR);
                $stmt->bindParam(':email', $emailct, PDO::PARAM_STR);
                $stmt->bindParam(':title', $subject, PDO::PARAM_STR);
                $stmt->bindParam(':contents', $contentct, PDO::PARAM_STR);
                
                if ($stmt->execute()) {
                    $success_message = "Gửi liên hệ thành công!";
                } else {
                    $error_message = "Gửi liên hệ thất bại!";
                }
            } catch(PDOException $e) {
                $error_message = "Có lỗi xảy ra: " . $e->getMessage();
            }
        } else {
            $error_message = "Vui lòng điền đầy đủ thông tin!";
        }
    }
    
    // Hiển thị thông báo từ GET parameters (giữ để tương thích)
    $alert_script = "";
    if(isset($_GET['cs'])) {
        $alert_script = "<script type=\"text/javascript\">alert(\"Gửi liên hệ thành công!\");</script>";
    }
    if(isset($_GET['cf'])) {
        $alert_script = "<script type=\"text/javascript\">alert(\"Gửi liên hệ thất bại!\");</script>";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content = "width=device-width, initial-scale =1">
    <meta charset="utf-8">
    <meta name="title" content="Fashion MyLiShop - fashion mylishop"/>
    <meta name="description" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="keywords" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="author" content="Hôih My" />
    <meta name="author" content="Y Blir" />
    <link rel="icon" type="image/png" href="../images/logoShop.png">
    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="../js/mylishop.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
    <script src='../js/wow.js'></script>
    <!-- Bootstrap Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <!-- button top -->
    <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>
    
    <!-- header -->
    <?php include 'model/header.php'; ?>
    <!-- /header -->
    
    <!-- Hiển thị thông báo -->
    <?php 
    if(isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible" style="margin: 15px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible" style="margin: 15px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <?php echo $alert_script; ?>
    
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="../index.php"> Trang chủ </a></li>
            <li> Liên hệ </li>
        </ul><!-- /breadcrumb -->
        <div class= "row">
            <div class= "col-md-12 col-sm-16 col-xs-12">
                <div class= "titles">
                    <center>
                        <h3><strong> THÔNG TIN LIÊN HỆ </strong></h3>
                    </center>
                </div>
                <!-- Sửa action thành trang hiện tại -->
                <form name="form-lien-he" action="lienhe_back.php" method="POST">
                    <div class="contents">
                        <div class="form-group">
                            <label>Họ và tên: <span style="color:#f00">&#42;</span></label>
                            <input name="contact-name" placeholder="Nhập họ tên đầy đủ" class="form-control" required="required" maxlength="255" type="text" id="contact-name" value="<?php echo isset($_POST['contact-name']) ? htmlspecialchars($_POST['contact-name']) : ''; ?>"> 
                        </div>

                        <div class="form-group">
                            <label>Email: <span style="color:#f00">&#42;</span></label>
                            <input name="contact-email" placeholder="Nhập email của bạn" class="form-control" required="required" maxlength="255" type="email" id="contact-email" value="<?php echo isset($_POST['contact-email']) ? htmlspecialchars($_POST['contact-email']) : ''; ?>">  
                        </div>

                        <div class="form-group">
                            <label>Tiêu đề: <span style="color:#f00">&#42;</span></label>
                            <input name="contact-subject" placeholder="Nhập tiêu đề của bạn" class="form-control" required="required" maxlength="255" type="text" id="contact-subject" value="<?php echo isset($_POST['contact-subject']) ? htmlspecialchars($_POST['contact-subject']) : ''; ?>">  
                        </div>
                        
                        <div class="form-group">
                            <label>Nội dung: <span style="color:#f00">&#42;</span></label>
                            <textarea name="contact-content" placeholder="Nhập thông tin cần liên hệ..." class="form-control" id="ContactContent" rows="5" required><?php echo isset($_POST['contact-content']) ? htmlspecialchars($_POST['contact-content']) : ''; ?></textarea>
                        </div>
                        <center><button type="submit" name="sendcontact" class="btn btn-info"> Gửi liên hệ </button></center>
                    </div><!-- /content -->
                </form>
            </div><!-- /col -->
        </div><!-- /row -->
    </div><!-- /container -->

    <!-- Maps -->
    <div class="container-fluid">
        <div class="row">
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d958.5247181884388!2d108.24206672970746!3d16.060358250494478!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31421836ed15dfc9%3A0x99c3cc369a33576c!2sPasserelles+num%C3%A9riques+Vietnam!5e0!3m2!1sen!2s!4v1513938605489" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div><!-- /map -->
        </div><!-- /row -->
    </div><!-- /container-fluid -->
    
<script>
    new WOW().init();
    
    // Xóa thông báo sau 5 giây
    $(document).ready(function(){
        setTimeout(function(){
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
</body>
</html>