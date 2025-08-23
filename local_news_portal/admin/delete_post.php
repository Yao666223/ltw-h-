<?php
require_once '../config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Thiếu ID bài viết.');
}

// Xoá ảnh cũ nếu có
$stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if ($post && $post['image']) {
    $imagePath = '../uploads/' . $post['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // Xóa file ảnh khỏi server
    }
}

// Xoá bài viết khỏi database
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
