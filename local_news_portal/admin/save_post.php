<?php
session_start();
require_once '../config.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Kiểm tra dữ liệu gửi về
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);
    $author_id = $_SESSION['user_id'];

    if ($title !== "" && $content !== "") {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, category_id, author_id, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $content, $category_id, $author_id]);

        header("Location: manage_posts.php?msg=success");
        exit;
    } else {
        echo "❌ Thiếu tiêu đề hoặc nội dung.";
    }
} else {
    echo "❌ Yêu cầu không hợp lệ.";
}
?>
