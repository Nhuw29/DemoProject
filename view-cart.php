<?php
session_start();
require_once('model/connect.php'); // Thêm kết nối PDO

// Kiểm tra và làm sạch giỏ hàng nếu cần
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // Gộp các sản phẩm trùng nhau dựa trên ID
    $grouped_cart = [];
    foreach ($_SESSION['cart'] as $item) {
        $id = $item['id'];
        if (!isset($grouped_cart[$id])) {
            $grouped_cart[$id] = $item;
        } else {
            // Nếu đã có sản phẩm này, cộng dồn số lượng
            $grouped_cart[$id]['quantity'] += $item['quantity'];
        }
    }
    // Gán lại giỏ hàng đã gộp
    $_SESSION['cart'] = $grouped_cart;
    
    // Loại bỏ các item không hợp lệ
    foreach ($_SESSION['cart'] as $key => $item) {
        if (!isset($item['id'], $item['name'], $item['price'], $item['quantity']) || $item['quantity'] <= 0) {
            unset($_SESSION['cart'][$key]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - NAU_Shop</title>
    <link rel="icon" type="image/png" href="images/logoShop.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <style>
        .cart-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .cart-title {
            color: #ff0066;
            border-bottom: 2px solid #ff0066;
            padding-bottom: 10px;
            margin-bottom: 30px;
            font-weight: 700;
        }
        
        .cart-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #eee;
        }
        
        .cart-item:hover {
            background-color: #f9f9f9;
            transition: 0.3s;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:hover {
            background: #e9ecef;
        }
        
        .quantity-display {
            width: 60px;
            text-align: center;
            border: 1px solid #ddd;
            margin: 0 5px;
            padding: 8px;
            font-weight: bold;
            background: #fff;
            border-radius: 3px;
        }
        
        .btn-checkout {
            background: #ff0066;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            border: none;
            transition: 0.3s;
        }
        
        .btn-checkout:hover {
            background: #e6005c;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart-icon {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .total-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #ff0066;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: 0.3s;
        }
        
        .btn-remove:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .cart-img {
                width: 80px;
                height: 80px;
            }
            
            .quantity-control {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include("model/header.php"); ?>
    
    <div class="container">
        <div class="cart-container">
            <h2 class="cart-title"><i class="fa fa-shopping-cart"></i> GIỎ HÀNG CỦA BẠN</h2>
            
            <?php if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0): ?>
                
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <h3 class="text-muted">Giỏ hàng của bạn đang trống</h3>
                    <p class="text-muted">Hãy chọn sản phẩm bạn yêu thích và thêm vào giỏ hàng!</p>
                    <a href="index.php" class="btn btn-primary btn-lg mt-3">
                        <i class="fa fa-shopping-bag"></i> TIẾP TỤC MUA SẮM
                    </a>
                </div>

            <?php else: ?>
            
                <!-- Thông báo -->
                <?php if (isset($_SESSION['cart_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-check-circle"></i> <?php echo $_SESSION['cart_success']; ?>
                    <?php unset($_SESSION['cart_success']); ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['cart_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['cart_error']; ?>
                    <?php unset($_SESSION['cart_error']); ?>
                </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            $total_items = 0;
                            
                            foreach ($_SESSION['cart'] as $id => $item):
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                                $total_items += $item['quantity'];
                            ?>
                                <tr class="cart-item align-middle text-center">
                                    <td>
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="cart-img">
                                    </td>
                                    <td class="text-left">
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="text-danger font-weight-bold">
                                            <?php echo number_format($item['price']); ?> đ
                                        </span>
                                    </td>
                                    <td>
                                        <div class="quantity-control">
                                            <a href="update-quantity.php?id=<?php echo $id; ?>&action=decrease" 
                                               class="quantity-btn minus" 
                                               title="Giảm số lượng">
                                                <i class="fa fa-minus"></i>
                                            </a>
                                            <span class="quantity-display">
                                                <?php echo $item['quantity']; ?>
                                            </span>
                                            <a href="update-quantity.php?id=<?php echo $id; ?>&action=increase" 
                                               class="quantity-btn plus" 
                                               title="Tăng số lượng">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-danger font-weight-bold">
                                            <?php echo number_format($subtotal); ?> đ
                                        </span>
                                    </td>
                                    <td>
                                        <a href="remove-cart.php?id=<?php echo $id; ?>" 
                                           class="btn-remove btn-sm" 
                                           onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                            <i class="fa fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Chính sách miễn phí vận chuyển:</strong> Áp dụng cho đơn hàng từ 500,000 đ
                        </div>
                        <div class="d-flex">
                            <a href="index.php" class="btn btn-outline-primary mr-2">
                                <i class="fa fa-arrow-left"></i> TIẾP TỤC MUA SẮM
                            </a>
                            <!-- <a href="clear-cart.php" class="btn btn-outline-danger" 
                               onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                                <i class="fa fa-trash"></i> XÓA TẤT CẢ
                            </a> -->
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="total-section">
                            <h4 class="mb-3"><i class="fa fa-file-text"></i> TÓM TẮT ĐƠN HÀNG</h4>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tổng sản phẩm:</span>
                                <strong><?php echo $total_items; ?> sản phẩm</strong>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <strong><?php echo number_format($total); ?> đ</strong>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <strong>
                                    <?php 
                                    $shipping = ($total >= 500000) ? 0 : 30000;
                                    echo ($shipping == 0) ? 'MIỄN PHÍ' : number_format($shipping) . ' đ';
                                    ?>
                                </strong>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="h5">Tổng thanh toán:</span>
                                <span class="h4 text-danger">
                                    <?php echo number_format($total + $shipping); ?> đ
                                </span>
                            </div>
                            
                            <a href="checkout.php" class="btn btn-checkout btn-block">
                                <i class="fa fa-check-circle"></i> THANH TOÁN NGAY
                            </a>
                            
                            <p class="text-muted small text-center mt-2">
                                <i class="fa fa-lock"></i> Thanh toán an toàn & bảo mật
                            </p>
                        </div>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include("model/footer.php"); ?>
    
    <script>
        $(document).ready(function() {
            // Hiệu ứng hover cho nút xóa
            $('.btn-remove').hover(
                function() {
                    $(this).css('transform', 'scale(1.05)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                }
            );
            
            // Xác nhận xóa toàn bộ giỏ hàng
            // $('a[href="clear_cart.php"]').click(function(e) {
            //     if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
            //         e.preventDefault();
            //     }
            // });
        });
    </script>
</body>
</html>