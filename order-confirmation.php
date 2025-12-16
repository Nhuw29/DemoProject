<?php
session_start();

// Kiểm tra nếu chưa có thông tin đơn hàng
if (!isset($_SESSION['order_info'])) {
    header('Location: index.php');
    exit();
}

$order = $_SESSION['order_info'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Xác Nhận Đơn Hàng - NAU_Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logoShop.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .order-number {
            background: #ff0066;
            color: white;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .order-details {
            text-align: left;
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            border-left: 5px solid #ff0066;
        }
        
        .btn-continue {
            background: #ff0066;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            border: none;
            margin-top: 20px;
            transition: 0.3s;
            display: inline-block;
            text-decoration: none;
        }
        
        .btn-continue:hover {
            background: #e6005c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,0,102,0.3);
            color: white;
            text-decoration: none;
        }
        
        .info-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ddd;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            width: 150px;
            display: inline-block;
        }
        
        .order-products {
            margin: 20px 0;
        }
        
        .product-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .product-row:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include("model/header.php"); ?>
    
    <div class="container">
        <div class="confirmation-container">
            <div class="success-icon">
                <i class="fa fa-check-circle"></i>
            </div>
            
            <h1 style="color: #28a745;">ĐẶT HÀNG THÀNH CÔNG!</h1>
            <p class="lead">Cảm ơn bạn đã mua hàng tại NAU_Shop</p>
            
            <div class="order-number">
                MÃ ĐƠN HÀNG: <?php echo htmlspecialchars($order['order_code']); ?>
            </div>
            
            <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
            
            <div class="order-details">
                <h4><i class="fa fa-info-circle"></i> Thông tin đơn hàng</h4>
                
                <div class="info-item">
                    <span class="info-label">Ngày đặt:</span>
                    <?php echo $order['order_date']; ?>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Khách hàng:</span>
                    <?php echo htmlspecialchars($order['name']); ?>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Số điện thoại:</span>
                    <?php echo htmlspecialchars($order['phone']); ?>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <?php echo htmlspecialchars($order['email']); ?>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Địa chỉ giao hàng:</span>
                    <?php echo htmlspecialchars($order['address']); ?>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Phương thức thanh toán:</span>
                    <?php 
                    $payment_methods = [
                        'cod' => 'Thanh toán khi nhận hàng (COD)',
                        'bank_transfer' => 'Chuyển khoản ngân hàng',
                        'momo' => 'Ví điện tử MoMo'
                    ];
                    echo isset($payment_methods[$order['payment_method']]) ? $payment_methods[$order['payment_method']] : $order['payment_method'];
                    ?>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                <div class="info-item">
                    <span class="info-label">Ghi chú:</span>
                    <?php echo htmlspecialchars($order['notes']); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <h5><i class="fa fa-shopping-bag"></i> Chi tiết đơn hàng</h5>
            <div class="order-products">
                <?php foreach ($order['cart'] as $item): ?>
                <div class="product-row">
                    <div>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        <div class="text-muted small">
                            Số lượng: <?php echo $item['quantity']; ?> × <?php echo number_format($item['price']); ?> đ
                        </div>
                    </div>
                    <div>
                        <strong><?php echo number_format($item['price'] * $item['quantity']); ?> đ</strong>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="product-row">
                    <div>Tạm tính</div>
                    <div><?php echo number_format($order['total_amount']); ?> đ</div>
                </div>
                
                <div class="product-row">
                    <div>
                        Phí vận chuyển
                        <?php if ($order['shipping_fee'] == 0): ?>
                            <span class="badge badge-success">MIỄN PHÍ</span>
                        <?php endif; ?>
                    </div>
                    <div><?php echo number_format($order['shipping_fee']); ?> đ</div>
                </div>
                
                <div class="product-row" style="background: #f5f5f5; padding: 15px; margin-top: 10px; border-radius: 5px;">
                    <div><strong>TỔNG CỘNG</strong></div>
                    <div><strong style="color: #ff0066; font-size: 18px;"><?php echo number_format($order['grand_total']); ?> đ</strong></div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="fa fa-clock-o"></i> 
                <strong>Thời gian giao hàng dự kiến:</strong> 2-5 ngày làm việc (tùy khu vực).