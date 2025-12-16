<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "fashion_mylishop";

try {
    // Tạo kết nối PDO
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);

    // Đặt chế độ báo lỗi (rất quan trọng)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Bắt lỗi kết nối
    die("Connection failed: " . $e->getMessage());
}

// Biến $conn hiện tại là đối tượng PDO để bạn thực hiện các truy vấn tiếp theo
