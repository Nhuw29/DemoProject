<div class="container">
    <div class="banner wow lightSpeedIn">
        <div class="row">
            <?php
            echo "<h3 class='title text-center'>BANNER - PNV27</h3>";
            require_once("connect.php");
            
            try {
                $sql = "SELECT image FROM slides WHERE status=2";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($result) > 0) {
                    foreach ($result as $kg) {
                        $db_path = $kg['image']; // đường dẫn trong database: "images/banner/2.jpg"
                        $filename = basename($db_path); // lấy tên file: "2.jpg"
                        $correct_path = "../images/banner/" . $filename;
            ?>
                        <div class="col-md-3 col-sm-4">
                            <div class="thumbnail">
                                <div class="banner">
                                    <img src="<?php echo htmlspecialchars($correct_path); ?>" 
                                         alt="Banner image" 
                                         width="100%" 
                                         height="160">
                                </div>
                            </div>
                        </div>
            <?php 
                    }
                } else {
                    echo "<p>No banners found</p>";
                }
            } catch(PDOException $e) {
                echo "<p>Error loading banners: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
</div>