<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css"> 
    <link rel="stylesheet" href="../css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once('connect.php');
    $prd = 0;
    if (isset($_SESSION['cart']))
    {
        $prd = count($_SESSION['cart']);
    }

    if (isset($_GET['ls'])) {
        echo "<script type=\"text/javascript\">alert(\"Bạn đã đăng nhập thành công!\");</script>";
    }
?>

<header>
    <div class="container-fluid header_top wow bounceIn" data-wow-delay="0.1s">
        <!-- <div class="col-sm-10 col-md-10">
            <div class="header_top_left"> <span><i class="fa fa-phone"></i></span> <span>0397 450 200 | 0552 980
                    270</span>&nbsp;&nbsp;&nbsp; <span><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                <span>hoihmy2712@gmail.com</span> </div>
        </div> -->
        <div class="col-sm-2 col-md-2">
            <div class="header_top_right">
                <a href="https://www.facebook.com/" target="_blank" title="facebook"><i class="fa fa-facebook"></i></a>
                <a href="https://twitter.com/" target="_blank" title="twitter"><i class="fa fa-twitter"></i></a>
                <a href="https://www.rss.com/" target="_blank" title="rss"><i class="fa fa-rss"></i></a>
                <a href="https://www.youtube.com/" target="_blank" title="youtube"><i class="fa fa-youtube"></i></a>
                <a href="https://plus.google.com/" target="_blank" title="google"><i class="fa fa-google-plus"></i></a>
                <a href="https://linkedin.com/" target="_blank" title="linkedin"><i class="fa fa-linkedin"></i></a>
            </div>
        </div>
        <div class="clear-fix"></div>
    </div>
    <!-- /header-top -->
    <!-- Menu ngang header -->
    <div class="container">
        <!-- Logo -->
        <div class="title">
            <a href="index.php" title="MyLiShop"> 
                <!-- Dùng 1 logo thống nhất -->
                <img src="images/logoShop.png" width="260px;" height="180px;" alt="MyLiShop Logo"> 
            </a>
        </div>
        <!-- /logo -->
        <div class="col-sm-12 col-md-12 account">
            <div class="row">
                <?php
                    if(isset($_SESSION['username']))
                    {
                ?>
                <i class="fa fa-user fa-lg"></i>
                <span><?php echo $_SESSION['username']?></span> &nbsp;
                <span><i class="fa fa-sign-out"></i><a href="user/logout.php"> Đăng xuất </a></span>
                <?php   }
                    else {
            ?>
                <i class="fa fa-user fa-lg"></i>
                <a href="user/login.php"> Đăng nhập </a> &nbsp;
                <i class="fa fa-users fa-lg"></i>
                <a href="user/register.php"> Đăng ký </a>
                <?php
                    }
                ?>
            </div>
        </div>
        <div class="clearfix"></div>

        <!-- Menu -->
        <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
                    </button>
                    <!-- <a class="navbar-brand" href="#">MyLiShop</a> -->
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a href="index.php"> Trang Chủ </a>
                        </li>
                        <li><a href="introduceshop.php"> Dịch Vụ </a>
                        </li>
                        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sản Phẩm <b
                                    class="fa fa-caret-down"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="fashionboy.php"><i class="fa fa-caret-right"></i> Thời Trang Nam</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="fashiongirl.php"><i class="fa fa-caret-right"></i> Thời Trang Nữ</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="newproduct.php"><i class="fa fa-caret-right"></i> Hàng Mới Về</a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="lienhe.php"> Liên Hệ </a>
                        </li>
                    </ul>
                    <!-- search area -->
                    <ul class="nav navbar-nav navbar-right">
                        <form role="search" action="search.php" method="POST">
                            <div class="input-group header-search">
                                <!-- form search -->
                                <input type="text" maxlength="50" name="search" id="searchs" class="form-control"
                                    placeholder="Nhập từ khóa..." style="font-size: 14px;">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-search" type="submit"><span
                                            class="fa fa-search"></span></button>
                                </span>
                            </div>
                            <!-- /input-group -->
                            <div class="cart-total">
                                <a class="bg_cart" href="view-cart.php" title="Giỏ hàng">
                                    <button type="button" class="btn header-cart">
                                        <span class="fa fa-shopping-cart"></span>&nbsp;
                                        <span id="cart-total">
                                            <?php
                                                if(isset($_SESSION['cart']))
                                                {
                                                    $cart = $_SESSION['cart'];
                                                    $sl = count($_SESSION['cart']);
                                                    echo $sl;
                                                }
                                                else {
                                                    echo "0";
                                                }
                                            ?>
                                        </span> sản phẩm
                                    </button>
                                </a>
                                <div class="mini-cart-content shopping_cart">

                                </div>
                            </div>
                        </form>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </div>
    <!-- /Menu ngang header -->
</header>
<!-- /header -->  
</body>
</html>