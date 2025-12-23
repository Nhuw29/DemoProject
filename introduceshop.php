<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('model/connect.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Giới Thiệu - Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logohong.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>
    <?php include 'model/header.php'; ?>

    <div class="main">
        <div class="container">
            <div class="intro-section wow fadeInUp" data-wow-delay="0.2s">
                <h2>Câu Chuyện Của AliceShop</h2>
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 story-content">
                        <p>Ra đời từ năm 2017, chỉ từ một cửa hàng thời trang nhỏ, đến nay <strong>AliceShop</strong> đã vươn lên trở thành một hệ thống cửa hàng chuyên kinh doanh thời trang trẻ, khẳng định vị thế là thương hiệu hàng đầu tại Đà Nẵng. Chúng tôi tự hào mang đến những sản phẩm không chỉ đẹp về mẫu mã mà còn chất lượng trong từng đường may, giúp các bạn trẻ tự tin thể hiện cá tính và phong cách riêng.</p>
                        <p>Với niềm đam mê thời trang bất tận, đội ngũ AliceShop luôn không ngừng sáng tạo và cập nhật những xu hướng mới nhất trên thế giới để mỗi sản phẩm trao đến tay khách hàng đều là một tác phẩm thời trang độc đáo và ấn tượng.</p>
                    </div>
                </div>
            </div>

            <div class="intro-section values-section wow fadeInUp" data-wow-delay="0.4s">
                <h2>Giá Trị Cốt Lõi</h2>
                <div class="row">
                    <div class="col-md-3 col-sm-6 value-item"><i class="fa fa-diamond"></i><h4>Chất Lượng</h4><p>Sản phẩm được tuyển chọn kỹ lưỡng từ chất liệu đến kiểu dáng.</p></div>
                    <div class="col-md-3 col-sm-6 value-item"><i class="fa fa-users"></i><h4>Khách Hàng</h4><p>Sự hài lòng của bạn là ưu tiên hàng đầu của chúng tôi.</p></div>
                    <div class="col-md-3 col-sm-6 value-item"><i class="fa fa-lightbulb-o"></i><h4>Sáng Tạo</h4><p>Luôn cập nhật và dẫn đầu các xu hướng thời trang mới.</p></div>
                    <div class="col-md-3 col-sm-6 value-item"><i class="fa fa-heart"></i><h4>Đam Mê</h4><p>Tình yêu với thời trang là động lực để chúng tôi phát triển.</p></div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'model/footer.php'; ?>

    <script>
        new WOW().init();
    </script>
</body>
</html>







    <style>
        body {
            background-color: #f9f9f9;
        }
        .intro-header {
            background: url('https://images.unsplash.com/photo-1583366859346-5449c4599a2a?q=80&w=2070') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
        }
        .intro-header:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .intro-header .container {
            position: relative;
            z-index: 2;
        }
        .intro-header h1 {
            font-size: 48px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        .intro-section {
            padding: 60px 0;
        }
        .intro-section h2 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: bold;
            color: #333;
            position: relative;
            padding-bottom: 15px;
        }
        .intro-section h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #ff0066;
        }
        .story-content p {
            font-size: 16px;
            line-height: 1.8;
            color: #555;
            text-align: justify;
        }
        .values-section {
            background-color: #fff;
        }
        .value-item {
            text-align: center;
            padding: 20px;
        }
        .value-item .fa {
            font-size: 40px;
            color: #ff0066;
            margin-bottom: 20px;
        }
        .value-item h4 {
            font-weight: bold;
            color: #333;
        }
        .value-item p {
            color: #666;
        }
        .why-us-section ul {
            list-style: none;
            padding-left: 0;
        }
        .why-us-section ul li {
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .why-us-section ul li .fa {
            color: #28a745;
            font-size: 20px;
            margin-right: 15px;
        }
    </style>