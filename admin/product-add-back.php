<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../model/connect.php');

if (isset($_POST['addProduct']))
{
    $keywordPr  = $_POST['txtKeyword']  ?? '';
    $descriptPr = $_POST['txtDescript'] ?? '';
    $status     = $_POST['status']      ?? 0;

    $namePr     = $_POST['txtName']     ?? '';
    $categoryPr = $_POST['category']    ?? '';
    $pricePr    = $_POST['txtPrice']    ?? 0;
    $salePricePr= $_POST['txtSalePrice']?? 0;
    $quantityPr = $_POST['txtNumber']   ?? 0;

    /* ======================= IMAGE HANDLE ======================= */

    $image = "";

    if (!empty($_FILES["FileImage"]["name"])) {

        $fileName = basename($_FILES["FileImage"]["name"]);
        
        // Tạo thư mục uploads nếu chưa tồn tại
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $target_file = $uploadDir . $fileName;
        $uploadOk = 1;

        // Check file có đúng dạng ảnh không
        $check = getimagesize($_FILES["FileImage"]["tmp_name"]);
        if ($check === false) {
            header("Location: product-add.php?notimage=notimage");
            exit();
        }

        // Auto rename nếu file trùng
        if (file_exists($target_file)) {
            $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $file_base = pathinfo($fileName, PATHINFO_FILENAME);
            $newName = $file_base . "_" . time() . "." . $file_ext;
            $target_file = $uploadDir . $newName;
            $fileName = $newName;
        }

        // Upload ảnh
        if (move_uploaded_file($_FILES["FileImage"]["tmp_name"], $target_file)) {
            $image = "uploads/" . $fileName;
        } else {
            // Lỗi upload
            echo "Lỗi upload file. Kiểm tra quyền thư mục uploads/";
            exit();
        }
    } 
    else {
        // Không có ảnh → redirect báo lỗi
        header("Location: product-add.php?notimage=notimage");
        exit();
    }

    /* ======================= INSERT DB (PDO) ======================= */

    try {
        $sql = "INSERT INTO products 
                (name, category_id, image, description, price, saleprice, created, quantity, keyword, status) 
                VALUES 
                (:name, :category, :image, :descript, :price, :sale, NOW(), :quantity, :keyword, :status)";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':name',     $namePr, PDO::PARAM_STR);
        $stmt->bindParam(':category', $categoryPr, PDO::PARAM_INT);
        $stmt->bindParam(':image',    $image, PDO::PARAM_STR);
        $stmt->bindParam(':descript', $descriptPr, PDO::PARAM_STR);
        $stmt->bindParam(':price',    $pricePr, PDO::PARAM_INT);
        $stmt->bindParam(':sale',     $salePricePr, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantityPr, PDO::PARAM_INT);
        $stmt->bindParam(':keyword',  $keywordPr, PDO::PARAM_STR);
        $stmt->bindParam(':status',   $status, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: product-list.php?addps=success");
            exit();
        } else {
            echo "Lỗi execute SQL";
            exit();
        }

    } catch (PDOException $e) {
        echo "Lỗi PDO: " . $e->getMessage();
        exit();
    }
} else {
    echo "Không có dữ liệu POST!";
}
?>