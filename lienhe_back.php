<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    
    require_once 'model/connect.php';

    if (isset($_POST['sendcontact'])) 
    {
        $namect = $_POST['contact-name'] ?? '';
        $emailct = $_POST['contact-email'] ?? '';
        $subject = $_POST['contact-subject'] ?? '';
        $contentct = $_POST['contact-content'] ?? '';

        try {
            // Sử dụng PDO prepared statement để tránh SQL Injection
            $sql = "INSERT INTO contacts(name, email, title, contents, created) 
                    VALUES(:name, :email, :title, :contents, NOW())";
            
            $stmt = $conn->prepare($sql);
            
            // Bind parameters
            $stmt->bindParam(':name', $namect, PDO::PARAM_STR);
            $stmt->bindParam(':email', $emailct, PDO::PARAM_STR);
            $stmt->bindParam(':title', $subject, PDO::PARAM_STR);
            $stmt->bindParam(':contents', $contentct, PDO::PARAM_STR);
            
            // Thực thi truy vấn
            $result = $stmt->execute();
            
            if ($result) 
            {
                header("location:lienhe.php?cs=success");
                exit();
            } 
            else 
            {
                header("location:lienhe.php?cf=failed");
                exit();
            }
        } 
        catch(PDOException $e) 
        {
            // Log lỗi và chuyển hướng
            error_log("Lỗi PDO: " . $e->getMessage());
            header("location:lienhe.php?cf=failed");
            exit();
        }
    }
    else {
        // Nếu không có POST data, quay lại trang liên hệ
        header("location:lienhe.php");
        exit();
    }
?>