<?php
session_start();

if (isset($_GET['id'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if (isset($_SESSION['cart'][$id])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$id]['quantity'] += 1;
            $_SESSION['cart_success'] = "Đã tăng số lượng sản phẩm!";
        } elseif ($action === 'decrease') {
            if ($_SESSION['cart'][$id]['quantity'] > 1) {
                $_SESSION['cart'][$id]['quantity'] -= 1;
                $_SESSION['cart_success'] = "Đã giảm số lượng sản phẩm!";
            } else {
                // Nếu số lượng = 1 mà giảm thì xóa luôn
                unset($_SESSION['cart'][$id]);
                $_SESSION['cart_success'] = "Đã xóa sản phẩm khỏi giỏ hàng!";
            }
        }
    }
}

// Quay lại trang giỏ hàng
header("Location: view-cart.php");
exit();
?>