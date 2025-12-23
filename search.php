
<?php
// PHẢI ĐẶT session_start() ở đầu file, trước mọi output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('model/connect.php');
$prd = 0;
if (isset($_SESSION['cart']))
{
    $prd = count($_SESSION['cart']);
}

// Lấy các tham số lọc từ form
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? $_GET['category'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Xây dựng câu truy vấn SQL
$sql = "SELECT id, image, name, price FROM products WHERE 1=1";
$params = array();

// Tìm kiếm theo từ khóa
if(!empty($searchKeyword)) {
    $sql .= " AND (name LIKE :keyword)";
    $params[':keyword'] = '%' . $searchKeyword . '%';
}

// Lọc theo danh mục
if(!empty($category_id) && is_numeric($category_id)) {
    $sql .= " AND category_id = :category_id";
    $params[':category_id'] = $category_id;
}

// Sắp xếp
switch($sort_by) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY name ASC";
        break;
    default:
        $sql .= " ORDER BY id DESC"; // Mới nhất
}

// Thực hiện truy vấn
$totalnumber = 0;
$resultSearch = false;

try {
    $stmt = $conn->prepare($sql);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $resultSearch = $stmt;
    
    $totalnumber = $resultSearch->rowCount();
} catch(PDOException $e) {
    $totalnumber = 0;
    $resultSearch = false;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tìm kiếm sản phẩm - Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logohong.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .filter-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin: 30px 0;
        }
        
        .filter-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #6a11cb;
        }
        
        .search-input {
            position: relative;
        }
        
        .search-input i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #6a11cb;
        }
        
        .search-input input {
            padding-left: 45px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            height: 45px;
        }
        
        .search-input input:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
        }
        
        .filter-btn {
            background: #6a11cb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            width: 100%;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .filter-btn:hover {
            background: #5a0db8;
        }
        
        .select-box {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            height: 45px;
            padding: 8px 12px;
        }
        
        .select-box:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
        }
        
        .product-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 100%;
            margin-bottom: 20px;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .product-img {
            height: 250px;
            overflow: hidden;
        }
        
        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-img img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
            height: 48px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .product-price {
            color: #6a11cb;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-detail {
            background: white;
            color: #6a11cb;
            border: 2px solid #6a11cb;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            flex: 1;
        }
        
        .btn-detail:hover {
            background: #6a11cb;
            color: white;
        }
        
        .btn-buy {
            background: #6a11cb;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            flex: 1;
        }
        
        .btn-buy:hover {
            background: #5a0db8;
        }
        
        .results-info {
            background: #e8f4ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #6a11cb;
        }
        
        .results-count {
            background: #6a11cb;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .no-results {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .no-results i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .product-img {
                height: 200px;
            }
            
            .filter-box {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include("model/header.php"); ?>
    <!-- /header -->

    <div class="container">
        <!-- Bộ lọc đơn giản -->
        <div class="filter-box">
            <h4 class="filter-title"><i class="fas fa-filter me-2"></i>Tìm Kiếm Sản Phẩm</h4>
            <form method="GET" action="search.php">
                <div class="row">
                    <!-- Tìm kiếm theo tên -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tìm theo tên</label>
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Nhập tên sản phẩm..." 
                                   value="<?php echo htmlspecialchars($searchKeyword); ?>">
                        </div>
                    </div>
                    
                    <!-- Danh mục -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Danh mục</label>
                        <select name="category" class="form-select select-box">
                            <option value="">Tất cả danh mục</option>
                            <option value="1" <?php echo $category_id == '1' ? 'selected' : ''; ?>>Thời trang nam</option>
                            <option value="2" <?php echo $category_id == '2' ? 'selected' : ''; ?>>Thời trang nữ</option>
                            <option value="3" <?php echo $category_id == '3' ? 'selected' : ''; ?>>Sản phẩm mới</option>
                        </select>
                    </div>
                    
                    <!-- Sắp xếp -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Sắp xếp theo</label>
                        <select name="sort" class="form-select select-box">
                            <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="price_asc" <?php echo $sort_by == 'price_asc' ? 'selected' : ''; ?>>Giá thấp đến cao</option>
                            <option value="price_desc" <?php echo $sort_by == 'price_desc' ? 'selected' : ''; ?>>Giá cao đến thấp</option>
                            <option value="name_asc" <?php echo $sort_by == 'name_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                        </select>
                    </div>
                    
                    <!-- Nút tìm kiếm -->
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Hiển thị bộ lọc đang dùng -->
            <?php if(!empty($searchKeyword) || !empty($category_id)): ?>
            <div class="mt-4 pt-3 border-top">
                <p class="mb-2 fw-bold">Đang lọc theo:</p>
                <div class="d-flex flex-wrap gap-2">
                    <?php if(!empty($searchKeyword)): ?>
                        <span class="badge bg-primary">
                            Từ khóa: "<?php echo htmlspecialchars($searchKeyword); ?>"
                            <a href="javascript:void(0)" onclick="removeFilter('search')" class="text-white ms-2">×</a>
                        </span>
                    <?php endif; ?>
                    
                    <?php if(!empty($category_id)): ?>
                        <?php 
                            $category_names = [
                                '1' => 'Thời trang nam',
                                '2' => 'Thời trang nữ',
                                '3' => 'Sản phẩm mới'
                            ];
                        ?>
                        <span class="badge bg-success">
                            Danh mục: <?php echo $category_names[$category_id] ?? ''; ?>
                            <a href="javascript:void(0)" onclick="removeFilter('category')" class="text-white ms-2">×</a>
                        </span>
                    <?php endif; ?>
                    
                    <?php if(!empty($searchKeyword) || !empty($category_id)): ?>
                        <a href="search.php" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Xóa tất cả
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Thông tin kết quả -->
        <?php if($totalnumber > 0): ?>
            <div class="results-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="fas fa-box me-2"></i>Kết quả tìm kiếm</h5>
                        <p class="mb-0">Tìm thấy <strong><?php echo $totalnumber; ?></strong> sản phẩm phù hợp</p>
                    </div>
                    <div class="results-count">
                        <?php echo $totalnumber; ?> sản phẩm
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Danh sách sản phẩm -->
        <?php if($totalnumber > 0): ?>
            <div class="row">
                <?php while ($kq = $resultSearch->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card">
                            <div class="product-img">
                                <img src="<?php echo $kq['image']; ?>" alt="<?php echo htmlspecialchars($kq['name']); ?>">
                            </div>
                            <div class="product-info">
                                <h6 class="product-name"><?php echo $kq['name']; ?></h6>
                                <div class="product-price">
                                    <?php echo number_format($kq['price'], 0, ',', '.'); ?> đ
                                </div>
                                <div class="product-info">
                                            <a href="addcart.php?id=<?php echo $kq['id']; ?>">
                                                <button type="button" class="btn btn-dark">
                                                    <label style="color: red;">&hearts;</label> Mua hàng <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                            <a href="detail.php?id=<?php echo $kq['id']; ?>">
                                                <button type="button" class="btn btn-dark">
                                                    <label style="color: red;">&hearts;</label> Chi Tiết <label
                                                        style="color: red;">&hearts;</label>
                                                </button>
                                            </a>
                                        </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x"></i>
                </div>
                <h4 class="mb-3">Không tìm thấy sản phẩm nào</h4>
                <p class="text-muted mb-4">Hãy thử thay đổi từ khóa tìm kiếm hoặc danh mục</p>
                <a href="search.php" class="btn btn-primary" style="background: #6a11cb; border-color: #6a11cb;">
                    <i class="fas fa-redo me-2"></i>Thử lại
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- footer -->
    <div class="container mt-5">
        <?php include("model/footer.php"); ?>
    </div>
    <!-- /footer -->

<script>
    // Xóa từng bộ lọc
    function removeFilter(filterName) {
        const url = new URL(window.location.href);
        url.searchParams.delete(filterName);
        window.location.href = url.toString();
    }
    
    // Hiệu ứng đơn giản
    document.addEventListener('DOMContentLoaded', function() {
        // Hiệu ứng cho card sản phẩm
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.08)';
            });
        });
    });
</script>
</body>
</html>