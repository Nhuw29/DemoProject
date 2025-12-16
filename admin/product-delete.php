<?php
session_start(); // THÊM DÒNG NÀY
error_reporting(E_ALL ^ E_DEPRECATED);
require_once '../model/connect.php';

// Xóa sản phẩm theo id
if (isset($_GET['idProducts'])) {
    $idProduct = $_GET['idProducts'];

    try {
        // Kiểm tra xem sản phẩm có tồn tại không
        $checkStmt = $conn->prepare("SELECT id FROM products WHERE id = :id");
        $checkStmt->execute(['id' => $idProduct]);
        
        if ($checkStmt->rowCount() > 0) {
            // Prepare statement
            $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute(['id' => $idProduct]);

            if ($stmt->rowCount()) {
                echo "<script>alert('Xóa sản phẩm thành công!'); window.location = 'product-list.php?ps=success';</script>";
            } else {
                echo "<script>alert('Không thể xóa sản phẩm!'); window.location = 'product-list.php?pf=fail';</script>";
            }
        } else {
            echo "<script>alert('Sản phẩm không tồn tại!'); window.location = 'product-list.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "'); window.location = 'product-list.php?pf=fail';</script>";
    }
} else {
    echo "<script>window.location = 'product-list.php';</script>";
}
?>