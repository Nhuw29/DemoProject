<?php require_once('model/connect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="Fashion MyLiShop - fashion mylishop"/>
    <link rel="icon" type="image/png" href="images/Logooo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">

</head>
<body>
    <!-- button top -->
    <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>
    
    <!-- background -->
    <div class="container-fluid">
        <div id="bg">
            <?php
                $stmt = $conn->prepare("SELECT image FROM slides WHERE id=:id");
                $stmt->bindValue(':id', 1);
                $stmt->execute();
                $result = $stmt;
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <img width="100%" height="100%" src="<?php echo $row['image']; ?>" alt="">
            <?php } ?>
        </div>
    </div><!-- /background -->

    <!-- Header -->
    <?php include("model/header.php"); ?>
    <!-- /header -->

    <div class="main">
        <!-- Content -->
        <div class="container">
        	<div class="row">
            	<div class="col-md-12 col-sm-12 col-xs-12">
                	<div class="product-main">
                        <!-- Thời Trang Nam -->
                        <div class="title-product-main">
                            <h3 class="section-title">Thời Trang Nam</h3>
                        </div>
                        <div class="content-product-main">
                            <div class="row">
                                <?php
                                    $stmt = $conn->prepare("SELECT id,image,name,price FROM products WHERE category_id=:category_id");
                                    $stmt->bindValue(':category_id', 1);
                                    $stmt->execute();
                                    $result = $stmt;
                                    
                                    while ($kq = $result->fetch(PDO::FETCH_ASSOC)) {
                                        
                                ?>                        
                                        <div class="col-md-3 col-sm-6 text-center">
                                            <div class="thumbnail">
                                                <div class="hoverimage1">
                                                    <img src= "<?php echo $kq['image']; ?>" alt="Generic placeholder thumbnail" width="100%" height="300">
                                                </div>
                                                <div class="name-product">
                                                    <?php echo $kq['name']; ?>
                                                </div>
                                                <div class="price">
                                                    Giá: <?php echo $kq['price'].' đ'; ?>
                                                </div>
                                                <div class="product-info">
                                                    <a href="addcart.php?id=<?php echo $kq['id']; ?>">
                                                        <button type="button" class="btn btn-primary">
                                                            <label style="color: red;">&hearts;</label> Mua hàng  <label style="color: red;">&hearts;</label>
                                                        </button>
                                                    </a>
                                                    <a href="detail.php?id=<?php echo $kq['id']; ?>">
                                                        <button type="button" class="btn btn-primary">
                                                            <label style="color: red;">&hearts;</label> Chi Tiết <label style="color: red;">&hearts;</label>
                                                        </button>
                                                    </a>
                                                </div><!-- /product-info -->
                                            </div><!-- /thumbnail -->
                                        </div><!-- /col -->
                                    <?php } ?>
                            </div><!-- /row -->
                        </div><!-- /Thời Trang Nam -->
            
                	</div> <!-- /product-main -->
            	</div> <!-- /col -->

            </div><!-- /row -->
        </div><!-- /container -->
    </div>
    <!-- /main -->

    <!-- partner -->
    <div class="container">
        <div class="title-product-main">
            <h3 class="section-title">Hãng Thời Trang Nổi Tiếng</h3>
        </div>
        <?php include("model/partner.php"); ?>
    </div>
    <!-- /partner -->

    <!-- footer -->
    <div class="container">
        <?php include("model/footer.php"); ?>
    </div>
    <!-- /footer -->

    
<script>
    new WOW().init();
</script>
</body>
</html>