<?php
require_once('connect.php');
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Fashion MyLiShop</title>
    <!-- Bootstrap 3 CSS (CDN) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        /* đảm bảo chỉ 1 item hiển thị và ảnh fit */
        .carousel {
            border-radius: 8px;
            overflow: hidden;
        }

        .carousel .item img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            display: block;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="col-sm-12 col-md-12 wow zoomIn">
            <div id="myCarousel" class="carousel slide" data-interval="false" style="max-height:500px;">
                <?php
                if (!isset($conn)) {
                    echo "<h3 style='color: red; text-align: center;'>Lỗi: Biến kết nối PDO (\$conn) chưa được khởi tạo.</h3>";
                    exit();
                }

                $sql = "SELECT image FROM slides WHERE status = 1 ORDER BY id ASC";
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $total_slides = count($results);

                    if ($total_slides > 0) {
                        $i = 0;
                ?>
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <?php for ($j = 0; $j < $total_slides; $j++): ?>
                                <li data-target="#myCarousel" data-slide-to="<?php echo $j; ?>" <?php echo ($j === 0) ? 'class="active"' : ''; ?>></li>
                            <?php endfor; ?>
                        </ol>

                        <!-- Slides -->
                        <div class="carousel-inner" role="listbox">
                            <?php foreach ($results as $row):
                                $cls = ($i === 0) ? 'item active' : 'item';
                                $image_path = htmlspecialchars($row['image']);
                                if (substr($image_path, 0, 1) !== '/') $image_path = '/' . $image_path;
                            ?>
                                <div class="<?php echo $cls; ?>">
                                    <img src="<?php echo $image_path; ?>" alt="Slideshow">
                                </div>
                            <?php $i++;
                            endforeach; ?>
                        </div>
                <?php
                    } else {
                        echo "<p class='text-center text-muted'>Không tìm thấy slide nào có status=1.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='text-center' style='color: red;'>Lỗi truy vấn database: " . $e->getMessage() . "</p>";
                }
                ?>

                <!-- Controls -->
                <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev" style="background-image:none; opacity:1;">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true" style="font-size:30px;color:white; top:50%; position:absolute; transform:translateY(-50%); left:10px;"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next" style="background-image:none; opacity:1;">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true" style="font-size:30px;color:white; top:50%; position:absolute; transform:translateY(-50%); right:10px;"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>

    <!-- jQuery + Bootstrap 3 JS (CDN) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script>
        $(function() {
            var $carousel = $('#myCarousel');
            var total = $carousel.find('.carousel-indicators li').length;
            if (total <= 1) return; // không cần chạy nếu chỉ 0 hoặc 1 slide

            var index = 0;
            var direction = 'next'; // 'next' hoặc 'prev'
            var delay = 3000; // ms

            // cập nhật index khi slide chuyển xong (tránh lệch chỉ số)
            $carousel.on('slid.bs.carousel', function() {
                index = $carousel.find('.carousel-inner .item.active').index();
            });

            // bắt đầu timer ping-pong
            var timer = setInterval(function() {
                if (direction === 'next') {
                    $carousel.carousel('next');
                    index++;
                } else {
                    $carousel.carousel('prev');
                    index--;
                }

                if (index >= total - 1) {
                    direction = 'prev';
                } else if (index <= 0) {
                    direction = 'next';
                }
            }, delay);

            // nếu người dùng di chuột lên carousel thì tạm dừng
            $carousel.hover(function() {
                clearInterval(timer);
            }, function() {
                // restart
                timer = setInterval(function() {
                    if (direction === 'next') {
                        $carousel.carousel('next');
                        index++;
                    } else {
                        $carousel.carousel('prev');
                        index--;
                    }
                    if (index >= total - 1) direction = 'prev';
                    if (index <= 0) direction = 'next';
                }, delay);
            });
        });
    </script>
</body>

</html>