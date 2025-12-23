<?php
session_start(); // Thêm session_start ở đầu file

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['username'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang chủ
    header("location:../index.php");
    exit();
}

// Kiểm tra quyền admin (role = 0 hoặc từ bảng admin)
$isAdmin = false;
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0) {
    $isAdmin = true; // Admin từ bảng users
}
if (isset($_SESSION['login_source']) && $_SESSION['login_source'] == 'admin') {
    $isAdmin = true; // Admin từ bảng admin
}

if (!$isAdmin) {
    // Nếu không phải admin, chuyển hướng về trang chủ
    header("location:../index.php");
    exit();
}

require_once('../model/connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Thông báo - THÊM THÔNG BÁO SỬA SẢN PHẨM
if (isset($_GET['ps'])) {
    echo "<script>alert('Bạn đã xóa sản phẩm thành công!');</script>";
}
if (isset($_GET['pf'])) {
    echo "<script>alert('Không thể xóa sản phẩm!');</script>";
}

if (isset($_GET['es'])) {
    echo "<script>alert('Bạn đã sửa sản phẩm thành công!');</script>";
}
if (isset($_GET['ef'])) {
    echo "<script>alert('Sửa sản phẩm thất bại!');</script>";
}

if (isset($_GET['addps'])) {
    echo "<script>alert('Bạn đã thêm sản phẩm thành công!');</script>";
}
if (isset($_GET['addpf'])) {
    echo "<script>alert('Thêm sản phẩm thất bại!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>Quản lý sản phẩm - Admin</title>
    <style>
        /* ====== ADMIN HEADER STYLES ====== */
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 0;
            border-bottom: 3px solid #ff6b6b;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            color: white;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .admin-header:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff6b6b, #ffd93d, #6bcf7f, #4d96ff);
        }
        
        .admin-brand {
            font-size: 24px;
            font-weight: 700;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
        }
        
        .admin-brand i {
            margin-right: 10px;
            color: #ffd93d;
            font-size: 28px;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 15px;
        }
        
        .user-welcome {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 15px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .user-welcome:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        .user-welcome i {
            color: #ffd93d;
            font-size: 16px;
        }
        
        .user-name {
            font-weight: 600;
            color: white;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        
        .nav-btn {
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            border: none;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .logout-btn:hover {
            background: linear-gradient(135deg, #ff5252 0%, #d93c3c 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
            text-decoration: none;
        }
        
        .home-btn {
            background: linear-gradient(135deg, #6bcf7f 0%, #4caf50 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .home-btn:hover {
            background: linear-gradient(135deg, #5cb85c 0%, #43a047 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 207, 127, 0.4);
            text-decoration: none;
        }
        
        /* ====== ADMIN NAVIGATION ====== */
        .admin-nav {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }
        
        .admin-nav .nav-tabs {
            border-bottom: none;
        }
        
        .admin-nav .nav-tabs > li {
            margin-bottom: 0;
        }
        
        .admin-nav .nav-tabs > li > a {
            border: none;
            border-radius: 0;
            padding: 15px 20px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
            border-right: 1px solid #e9ecef;
            background: transparent;
        }
        
        .admin-nav .nav-tabs > li:first-child > a {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        
        .admin-nav .nav-tabs > li:last-child > a {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-right: none;
        }
        
        .admin-nav .nav-tabs > li > a:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        
        .admin-nav .nav-tabs > li.active > a {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .admin-nav .nav-tabs > li.active > a:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
        }
        
        /* ====== PAGE HEADER ====== */
        .page-header {
            color: #333;
            padding-bottom: 15px;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            position: relative;
        }
        
        .page-header:after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #ff6b6b);
        }
        
        /* ====== BUTTON STYLES ====== */
        .btn-success {
            background: linear-gradient(135deg, #6bcf7f 0%, #4caf50 100%);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #5cb85c 0%, #43a047 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 207, 127, 0.4);
            border: none;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #4d96ff 0%, #3578e5 100%);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-info:hover {
            background: linear-gradient(135deg, #3578e5 0%, #2a65d1 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(77, 150, 255, 0.4);
            border: none;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #ff5252 0%, #d93c3c 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
            border: none;
        }
        
        /* ====== TABLE STYLES ====== */
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .table-hover > tbody > tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
            transform: scale(1.005);
            transition: all 0.2s ease;
        }
        
        /* ====== ANIMATION ====== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        #page-wrapper {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* ====== BADGE STYLES ====== */
        .badge-category {
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-noimage {
            background: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 12px;
        }
        
        /* ====== IMAGE STYLES ====== */
        .product-image {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .product-image:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* ====== TEXT STYLES ====== */
        .product-name {
            font-weight: 600;
            color: #333;
        }
        
        .price-text {
            color: #28a745;
            font-weight: 700;
        }
        
        .sale-price-text {
            color: #ff6b6b;
            font-weight: 700;
        }
        
        /* ====== TABLE HEADER ====== */
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        /* ====== EMPTY STATE ====== */
        .empty-state {
            padding: 30px;
            color: #6c757d;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="admin-brand">
                        <i class="fa fa-cogs"></i>
                        <span>Trang Quản Trị MyLiShop</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="admin-info">
                        <div class="user-welcome">
                            <i class="fa fa-user-circle"></i>
                            <span class="user-name">Xin chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        </div>
                        <div class="nav-buttons">
                            <a href="../user/logout.php" class="nav-btn logout-btn">
                                <i class="fa fa-sign-out"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Navigation -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="admin-nav">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="product-list.php"><i class="fa fa-cube"></i> Quản lý sản phẩm</a></li> 
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <i class="fa fa-cubes"></i> Danh sách sản phẩm
                    </h1>
                </div>

                <!-- Nút thêm sản phẩm mới -->
                <div class="col-lg-12" style="margin-bottom: 20px;">
                    <a href="product-add.php" class="btn btn-success">
                        <i class="fa fa-plus-circle"></i> Thêm sản phẩm mới
                    </a>
                </div>

                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr align="center" class="table-header">
                            <th>STT</th>
                            <th>Tên sản phẩm</th>
                            <th>Mã danh mục</th>
                            <th>Hình ảnh</th>
                            <th>Giá</th>
                            <th>Giảm giá</th>
                            <th>Chỉnh sửa</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    // Lấy dữ liệu sản phẩm bằng PDO
                    $sql = "SELECT * FROM products ORDER BY id DESC";

                    try {
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $rows = [];
                    }

                    if (!empty($rows)) {
                        foreach ($rows as $index => $row) {
                            // Ảnh thumb
                            $thumbImage = (!empty($row['image'])) ? "../" . $row['image'] : "";
                    ?>
                            <tr class="odd gradeX" align="center">
                                <td><?= $index + 1; ?></td>
                                <td class="product-name"><?= htmlspecialchars($row['name']); ?></td>
                                <td><span class="badge-category"><?= $row['category_id']; ?></span></td>
                                <td>
                                    <?php if($thumbImage): ?>
                                        <img src="<?= $thumbImage; ?>" width="100" height="100" alt="<?= htmlspecialchars($row['name']); ?>" class="product-image">
                                    <?php else: ?>
                                        <span class="badge-noimage">Không có ảnh</span>
                                    <?php endif; ?>
                                </td>
                                <td class="price-text"><?= number_format($row['price'], 0, ',', '.'); ?> đ</td>
                                <td class="sale-price-text">
                                    <?php 
                                    // Kiểm tra xem có giá giảm không
                                    if ($row['saleprice'] > 0 && $row['price'] > 0 && $row['saleprice'] < $row['price']) {
                                        // Tính phần trăm giảm giá
                                        $discount_percent = round((($row['price'] - $row['saleprice']) / $row['price']) * 100);
                                        
                                        // Hiển thị phần trăm giảm giá với badge đẹp
                                        echo '<span class="discount-badge" style="background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 15px; font-weight: bold; font-size: 14px;">';
                                        echo '-' . $discount_percent . '%';
                                        echo '</span>';
                                    } else {
                                        // Nếu không có giảm giá, hiển thị dấu gạch ngang hoặc trống
                                        echo '<span style="color: #999; font-size: 14px;">-</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="product-edit.php?idProduct=<?= $row['id'] ?>" class="btn btn-info btn-sm">
                                        <i class="fa fa-edit"></i> Sửa
                                    </a>
                                </td>
                                <td>
                                    <a href="product-delete.php?idProducts=<?= $row['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                        <i class="fa fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="8" class="empty-state"><i class="fa fa-exclamation-circle"></i><br>Không có sản phẩm nào</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>