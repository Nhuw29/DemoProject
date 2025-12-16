<?php
session_start();
require_once("../model/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Thông báo sửa sản phẩm
if (isset($_GET['es'])) echo "<script>alert('Bạn đã sửa sản phẩm thành công!');</script>";
if (isset($_GET['ef'])) echo "<script>alert('Sửa sản phẩm thất bại!');</script>";

// Lấy dữ liệu sản phẩm
$product = null;
$categories = [];

if (isset($_GET['idProduct'])) {
    $idProduct = $_GET['idProduct'];

    // Lấy sản phẩm
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $idProduct]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $thumImage = "../" . $product['image'];

        // Lấy danh mục
        $stmtCat = $conn->query("SELECT * FROM categories");
        $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Xử lý submit edit
if (isset($_POST['editProduct']) && $product) {
    $namePr = $_POST['txtName'] ?? '';
    $categoryPr = $_POST['category'] ?? 0;
    $pricePr = $_POST['txtPrice'] ?? 0;
    $salePricePr = $_POST['txtSalePrice'] ?? 0;
    $quantityPr = $_POST['txtNumber'] ?? 0;
    $keywordPr = $_POST['txtKeyword'] ?? '';
    $descriptPr = $_POST['txtDescript'] ?? '';
    $status = $_POST['status'] ?? 0;

    // Xử lý ảnh
    $image = $product['image']; // Mặc định giữ ảnh cũ
    
    if (!empty($_FILES['FileImage']['name'])) {
        $fileName = basename($_FILES["FileImage"]["name"]);
        
        // Tạo thư mục uploads nếu chưa tồn tại
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $target_file = $uploadDir . $fileName;
        
        // Check file có đúng dạng ảnh không
        $check = getimagesize($_FILES["FileImage"]["tmp_name"]);
        if ($check !== false) {
            // Auto rename nếu file trùng
            if (file_exists($target_file)) {
                $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $file_base = pathinfo($fileName, PATHINFO_FILENAME);
                $newName = $file_base . "_" . time() . "." . $file_ext;
                $target_file = $uploadDir . $newName;
                $fileName = $newName;
            }
            
            if (move_uploaded_file($_FILES["FileImage"]["tmp_name"], $target_file)) {
                $image = "uploads/" . $fileName;
            }
        }
    }

    // Update sản phẩm với PDO
    $sql = "UPDATE products SET name = :name, category_id = :category, image = :image, description = :description, price = :price, saleprice = :saleprice, quantity = :quantity, keyword = :keyword, status = :status WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        'name' => $namePr,
        'category' => $categoryPr,
        'image' => $image,
        'description' => $descriptPr,
        'price' => $pricePr,
        'saleprice' => $salePricePr,
        'quantity' => $quantityPr,
        'keyword' => $keywordPr,
        'status' => $status,
        'id' => $idProduct
    ]);

    if ($result) {
        // SỬA Ở ĐÂY: Thay đổi tham số từ ?ps=success thành ?es=success
        header("Location: product-list.php?es=success");
        exit();
    } else {
        header("Location: product-list.php?ef=fail");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chỉnh sửa sản phẩm</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12"><h1 class="page-header">Chỉnh sửa sản phẩm</h1></div>
            <div class="col-lg-7" style="padding-bottom:120px">
                <?php if ($product): ?>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Tên sản phẩm</label>
                        <input type="text" class="form-control" name="txtName" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Danh mục sản phẩm</label>
                        <select class="form-control" name="category">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Chọn hình ảnh sản phẩm</label>
                        <input type="file" name="FileImage">
                        <p>Ảnh hiện tại:</p>
                        <img src="<?php echo $thumImage; ?>" width="150" height="150">
                    </div>

                    <div class="form-group">
                        <label>Mô tả sản phẩm</label>
                        <textarea class="form-control" rows="3" name="txtDescript"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Giá sản phẩm</label>
                            <input type="number" class="form-control" name="txtPrice" value="<?php echo $product['price']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Phần trăm giảm</label>
                            <input type="number" class="form-control" name="txtSalePrice" value="<?php echo $product['saleprice']; ?>" min="0" max="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Số lượng sản phẩm</label>
                        <input type="number" class="form-control" name="txtNumber" value="<?php echo $product['quantity']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Từ khóa tìm kiếm</label>
                        <input type="text" class="form-control" name="txtKeyword" value="<?php echo htmlspecialchars($product['keyword']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Tình trạng sản phẩm</label>
                        <label class="radio-inline"><input type="radio" name="status" value="0" <?php echo ($product['status']==0)?'checked':''; ?>> Còn hàng</label>
                        <label class="radio-inline"><input type="radio" name="status" value="1" <?php echo ($product['status']==1)?'checked':''; ?>> Hết hàng</label>
                    </div>

                    <button type="submit" name="editProduct" class="btn btn-warning btn-lg">Chỉnh sửa sản phẩm</button>
                    <a href="product-list.php" class="btn btn-default btn-lg">Quay lại</a>
                </form>
                <?php else: ?>
                    <p>Không tìm thấy sản phẩm!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>