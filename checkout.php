<?php
session_start();
require_once('model/connect.php');

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: view-cart.php');
    exit();
}

// Tính tổng đơn hàng
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu form
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $payment = $_POST['payment_method'];
    
    // Kiểm tra dữ liệu
    if (empty($name) || empty($phone) || empty($email) || empty($address)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        try {
            // Lưu đơn hàng
            $order_code = 'DH' . date('YmdHis');
            
            $sql = "INSERT INTO orders (order_code, customer_name, customer_phone, 
                    customer_email, customer_address, payment_method, total_amount, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$order_code, $name, $phone, $email, $address, $payment, $total]);
            
            $order_id = $conn->lastInsertId();
            
            // Lưu chi tiết đơn hàng
            $sql_detail = "INSERT INTO order_details (order_id, product_id, product_name, 
                          product_price, quantity, subtotal) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt_detail = $conn->prepare($sql_detail);
            
            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $stmt_detail->execute([$order_id, $item['id'], $item['name'], 
                                      $item['price'], $item['quantity'], $subtotal]);
            }
            
            // Lưu thông tin đơn hàng vào session
            $_SESSION['order_info'] = [
                'order_id' => $order_id,
                'order_code' => $order_code,
                'name' => $name,
                'total' => $total
            ];
            
            // Xóa giỏ hàng
            unset($_SESSION['cart']);
            
            // Chuyển hướng đến trang xác nhận
            header('Location: order-confirmation.php');
            exit();
            
        } catch (PDOException $e) {
            $error = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .checkout-box { 
            background: white; 
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .order-summary { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px;
        }
        .btn-checkout { 
            background: #28a745; 
            color: white; 
            padding: 10px 20px; 
            width: 100%;
        }
        .btn-checkout:hover { background: #218838; color: white; }
    </style>
</head>
<body>
    <?php include("model/header.php"); ?>
    
    <div class="container">
        <div class="checkout-box">
            <h2><i class="glyphicon glyphicon-shopping-cart"></i> Thanh Toán</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-7">
                    <h4>Thông tin giao hàng</h4>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Họ và tên *</label>
                            <input type="text" class="form-control" name="name" required 
                                   value="<?php echo $_POST['name'] ?? ''; ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Số điện thoại *</label>
                                    <input type="tel" class="form-control" name="phone" required 
                                           value="<?php echo $_POST['phone'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" class="form-control" name="email" required 
                                           value="<?php echo $_POST['email'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Địa chỉ *</label>
                            <textarea class="form-control" name="address" rows="3" required><?php echo $_POST['address'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Phương thức thanh toán</label>
                            <select class="form-control" name="payment_method">
                                <option value="cod">Thanh toán khi nhận hàng</option>
                                <option value="bank">Chuyển khoản ngân hàng</option>
                                <option value="momo">Ví MoMo</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="place_order" class="btn btn-checkout">
                                <i class="glyphicon glyphicon-ok"></i> ĐẶT HÀNG
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-5">
                    <h4>Đơn hàng của bạn</h4>
                    <div class="order-summary">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="row" style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed #ddd;">
                                <div class="col-xs-8">
                                    <strong><?php echo $item['name']; ?></strong><br>
                                    <small>Số lượng: <?php echo $item['quantity']; ?></small>
                                </div>
                                <div class="col-xs-4 text-right">
                                    <?php echo number_format($item['price'] * $item['quantity']); ?> đ
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="row" style="margin-top: 15px; font-size: 18px; font-weight: bold;">
                            <div class="col-xs-6">Tổng tiền:</div>
                            <div class="col-xs-6 text-right text-danger">
                                <?php echo number_format($total); ?> đ
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" style="margin-top: 20px;">
                        <strong><i class="glyphicon glyphicon-info-sign"></i> Thông tin:</strong><br>
                        • Giao hàng toàn quốc<br>
                        • Thanh toán khi nhận hàng<br>
                        • Hotline: 1900 1000
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include("model/footer.php"); ?>
</body>
</html>