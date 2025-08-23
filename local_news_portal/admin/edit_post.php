<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
$id = $_GET['id'] ?? null;
if (!$id) {
    die('Thiếu ID bài viết.');
}

// Lấy thông tin bài viết cần sửa
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die('Không tìm thấy bài viết.');
}

$errors = [];
$title = $post['title'];
$content = $post['content'];
$category_id = $post['category_id'];
$currentImage = $post['image'];

// Lấy danh sách chuyên mục
$categoryStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categoryStmt->fetchAll();

// Xử lý khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $category_id = $_POST['category_id'] ?? null;

    if ($title === '') $errors[] = "Tiêu đề không được để trống.";
    if ($content === '') $errors[] = "Nội dung không được để trống.";

    // Xử lý ảnh mới nếu có upload
    $imageName = $currentImage;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $imageName);
    }

    // Update nếu không có lỗi
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE posts 
            SET title = ?, content = ?, category_id = ?, image = ?
            WHERE id = ? AND author_id = ?
        ");
        $stmt->execute([$title, $content, $category_id, $imageName, $id, $_SESSION['admin_user']['id']]);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa bài viết - <?= SITE_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h2 class="mb-4">Sửa bài viết</h2>

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
        <label class="form-label">Ảnh minh họa</label><br>
        <?php if ($currentImage): ?>
          <img src="../uploads/<?= $currentImage ?>" alt="Ảnh hiện tại" class="mb-2" style="max-width: 200px;"><br>
        <?php endif; ?>
        <input type="file" name="image" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary">Cập nhật bài viết</button>
      <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </form>
  </div>
</body>
</html>
