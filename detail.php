<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="Fashion MyLiShop - fashion mylishop"/>
    <meta name="description" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="keywords" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="author" content="Hôih My" />
    <meta name="author" content="Y Blir" />
    <link rel="icon" type="image/png" href="images/logoShop.png">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" type="text/css" href="admin/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js" charset="utf-8"></script> -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'> -->

    <!-- customer js -->
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>
    <!-- customer css -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">

</head>
<body>
    <!-- button top -->
    <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>
    
<?php
    require_once("model/connect.php");
    include 'model/header.php';
    error_reporting(2);

    // Kiểm tra và validate ID sản phẩm
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo '<div class="container"><div class="alert alert-danger">ID sản phẩm không hợp lệ!</div></div>';
        include 'model/footer.php';
        exit();
    }
    
    $product_id = (int)$_GET['id'];
    
    try {
        // Sử dụng Prepared Statement để tránh SQL Injection
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Kiểm tra xem có sản phẩm không
        if ($stmt->rowCount() == 0) {
            echo '<div class="container"><div class="alert alert-warning">Sản phẩm không tồn tại!</div></div>';
            include 'model/footer.php';
            exit();
        }
        
        // Lấy dữ liệu sản phẩm
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Xử lý hình ảnh
        $thum_Image = (!empty($row['image']) && $row['image'] != '') ? $row['image'] : "images/no-image.jpg";
        
        // Xử lý giá
        $price = (float)$row['price'];
        $salePercent = (float)$row['saleprice'];
        $hasSale = ($salePercent > 0 && $salePercent <= 100);
        $salePrice = $hasSale ? ($price - ($price * $salePercent / 100)) : $price;
        
?>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="detail-product">
                            <div class ="row">
                                <div class ="col-md-5 col-sm-6 col-xs-12">
                                    <div class ="product-frame">
                                        <div class="" style="margin-bottom: 20px; margin-top: 10px;">
                                            <img src="<?php echo htmlspecialchars($thum_Image); ?>" 
                                                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                                 width="100%" 
                                                 height="450"
                                                 onerror="this.src='images/no-image.jpg'">
                                                 <!-- nếu ảnh lỗi, dùng ảnh mặc định -->
                                        </div>
                                    </div>
                                </div>
                                <!-- // hình ảnh -->

                                <div class="col-md-7 col-xs-6 col-xs-12">
                                    <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                                    <hr>
                                    <?php
                                        if ($hasSale) {
                                    ?>
                                        <p class="price">
                                            <span style="text-decoration: line-through; color: #999;">
                                                Giá cũ: <?php echo number_format($price, 0, ',', '.'); ?><sup>đ</sup>
                                            </span>
                                        </p>
                                        <p class="price" style="color: #d9534f; font-weight: bold;">
                                            <span style="background: #ffebee; padding: 5px 10px; border-radius: 3px;">
                                                Giảm <?php echo $saleprice; ?>%: 
                                                <?php echo number_format($salePrice, 0, ',', '.'); ?><sup>đ</sup>
                                            </span>
                                        </p>
                                    <?php
                                        } else {
                                    ?>
                                        <p class="price" style="color: #333; font-weight: bold;">
                                            Giá sản phẩm: <?php echo number_format($price, 0, ',', '.'); ?><sup>đ</sup>
                                        </p>
                                    <?php
                                        }
                                    ?>
                                    <hr>
                                    <div class="button-order">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="addcart.php?id=<?php echo $product_id; ?>" class="btn-add-cart">
                                                    <button class="btn btn-warning btn-md"> 
                                                        <i class="fa fa-shopping-cart"></i> Đặt mua 
                                                    </button>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="number" 
                                                           name="quantity" 
                                                           class="form-control" 
                                                           placeholder="Số lượng" 
                                                           value="1" 
                                                           min="1"
                                                           id="productQuantity">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p style="padding-top: 30px;">
                                        <span class="fa fa-check-circle text-success"></span>&nbsp;&nbsp;&nbsp;GIAO HÀNG TOÀN QUỐC<br>
                                        <span class="fa fa-check-circle text-success"></span>&nbsp;&nbsp;THANH TOÁN KHI NHẬN HÀNG<br>
                                        <span class="fa fa-check-circle text-success"></span>&nbsp;&nbsp;ĐỔI HÀNG TRONG 15 NGÀY
                                    </p>
                                    
                                    <?php if (!empty($row['description'])): ?>
                                    <div class="product-description" style="margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                                        <h4><i class="fa fa-info-circle"></i> Mô tả sản phẩm</h4>
                                        <hr>
                                        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div><!-- /.col -->
                                <!-- // Thông tin sản phẩm -->
                            </div><!-- /.row -->
                        </div><!-- /.detail-product -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container -->
    <?php
        
    } catch(PDOException $e) {
        // Hiển thị lỗi cho developer (nên tắt trong môi trường production)
        echo '<div class="container"><div class="alert alert-danger">Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.</div></div>';
        // Ghi log lỗi
        error_log("PDO Error: " . $e->getMessage());
    }
    
    include 'model/footer.php';
?>
<script>
    new WOW().init();
    
    // JavaScript để xử lý số lượng sản phẩm
    $(document).ready(function() {
        // Cập nhật link thêm vào giỏ hàng với số lượng
        $('.btn-add-cart').click(function(e) {
            e.preventDefault();
            var quantity = $('#productQuantity').val();
            var url = $(this).attr('href') + '&quantity=' + quantity;
            window.location.href = url;
        });
    });
</script>
</body>
</html>