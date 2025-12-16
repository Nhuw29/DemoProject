<?php
	session_start();
	
	// Lưu thông báo nếu cần (ví dụ: "Bạn đã đăng xuất thành công")
	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		// Có thể lưu thông báo vào session flash nếu cần
		$_SESSION['logout_message'] = "Bạn đã đăng xuất thành công!";
	}
	
	// Xóa tất cả session
	session_unset();
	session_destroy();
	
	// Bắt đầu session mới để lưu thông báo (nếu dùng)
	session_start();
	
	// Chuyển hướng về trang chủ
	header('location:../index.php');
	exit();
?>