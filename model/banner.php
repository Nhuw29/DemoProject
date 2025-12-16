<div class="container-fluid">
    <div class="banner-wrapper wow lightSpeedIn">
        <div class="row">
            <?php
            require_once("connect.php");

            if (!isset($conn)) {
                echo "<h3 style='color: red; text-align: center;'>Lỗi: Biến kết nối PDO (\$conn) chưa được khởi tạo.</h3>";
                exit();
            }

            $sql = "SELECT image FROM slides WHERE status = :status LIMIT 1";

            try {
                $stmt = $conn->prepare($sql);
                $stmt->execute(['status' => 2]);
                $kq = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($kq) {
                    $image_src_fixed = "/images/slide/slide-2.jpg";
            ?>
                    <div class="col-12 p-0">
                        <div class="banner-overlay" style="
                            position: relative;
                            height: 380px;
                            overflow: hidden;
                            background-color: #333;
                        ">
                            <div class="image-overlay" style="
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background-color: rgba(0, 0, 0, 0.3); 
                                z-index: 5;
                            "></div>

                            <h3 class='title' style="
                                position: absolute; 
                                top: 50%; 
                                left: 50%; 
                                transform: translate(-50%, -50%);
                                z-index: 10; 
                                color: #FFFFFF; 
                                font-family: sans-serif; 
                                font-size: 2.2em; 
                                font-weight: 900; 
                                text-transform: uppercase;
                                letter-spacing: 2px; 
                                text-shadow: 0 0 8px rgba(0,0,0,0.7), 0 0 20px rgba(255,255,255,0.3);
                                text-align: center;
                            ">
                                BANNER- PNV 27
                            </h3>

                            <img src="<?php echo $image_src_fixed; ?>"
                                alt="Banner Image"
                                class="img-fluid"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
            <?php
                } else {
                    echo "<p class='text-center text-muted'>Chưa có slide nào có status = 2 để hiển thị.</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='text-center' style='color: red;'>Lỗi truy vấn cơ sở dữ liệu: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
</div>