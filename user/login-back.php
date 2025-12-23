<?php
    session_start();
    error_reporting(E_ALL ^ E_DEPRECATED);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    
    require_once('../model/connect.php');

    if (isset($_POST['submit']))
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user_data = false;
        $role = 0;
        $table_source = '';

        try {
            // -----------------------------------------------------
            // BƯỚC 1: KIỂM TRA TÀI KHOẢN ADMIN TRONG BẢNG 'admin'
            // -----------------------------------------------------
            $sql_admin = "SELECT id, username FROM admin WHERE username = :username AND password = :password";
            $stmt_admin = $conn->prepare($sql_admin);
            $stmt_admin->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt_admin->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt_admin->execute();
            
            if ($stmt_admin->rowCount() > 0) {
                $user_data = $stmt_admin->fetch(PDO::FETCH_ASSOC);
                $role = 1; // Admin từ bảng admin
                $table_source = 'admin';
            }

            // -----------------------------------------------------
            // BƯỚC 2: KIỂM TRA TÀI KHOẢN TRONG BẢNG 'users'
            // -----------------------------------------------------
            if (!$user_data) {
                
                $sql_users = "SELECT id, username, role FROM users WHERE username = :username AND password = :password";
                $stmt_users = $conn->prepare($sql_users);
                $stmt_users->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt_users->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt_users->execute();

                if ($stmt_users->rowCount() > 0) {
                    $user_data = $stmt_users->fetch(PDO::FETCH_ASSOC);
                    $role = $user_data['role']; 
                    $table_source = 'users';
                } else {
                    // Nếu không đúng với plain text, thử với MD5
                    $sql_users_md5 = "SELECT id, username, role FROM users WHERE username = :username AND password = md5(:password)";
                    $stmt_users_md5 = $conn->prepare($sql_users_md5);
                    $stmt_users_md5->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt_users_md5->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt_users_md5->execute();
                    
                    if ($stmt_users_md5->rowCount() > 0) {
                        $user_data = $stmt_users_md5->fetch(PDO::FETCH_ASSOC);
                        $role = $user_data['role']; 
                        $table_source = 'users';
                    }
                }
            }

            // -----------------------------------------------------
            // BƯỚC 3: XỬ LÝ KẾT QUẢ ĐĂNG NHẬP
            // -----------------------------------------------------
            if ($user_data) 
            {
                // Khởi tạo Session
                $_SESSION['username'] = $user_data['username']; 
                $_SESSION['id-user'] = $user_data['id'];
                $_SESSION['user_role'] = $role;
                $_SESSION['login_source'] = $table_source;

                // Kiểm tra Vai trò (Role)
                // CHỈ SỬA DÒNG NÀY: Thay đổi điều kiện từ $role > 0 thành $role == 0
                if ($role == 0 || $table_source == 'admin') // Admin (role = 0 từ users) hoặc từ bảng admin
                {
                    header("location:../admin/product-list.php"); 
                    exit();
                } 
                else 
                {
                    header("location:../index.php?ls=success");
                    exit();
                }

            } else {
                $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không hợp lệ!';
                header("location:../user/login.php?error=wrong");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau!';
            error_log("Login Error: " . $e->getMessage());
            header("location:../user/login.php?error=system");
            exit();
        }
    } else {
        header("location:../user/login.php");
        exit();
    }
?>