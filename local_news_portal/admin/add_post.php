<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
$errors = [];
$title = '';
$content = '';
$category_id = '';

// Lấy danh sách chuyên mục
$categoryStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categoryStmt->fetchAll();

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $category_id = $_POST['category_id'] ?? null;
    $author_id   = 1; // Tạm đặt admin ID là 1

    // Validate
    if ($title === '') $errors[] = "Tiêu đề không được để trống.";
    if ($content === '') $errors[] = "Nội dung không được để trống.";
    if (!$category_id) $errors[] = "Vui lòng chọn chuyên mục.";

    // Xử lý upload ảnh
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = '../uploads/' . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    }

    // Nếu không có lỗi thì insert
    if (empty($errors)) {
        $authorId = $_SESSION['admin_user']['id'];
        $stmt = $pdo->prepare("
            INSERT INTO posts (title, content, category_id, image, author_id, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$title, $content, $category_id, $imageName, $authorId]);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm bài viết - <?= SITE_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h2 class="mb-4">Thêm bài viết mới</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Tiêu đề</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nội dung</label>
        <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($content) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Chuyên mục</label>
        <select name="category_id" class="form-select" required>
          <option value="">-- Chọn chuyên mục --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category_id ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Ảnh minh họa (nếu có)</label>
        <input type="file" name="image" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary">Lưu bài viết</button>
      <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </form>
  </div>
</body>
</html>
