<?php
require_once 'config.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT posts.*, categories.name AS category_name, users.username
                       FROM posts
                       JOIN categories ON posts.category_id = categories.id
                       JOIN users ON posts.author_id = users.id
                       WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("Bài viết không tồn tại!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <a href="index.php" class="btn btn-sm btn-secondary mb-3">&larr; Về trang chủ</a>

        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p class="text-muted">
            🗂 <?= htmlspecialchars($post['category_name']) ?> | ✍️ <?= htmlspecialchars($post['username']) ?> | 🕒 <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
        </p>

        <hr>
        <div>
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
    </div>
</body>
</html>
