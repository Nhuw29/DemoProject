<?php
session_start();
require_once('model/connect.php');

// Bật báo lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra có id sản phẩm hay không
if (!isset($_GET['id'])) {
    $_SESSION['cart_error'] = "Không tìm thấy sản phẩm!";
    header("Location: index.php");
    exit();
}

// Lấy id sản phẩm và validate
$id = intval($_GET['id']);
if ($id <= 0) {
    $_SESSION['cart_error'] = "ID sản phẩm không hợp lệ!";
    header("Location: index.php");
    exit();
}

// Lấy số lượng từ URL (mặc định là 1)
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
if ($quantity < 1) $quantity = 1; // Đảm bảo số lượng ít nhất là 1
if ($quantity > 99) $quantity = 99; // Giới hạn tối đa 99

try {
    // Query lấy thông tin sản phẩm bằng PDO
    $sql = "SELECT * FROM products WHERE id = :id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Nếu không có sản phẩm → quay về trang chủ
    if (!$product) {
        $_SESSION['cart_error'] = "Sản phẩm không tồn tại!";
        header("Location: index.php");
        exit();
    }
    
    // Nếu chưa có giỏ hàng thì tạo giỏ
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // TỰ ĐỘNG GỘP SẢN PHẨM TRÙNG - SỬA CHÍNH Ở ĐÂY
    // Nếu sản phẩm đã có trong giỏ → tăng số lượng
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $quantity;
        $message = "Đã tăng số lượng '" . htmlspecialchars($product['name']) . "' lên " . $_SESSION['cart'][$id]['quantity'] . " sản phẩm!";
    } else {
        // Nếu chưa có → thêm sản phẩm mới
        $_SESSION['cart'][$id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'image' => $product['image'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];
        $message = "Đã thêm '" . htmlspecialchars($product['name']) . "' vào giỏ hàng!";
    }
    
    // Cập nhật tổng số lượng trong giỏ (cho header)
    $_SESSION['cart_total_items'] = 0;
    foreach ($_SESSION['cart'] as $item) {
        $_SESSION['cart_total_items'] += $item['quantity'];
    }
    
    // Lưu thông báo thành công
    $_SESSION['cart_success'] = $message;
    
    // Xác định trang quay lại
    $redirect = 'view-cart.php'; // Mặc định về giỏ hàng
    
    // Nếu có tham số từ_url thì quay lại trang đó
    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
        // Kiểm tra nếu không phải là trang addcart.php thì dùng referer
        if (strpos($referer, 'addcart.php') === false) {
            $redirect = $referer;
        }
    }
    
    // Quay lại trang trước đó hoặc giỏ hàng
    header("Location: " . $redirect);
    exit();
    
} catch (PDOException $e) {
    // Xử lý lỗi
    error_log("Lỗi khi thêm vào giỏ hàng: " . $e->getMessage());
    
    // Hiển thị thông báo lỗi
    $_SESSION['cart_error'] = "Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.";
    
    // Quay về trang trước đó
    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: index.php");
    }
    exit();
}
?>