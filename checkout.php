<?php
session_start();
require_once('model/connect.php');

// Kiểm tra nếu giỏ hàng trống
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    $_SESSION['cart_error'] = "Giỏ hàng của bạn đang trống!";
    header('Location: view-cart.php');
    exit();
}

// Tính tổng tiền
$total_amount = 0;
$total_items = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}

// Tính phí vận chuyển
$shipping_fee = ($total_amount >= 500000) ? 0 : 30000;
$grand_total = $total_amount + $shipping_fee;

// Xử lý khi nhấn nút Đặt hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Lấy dữ liệu từ form
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    
    // Validate dữ liệu
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Vui lòng nhập họ tên";
    }
    
    if (empty($phone) || !preg_match('/^(0[1-9][0-9]{8,9})$/', $phone)) {
        $errors[] = "Số điện thoại không hợp lệ";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (empty($address)) {
        $errors[] = "Vui lòng nhập địa chỉ giao hàng";
    }
    
    // Nếu không có lỗi, tiến hành đặt hàng
    if (empty($errors)) {
        try {
            // Bắt đầu transaction
            $conn->beginTransaction();
            
            // 1. Lưu thông tin đơn hàng
            $order_code = 'ORDER' . date('YmdHis') . rand(100, 999);
            
            $sql_order = "INSERT INTO orders (order_code, customer_name, customer_phone, customer_email, 
                          customer_address, payment_method, notes, total_amount, shipping_fee, grand_total, status) 
                          VALUES (:order_code, :name, :phone, :email, :address, :payment_method, 
                          :notes, :total_amount, :shipping_fee, :grand_total, 'pending')";
            
            $stmt_order = $conn->prepare($sql_order);
            $stmt_order->execute([
                ':order_code' => $order_code,
                ':name' => $name,
                ':phone' => $phone,
                ':email' => $email,
                ':address' => $address,
                ':payment_method' => $payment_method,
                ':notes' => $notes,
                ':total_amount' => $total_amount,
                ':shipping_fee' => $shipping_fee,
                ':grand_total' => $grand_total
            ]);
            
            $order_id = $conn->lastInsertId();
            
            // 2. Lưu chi tiết đơn hàng
            $sql_detail = "INSERT INTO order_details (order_id, product_id, product_name, 
                          product_price, quantity, subtotal) 
                          VALUES (:order_id, :product_id, :product_name, :product_price, 
                          :quantity, :subtotal)";
            
            $stmt_detail = $conn->prepare($sql_detail);
            
            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                
                $stmt_detail->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['id'],
                    ':product_name' => $item['name'],
                    ':product_price' => $item['price'],
                    ':quantity' => $item['quantity'],
                    ':subtotal' => $subtotal
                ]);
                
                // 3. Cập nhật số lượng tồn kho (nếu có cột stock)
                // $sql_update = "UPDATE products SET stock = stock - :quantity WHERE id = :id";
                // $stmt_update = $conn->prepare($sql_update);
                // $stmt_update->execute([':quantity' => $item['quantity'], ':id' => $item['id']]);
            }
            
            // Commit transaction
            $conn->commit();
            
            // Lưu thông tin đơn hàng vào session để hiển thị xác nhận
            $_SESSION['order_info'] = [
                'order_id' => $order_id,
                'order_code' => $order_code,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'payment_method' => $payment_method,
                'notes' => $notes,
                'total_amount' => $total_amount,
                'shipping_fee' => $shipping_fee,
                'grand_total' => $grand_total,
                'cart' => $_SESSION['cart'],
                'order_date' => date('d/m/Y H:i:s')
            ];
            
            // Xóa giỏ hàng sau khi đặt hàng thành công
            unset($_SESSION['cart']);
            unset($_SESSION['cart_total_items']);
            
            // Chuyển đến trang xác nhận
            header('Location: order-confirmation.php');
            exit();
            
        } catch (PDOException $e) {
            // Rollback nếu có lỗi
            $conn->rollBack();
            
            $_SESSION['checkout_error'] = "Có lỗi xảy ra khi đặt hàng: " . $e->getMessage();
            header('Location: checkout.php');
            exit();
        }
    } else {
        // Hiển thị lỗi validation
        $_SESSION['checkout_errors'] = $errors;
        header('Location: checkout.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Thanh Toán - NAU_Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logoShop.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <style>
        .checkout-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .checkout-title {
            color: #ff0066;
            border-bottom: 2px solid #ff0066;
            padding-bottom: 10px;
            margin-bottom: 30px;
            font-weight: 700;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-left: 4px solid #ff0066;
            padding-left: 10px;
        }
        
        .form-group label {
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px;
            transition: 0.3s;
        }
        
        .form-control:focus {
            border-color: #ff0066;
            box-shadow: 0 0 0 2px rgba(255,0,102,0.1);
        }
        
        .order-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            flex: 2;
        }
        
        .item-qty, .item-price {
            flex: 1;
            text-align: center;
        }
        
        .total-row {
            font-size: 18px;
            font-weight: 700;
            color: #ff0066;
            padding: 15px 0;
            border-top: 2px solid #ddd;
            margin-top: 10px;
        }
        
        .payment-option {
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: 0.3s;
            background: #fff;
        }
        
        .payment-option:hover {
            border-color: #ff0066;
            background: #fff5f9;
        }
        
        .payment-option.active {
            border-color: #ff0066;
            background: #fff5f9;
        }
        
        .payment-option input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.2);
        }
        
        .payment-icon {
            font-size: 24px;
            margin-right: 10px;
            color: #ff0066;
        }
        
        .btn-place-order {
            background: #ff0066;
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 18px;
            border: none;
            width: 100%;
            margin-top: 20px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-place-order:hover {
            background: #e6005c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,0,102,0.3);
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            margin-right: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        
        .info-box {
            background: #e8f4fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include("model/header.php"); ?>
    
    <div class="container">
        <div class="checkout-container">
            <h2 class="checkout-title"><i class="fa fa-shopping-cart"></i> THANH TOÁN ĐƠN HÀNG</h2>
            
            <!-- Hiển thị lỗi validation -->
            <?php if (isset($_SESSION['checkout_errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h5><i class="fa fa-exclamation-triangle"></i> Vui lòng kiểm tra lại thông tin:</h5>
                    <ul>
                        <?php foreach ($_SESSION['checkout_errors'] as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php unset($_SESSION['checkout_errors']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['checkout_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['checkout_error']; ?>
                    <?php unset($_SESSION['checkout_error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Form thông tin khách hàng -->
                <div class="col-md-7">
                    <h4 class="section-title"><i class="fa fa-user-circle"></i> Thông tin giao hàng</h4>
                    
                    <div class="info-box">
                        <i class="fa fa-info-circle"></i> 
                        Vui lòng điền đầy đủ và chính xác thông tin để chúng tôi có thể liên hệ và giao hàng cho bạn.
                    </div>
                    
                    <form method="POST" action="checkout.php" id="checkoutForm">
                        <div class="form-group">
                            <label for="name">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                           required placeholder="0912345678">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                           required placeholder="example@email.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      required placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        
                        <h4 class="section-title"><i class="fa fa-credit-card-alt"></i> Phương thức thanh toán</h4>
                        
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod" checked> 
                                <i class="fa fa-money payment-icon"></i>
                                <div>
                                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                                    <p class="text-muted small mb-0">Bạn chỉ thanh toán khi nhận được hàng. Phù hợp với tất cả khu vực.</p>
                                </div>
                            </label>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank_transfer"> 
                                <i class="fa fa-university payment-icon"></i>
                                <div>
                                    <strong>Chuyển khoản ngân hàng</strong>
                                    <p class="text-muted small mb-0">Chuyển khoản qua tài khoản ngân hàng. Thông tin tài khoản sẽ được gửi qua email.</p>
                                </div>
                            </label>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="momo"> 
                                <i class="fa fa-mobile payment-icon"></i>
                                <div>
                                    <strong>Ví điện tử MoMo</strong>
                                    <p class="text-muted small mb-0">Thanh toán qua ứng dụng MoMo. Quét QR code để thanh toán.</p>
                                </div>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Ghi chú đơn hàng (tùy chọn)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ví dụ: Giao hàng giờ hành chính, gọi điện trước khi giao, yêu cầu đóng gói cẩn thận..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    Tôi đồng ý với <a href="#" data-toggle="modal" data-target="#termsModal">điều khoản và điều kiện</a> mua hàng của NAU_Shop
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="view-cart.php" class="btn btn-back">
                                <i class="fa fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                            <button type="submit" name="place_order" class="btn btn-place-order">
                                <i class="fa fa-check-circle"></i> HOÀN TẤT ĐẶT HÀNG
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Tóm tắt đơn hàng -->
                <div class="col-md-5">
                    <h4 class="section-title"><i class="fa fa-shopping-bag"></i> Đơn hàng của bạn</h4>
                    
                    <div class="order-summary">
                        <?php foreach ($_SESSION['cart'] as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                        <div class="order-item">
                            <div class="item-name">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <div class="text-muted small">
                                    Số lượng: <?php echo $item['quantity']; ?>
                                </div>
                            </div>
                            <div class="item-price">
                                <?php echo number_format($subtotal); ?> đ
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="order-item">
                            <div class="item-name">Tạm tính</div>
                            <div class="item-price"><?php echo number_format($total_amount); ?> đ</div>
                        </div>
                        
                        <div class="order-item">
                            <div class="item-name">
                                Phí vận chuyển
                                <?php if ($shipping_fee == 0): ?>
                                    <span class="badge badge-success">MIỄN PHÍ</span>
                                <?php endif; ?>
                            </div>
                            <div class="item-price">
                                <?php 
                                echo ($shipping_fee == 0) ? '0 đ' : number_format($shipping_fee) . ' đ';
                                ?>
                            </div>
                        </div>
                        
                        <?php if ($shipping_fee > 0 && $total_amount < 500000): ?>
                        <div class="alert alert-warning small mb-3">
                            <i class="fa fa-truck"></i> 
                            Mua thêm <strong><?php echo number_format(500000 - $total_amount); ?> đ</strong> để được miễn phí vận chuyển!
                        </div>
                        <?php endif; ?>
                        
                        <div class="order-item total-row">
                            <div class="item-name">Tổng cộng</div>
                            <div class="item-price"><?php echo number_format($grand_total); ?> đ</div>
                        </div>
                    </div>
                    
                    <!-- Thông tin hỗ trợ -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5><i class="fa fa-headphones"></i> Hỗ trợ khách hàng</h5>
                            <p class="mb-1"><strong>Hotline:</strong> <a href="tel:19001000">1900 1000</a></p>
                            <p class="mb-1"><strong>Zalo:</strong> 0912 345 678</p>
                            <p class="mb-1"><strong>Email:</strong> <a href="mailto:support@naushop.com">support@naushop.com</a></p>
                            <p class="mb-0"><strong>Giờ làm việc:</strong> 8:00 - 22:00 (T2 - CN)</p>
                        </div>
                    </div>
                    
                    <!-- Chính sách -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5><i class="fa fa-shield"></i> Cam kết từ NAU_Shop</h5>
                            <ul class="small pl-3 mb-0">
                                <li>✅ Sản phẩm chính hãng 100%</li>
                                <li>✅ Giao hàng nhanh 2-4 ngày</li>
                                <li>✅ Đổi trả trong 7 ngày</li>
                                <li>✅ Hỗ trợ 24/7</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Điều khoản -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Điều khoản và điều kiện mua hàng</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h5>1. Thông tin chung</h5>
                    <p>Khi sử dụng dịch vụ của NAU_Shop, bạn đồng ý với các điều khoản và điều kiện sau đây...</p>
                    
                    <h5>2. Chính sách thanh toán</h5>
                    <p>Chúng tôi chấp nhận các phương thức thanh toán: COD, chuyển khoản ngân hàng, ví điện tử MoMo...</p>
                    
                    <h5>3. Chính sách vận chuyển</h5>
                    <p>Thời gian giao hàng: 2-5 ngày làm việc tùy khu vực. Miễn phí vận chuyển cho đơn hàng từ 500,000 đ...</p>
                    
                    <h5>4. Chính sách đổi trả</h5>
                    <p>Đổi trả trong vòng 7 ngày nếu sản phẩm lỗi, không đúng mô tả hoặc không vừa size...</p>
                    
                    <h5>5. Bảo mật thông tin</h5>
                    <p>Chúng tôi cam kết bảo mật thông tin cá nhân của khách hàng và không chia sẻ cho bên thứ ba...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include("model/footer.php"); ?>
    
    <script>
        $(document).ready(function() {
            // Highlight payment option khi click
            $('.payment-option').click(function() {
                $('.payment-option').removeClass('active');
                $(this).addClass('active');
                $(this).find('input[type="radio"]').prop('checked', true);
            });
            
            // Auto check payment option đầu tiên
            $('.payment-option:first').addClass('active');
            
            // Validate form
            $('#checkoutForm').submit(function(e) {
                var name = $('#name').val().trim();
                var phone = $('#phone').val().trim();
                var email = $('#email').val().trim();
                var address = $('#address').val().trim();
                
                // Reset error styles
                $('.form-control').removeClass('is-invalid');
                
                var isValid = true;
                
                // Validate name
                if (name.length < 2) {
                    $('#name').addClass('is-invalid');
                    isValid = false;
                }
                
                // Validate phone (Việt Nam)
                var phoneRegex = /^(0[1-9][0-9]{8,9})$/;
                if (!phoneRegex.test(phone)) {
                    $('#phone').addClass('is-invalid');
                    isValid = false;
                }
                
                // Validate email
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    $('#email').addClass('is-invalid');
                    isValid = false;
                }
                
                // Validate address
                if (address.length < 10) {
                    $('#address').addClass('is-invalid');
                    isValid = false;
                }
                
                // Check terms agreement
                if (!$('#agree_terms').prop('checked')) {
                    alert('Vui lòng đồng ý với điều khoản và điều kiện mua hàng!');
                    $('#agree_terms').focus();
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Vui lòng kiểm tra lại thông tin đã nhập!');
                }
            });
            
            // Format phone number
            $('#phone').on('input', function() {
                var value = $(this).val().replace(/\D/g, '');
                if (value.length > 0 && value[0] !== '0') {
                    value = '0' + value;
                }
                $(this).val(value);
            });
        });
    </script>
</body>
</html>