
<?php
$servername = "localhost";
$port = 3306;
$username = "root";
$password = "";

try {
    $db_name = "THOITRANG";
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully<br>";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
