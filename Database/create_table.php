<?php
// 1. Cấu hình Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "THOITRANG"; // Đảm bảo bạn đã tạo Database tên này rồi

try {
    // 2. Tạo kết nối ngay tại đây (Không cần require file khác)
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Câu lệnh SQL tạo bảng
    // IF NOT EXISTS: Giúp code không bị lỗi nếu lỡ chạy 2 lần
    $sql = "CREATE TABLE IF NOT EXISTS THOITRANG (
        id INT AUTO_INCREMENT PRIMARY KEY,
        img VARCHAR(255) NOT NULL COMMENT 'Link ảnh',
        names VARCHAR(100) NOT NULL COMMENT 'Tên sản phẩm',
        price INT NOT NULL DEFAULT 0 COMMENT 'Giá tiền',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // 4. Thực thi lệnh
    $conn->exec($sql);
    echo "Đã tạo bảng 'THOITRANG' thành công!</h3>";
} catch (PDOException $e) {
    // Báo lỗi chi tiết nếu có
    echo " Lỗi: " . $e->getMessage() . "</h3>";
}

// 5. Đóng kết nối
$conn = null;
