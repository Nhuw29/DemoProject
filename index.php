<?php
    require_once('model/connect.php');
    $prd = 0;
    if (isset($_SESSION['cart']))
    {
        $prd = count($_SESSION['cart']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logoShop.png">
    <link rel="stylesheet" type="text/css" href="admin/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <!-- button top -->
    <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- Header -->
    <?php include("model/header.php"); ?>
    <!-- /header -->

    <div class="main">
        <!-- slide -->
        <?php include("model/slide.php"); ?>

        <!-- Banner -->
        <?php include("model/banner.php"); ?>
        <!-- /banner -->

        <!-- Content -->
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="product-main">
                        <!-- sản phẩm mới -->
                        <div class="title-product-main">
                            <h3 class="section-title">Sản phẩm mới</h3> 
                        </div>
                        <div class="content-product-main">
                            <div class="row">
                                <?php
                                    $sql = "SELECT id,image,name,price FROM products WHERE category_id=3 AND status = 0";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($result as $kq) {
                                ?>
                                <div class="col-md-3 col-sm-6 text-center">
                                    <div class="thumbnail">
                                        <div class="hoverimage1">
                                            <img src="<?php echo $kq['image']; ?>" alt="Generic placeholder thumbnail"
                                                width="100%" height="300">
                                        </div>
                                        <div class="name-product">
                                            <?php echo $kq['name']; ?>
                                        </div>
                                        <div class="price">
                                            Giá: <?php echo number_format($kq['price'], 0, ',', '.'); ?><sup> đ</sup>
                                        </div>
                                        <div class="product-info">
                                            <a href="addcart.php?id=<?php echo $kq['id']; ?>">
                                                <button type="button" class="btn btn-primary">
                                                    <label style="color: red;">&hearts;</label> Mua hàng <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                            <a href="detail.php?id=<?php echo $kq['id']; ?>">
                                                <button type="button" class="btn btn-primary">
                                                    <label style="color: red;">&hearts;</label> Chi Tiết <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Thời Trang Nam -->
                        <div class="title-product-main">
                            <h3 class="section-title">Thời Trang Nam</h3>
                        </div>
                        <div class="content-product-main">
                            <div class="row">
                                <?php
                                    $sql = "SELECT id,image,name,price FROM products WHERE category_id=1 LIMIT 8";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($result as $kq) {
                                ?>
                                <div class="col-md-3 col-sm-6 text-center">
                                    <div class="thumbnail">
                                        <div class="hoverimage1">
                                            <img src="<?php echo $kq['image']; ?>" alt="Generic placeholder thumbnail"
                                                width="100%" height="300">
                                        </div>
                                        <div class="name-product">
                                            <?php echo $kq['name']; ?>
                                        </div>
                                        <div class="price">
                                            Giá: <?php echo number_format($kq['price'], 0, ',', '.'); ?><sup> đ</sup>
                                        </div>
                                        <div class="product-info">
                                            <a href="addcart.php?id=<?php echo $kq['id']; ?>">
                                                <button type="button" class="btn btn-primary">
                                                    <label style="color: red;">&hearts;</label> Mua hàng <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                            <a href="detail.php?id=<?php echo $kq['id'] ?>">
                                                <button type="button" class="btn btn-primary">
                                                    <label style="color: red;">&hearts;</label> Chi Tiết <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Thời Trang Nữ -->
                        <div class="title-product-main">
                            <h3 class="section-title">Thời Trang Nữ</h3>
                        </div>
                        <div class="content-product-main">
                            <div class="row">
                                <?php
                                    $sql = "SELECT id,image,name,price FROM products WHERE category_id=2 LIMIT 8";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($result as $kq) {
                                ?>
                                <div class="col-md-3 col-sm-6 text-center">
                                    <div class="thumbnail">
                                        <div class="hoverimage1">
                                            <img src="<?php echo $kq['image']; ?>" alt="Generic placeholder thumbnail"
                                                width="100%" height="300">
                                        </div>
                                        <div class="name-product">
                                            <?php echo $kq['name']; ?>
                                        </div>
                                        <div class="price">
                                            Giá: <?php echo number_format($kq['price'], 0, ',', '.'); ?><sup> đ</sup>
                                        </div>
                                        <div class="product-info">
                                            <a href="addcart.php?id=<?php echo $kq['id']; ?>">
                                                <button type="button" class="btn btn-primary">
                                                    <label style="color: red;">&hearts;</label> Mua hàng <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                            <a href="detail.php?id=<?php echo $kq['id'] ?>">
                                                <button type="button" class="btn btn-primary">
                                                    <label style="color: red;">&hearts;</label> Chi Tiết <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- partner -->
        <div class="container">
            <?php include("model/partner.php"); ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include("model/footer.php"); ?>
</body>
</html>