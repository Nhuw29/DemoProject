<?php
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if (isset($_SESSION['cart'][$id])) {
        $product_name = $_SESSION['cart'][$id]['name'];
        unset($_SESSION['cart'][$id]);
        $_SESSION['cart_success'] = "Đã xóa '$product_name' khỏi giỏ hàng!";
    }
}

// Quay lại trang giỏ hàng
header("Location: view-cart.php");
exit();
?>