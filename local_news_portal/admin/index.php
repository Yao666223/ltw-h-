<?php
require_once '../config.php';
include '../header.php';
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['admin_user'];
$isAdmin = ($user['role'] === 'admin');
$userId = $user['id'];

// Lấy posts
if ($isAdmin) {
    $sql = "SELECT p.id, p.title, p.status, c.name AS category_name, p.created_at, u.username AS author_name
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN users u ON p.author_id = u.id
            ORDER BY p.created_at DESC";
    $stmt = $pdo->query($sql);
} else {
    $sql = "SELECT p.id, p.title, p.status, c.name AS category_name, p.created_at
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.author_id = :author_id
            ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['author_id' => $userId]);
}
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy categories và users nếu admin
if ($isAdmin) {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $users = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - <?= SITE_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Bảng điều khiển</h2>
    <div>
      <span class="me-3">👋 <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role']) ?>)</span>
      <a href="logout.php" class="btn btn-outline-secondary btn-sm">Đăng xuất</a>
    </div>
  </div>

  <!-- Quản lý Bài viết -->
  <div class="mb-4">
    <?php if ($isAdmin): ?>
      <a href="add_post.php" class="btn btn-success">+ Thêm bài viết mới</a>
    <?php else: ?>
      <a href="add_post.php" class="btn btn-primary">+ Tạo bài viết (staff)</a>
    <?php endif; ?>
  </div>
  <table class="table table-bordered table-striped mb-5">
    <thead>
      <tr>
        <th>Tiêu đề</th>
        <th>Chuyên mục</th>
        <?php if ($isAdmin): ?><th>Tác giả</th><?php endif; ?>
        <th>Ngày tạo</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $post): ?>
        <tr>
          <td><?= htmlspecialchars($post['title']) ?></td>
          <td><?= htmlspecialchars($post['category_name'] ?? '---') ?></td>
          <?php if ($isAdmin): ?><td><?= htmlspecialchars($post['author_name']) ?></td><?php endif; ?>
          <td><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
          <td><?= htmlspecialchars($post['status']) ?></td>
          <td>
            <?php if ($isAdmin && $post['status'] === 'pending'): ?>
              <a href="publish_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-success">Phê duyệt</a>
            <?php endif; ?>
            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
            <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($isAdmin): ?>
    <!-- Quản lý Chuyên mục -->
    <h4 class="mt-5">📂 Quản lý Chuyên mục</h4>
    <a href="add_category.php" class="btn btn-primary mb-3">+ Thêm chuyên mục</a>
    <table class="table table-bordered table-striped mb-5">
      <thead>
        <tr><th>ID</th><th>Tên chuyên mục</th><th>Hành động</th></tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr>
            <td><?= $cat['id'] ?></td>
            <td><?= htmlspecialchars($cat['name']) ?></td>
            <td>
              <a href="edit_category.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
              <a href="delete_category.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Quản lý Người dùng -->
    <h4 class="mt-5">👥 Quản lý Người dùng</h4>
    <a href="add_user.php" class="btn btn-secondary mb-3">+ Thêm nhân viên</a>
    <table class="table table-bordered table-striped mb-5">
      <thead>
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Ngày tạo</th><th>Hành động</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
            <td>
              <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
              <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa người dùng?')">Xóa</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</div>
</body>
</html>
