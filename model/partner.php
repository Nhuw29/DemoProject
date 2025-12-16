<?php
include "connect.php"; // kết nối DB
?>
<div class="container">
    <h3 class="title text-center" style="margin-bottom:20px; color:#ff0066; font-weight:700;">
        PARTNER
    </h3>
    <div class="row">
        <?php
        $sql = "SELECT image FROM slides WHERE status = 3";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // In case of error, treat as no rows
            $rows = [];
        }

        if (!empty($rows)) {
            foreach ($rows as $kq) {
                $imagePath = "../" . $kq['image'];
        ?>
                <div class="col-md-2 col-sm-4 col-xs-6 text-center" style="margin-bottom:15px;">
                    <div class="thumbnail" style="padding:5px; border:none; background:#fff;">
                        <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES); ?>"
                            alt="Partner Slide"
                            class="img-responsive"
                            style="width:100%; height:160px; object-fit:contain;">
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<div class="col-12 text-center"><p>Không có slide nào có status = 3.</p></div>';
        }
        ?>
    </div>
</div>