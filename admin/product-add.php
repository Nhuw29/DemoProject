<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>Thêm sản phẩm</title>
</head>
<body>
    <meta charset="utf-8">
<?php
    session_start();
    require_once('../model/connect.php');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $noimage = isset($_GET['notimage']) 
        ? 'Vui lòng chọn hình ảnh hợp lệ!' 
        : '';
?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"> Thêm sản phẩm </h1>
            </div>

            <div class="col-lg-7" style="padding-bottom:120px">
                <!-- SỬA ĐƯỜNG DẪN Ở ĐÂY: product-add-back.php -->
                <form action="product-add-back.php" method="POST" enctype="multipart/form-data">

                    <!-- Tên sản phẩm -->
                    <div class="form-group">
                        <label> Tên sản phẩm </label>
                        <input type="text" class="form-control" name="txtName" placeholder="Nhập tên sản phẩm" required />
                    </div>

                    <!-- Danh mục -->
                    <div class="form-group">
                        <label> Danh mục sản phẩm </label>
                        <select class="form-control" name="category">

                        <?php
                            try {
                                $sql = "SELECT * FROM categories";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($categories as $row) {
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }
                            } catch(PDOException $e) {
                                echo '<option disabled>Lỗi load danh mục</option>';
                            }
                        ?>

                        </select>
                    </div>

                    <!-- Giá + Sale -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Giá sản phẩm </label>
                                <input type="number" class="form-control" name="txtPrice"
                                       placeholder="Nhập giá sản phẩm" min="20000" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Phần trăm giảm (nếu có) </label>
                                <input type="number" class="form-control" name="txtSalePrice"
                                       placeholder="Nhập phần trăm giá giảm" value="0" min="0" max="50" />
                            </div>
                        </div>
                    </div>

                    <!-- Số lượng -->
                    <div class="form-group">
                        <label> Số lượng sản phẩm </label>
                        <input type="number" class="form-control" name="txtNumber"
                               placeholder="Nhập số lượng sản phẩm" required />
                    </div>

                    <!-- Hình ảnh -->
                    <div class="form-group">
                        <label> Chọn hình ảnh sản phẩm </label>
                        <input type="file" name="FileImage" required>
                        <span style="color: red"><?= $noimage; ?></span>
                    </div>

                    <!-- Keyword -->
                    <div class="form-group">
                        <label> Nhập từ cho khách hàng tìm kiếm </label>
                        <input class="form-control" name="txtKeyword" placeholder="Nhập từ khóa tìm kiếm" />
                    </div>

                    <!-- Mô tả -->
                    <div class="form-group">
                        <label> Mô tả sản phẩm </label>
                        <textarea class="form-control" rows="3" name="txtDescript"></textarea>
                    </div>

                    <!-- Trạng thái sản phẩm -->
                    <div class="form-group">
                        <label> Tình trạng sản phẩm </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="0" checked> Còn hàng
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="1"> Hết hàng
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" name="addProduct" class="btn btn-warning btn-block btn-lg">
                                Thêm
                            </button>
                        </div>

                        <div class="col-md-6">
                            <button type="reset" class="btn btn-default btn-block btn-lg"
                                    style="background: gray; color:white;">
                                Thiết lập lại
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>