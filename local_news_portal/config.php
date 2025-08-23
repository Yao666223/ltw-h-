<?php
$host = 'localhost';
$dbname = 'local_news_portal';
$username = 'root';
$password = ''; // Nếu dùng XAMPP, thường để trống
// config.php
define('SITE_NAME', 'Tin tức online');

define('BASE_URL', 'http://localhost/local_news_portal/');



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}
?>

