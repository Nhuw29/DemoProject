<?php
    session_start();
    include '../model/header.php';
    require_once("../model/connect.php");
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Hiện thông báo
    if (isset($_GET['idProduct'])) {
        if (isset($_GET['es'])) {
            echo "<script>alert('Bạn đã sửa sản phẩm thành công!');</script>";
        }
        if (isset($_GET['ef'])) {
            echo "<script>alert('Sửa sản phẩm thất bại!');</script>";
        }
    }

    // Lấy sản phẩm theo ID
    if (isset($_GET['idProduct'])) {
        $idProduct = $_GET['idProduct'];

        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $idProduct]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"> Chỉnh sửa sản phẩm </h1>
            </div>

            <div class="col-lg-7" style="padding-bottom:120px">

                <?php
                    if ($result) {
                        $thumImage = "../" . $result['image'];
                ?>

                <form action="productedit-back.php?idProduct=<?= $result['id'] ?>" method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label> Tên sản phẩm </label>
                        <input type="text" class="form-control" name="txtName" value="<?= $result['name'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label> Danh mục sản phẩm </label>
                        <select class="form-control" name="category">
                            <?php
                                // Lấy tất cả categories
                                $sqlAll = "SELECT * FROM categories";
                                $stmtAll = $conn->query($sqlAll);
                                $allCategories = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($allCategories as $rowCate) {
                                    $selected = ($rowCate['id'] == $result['category_id']) ? 'selected' : '';
                                    echo '<option value="'.$rowCate['id'].'" '.$selected.'>'.$rowCate['name'].'</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label> Chọn hình ảnh sản phẩm </label>
                        <input type="file" name="FileImage">
                        <p>Ảnh hiện tại:</p>
                        <img src ="<?= $thumImage ?>" width="150" height="150">
                    </div>

                    <div class="form-group">
                        <label> Mô tả sản phẩm </label>
                        <textarea class="form-control" rows="3" name="txtDescript"><?= $result['description']; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label> Giá sản phẩm </label>
                            <input type ="number" class="form-control" name="txtPrice" value="<?= $result['price']; ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label> Phần trăm giảm </label>
                            <input type="number" class="form-control" name="txtSalePrice" value="<?= $result['saleprice']; ?>" min="0" max="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label> Số lượng sản phẩm </label>
                        <input type="number" class="form-control" name="txtNumber" value="<?= $result['quantity']; ?>">
                    </div>

                    <div class="form-group">
                        <label> Từ khoá tìm kiếm </label>
                        <input class="form-control" name="txtKeyword" value="<?= $result['keyword']; ?>">
                    </div>

                    <div class="form-group">
                        <label> Tình trạng sản phẩm </label>

                        <?php if ($result['status'] == 0) { ?>
                            <label class="radio-inline">
                                <input name="status" value="0" type="radio" checked> Còn hàng
                            </label>
                            <label class="radio-inline">
                                <input name="status" value="1" type="radio"> Hết hàng
                            </label>
                        <?php } else { ?>
                            <label class="radio-inline">
                                <input name="status" value="0" type="radio"> Còn hàng
                            </label>
                            <label class="radio-inline">
                                <input name="status" value="1" type="radio" checked> Hết hàng
                            </label>
                        <?php } ?>

                    </div>

                    <button type="submit" name="editProduct" class="btn btn-warning btn-lg">
                        Chỉnh sửa sản phẩm
                    </button>

                </form>

                <?php } else {
                    echo "Không tìm thấy sản phẩm!";
                } ?>

            </div>
        </div>
    </div>
</div>

<?php } else {
    echo "Không có ID sản phẩm!";
} ?>
</body>
</html>