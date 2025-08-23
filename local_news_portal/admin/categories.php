<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}

// Thêm chuyên mục mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_category'])) {
    $newCategory = trim($_POST['new_category']);
    if ($newCategory !== '') {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $newCategory]);
        header("Location: categories.php");
        exit;
    }
}

// Xóa chuyên mục
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: categories.php");
    exit;
}

// Lấy danh sách chuyên mục
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý chuyên mục - <?= SITE_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2 class="mb-4">Quản lý chuyên mục</h2>

  <form method="POST" class="mb-3 d-flex">
    <input type="text" name="new_category" class="form-control me-2" placeholder="Tên chuyên mục mới" required>
    <button type="submit" class="btn btn-primary">Thêm</button>
  </form>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Tên chuyên mục</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td><?= $cat['id'] ?></td>
          <td><?= htmlspecialchars($cat['name']) ?></td>
          <td>
            <a href="edit_category.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
            <a href="categories.php?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa chuyên mục?')">Xóa</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="index.php" class="btn btn-secondary mt-3">← Quay về Dashboard</a>
</div>
</body>
</html>
