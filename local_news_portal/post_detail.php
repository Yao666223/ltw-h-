<?php
require_once 'config.php';
include 'header.php';
// Xác định và validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID bài viết không hợp lệ.");
}
$id = (int)$_GET['id'];

// Xử lý gửi bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['user_name'] ?? '');
    $email = trim($_POST['user_email'] ?? '') ?: null;
    $cont  = trim($_POST['content'] ?? '');

    if ($name === '' || $cont === '') {
        $commentError = 'Vui lòng điền tên và nội dung bình luận.';
    } else {
        $iStmt = $pdo->prepare("INSERT INTO comments (post_id, user_name, user_email, content, created_at) VALUES (?, ?, ?, ?, NOW())");
        $iStmt->execute([$id, $name, $email, $cont]);
        header("Location: post_detail.php?id=$id");
        exit;
    }
}

// Lấy thông tin bài viết
$sql = "SELECT p.*, c.name AS category_name, u.username AS author_name
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) {
    die("Bài viết không tồn tại.");
}

// Lấy tất cả bình luận cho bài này
$cStmt = $pdo->prepare("SELECT user_name, user_email, content, created_at FROM comments WHERE post_id = ? ORDER BY created_at DESC");
$cStmt->execute([$id]);
$comments = $cStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($post['title']) ?> - <?= SITE_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for share icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <!-- Open Graph Meta -->
  <meta property="og:title" content="<?= htmlspecialchars($post['title']) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars(substr(strip_tags($post['content']), 0, 150)) ?>" />
  <?php if (!empty($post['image'])): ?>
    <meta property="og:image" content="<?= BASE_URL ?>uploads/<?= htmlspecialchars($post['image']) ?>" />
  <?php endif; ?>
  <meta property="og:url" content="<?= BASE_URL ?>post_detail.php?id=<?= $post['id'] ?>" />
  <meta property="og:type" content="article" />
</head>
<body>

<div class="container mt-5">
  <a href="index.php" class="btn btn-sm btn-secondary mb-3">&larr; Quay lại</a>

  <div class="card shadow-sm mb-4">
    <?php if (!empty($post['image'])): ?>
      <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top" style="max-height:400px;object-fit:cover;">
    <?php endif; ?>
    <div class="card-body">
      <h2 class="card-title fw-bold"><?= htmlspecialchars($post['title']) ?></h2>
      <p class="text-muted small">
        🗂 <?= htmlspecialchars($post['category_name'] ?? 'Chưa phân loại') ?> |
        🕒 <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?> |
        ✍️ <?= htmlspecialchars($post['author_name']) ?>
      </p>
      <hr>
      <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])) ?></p>

      <!-- Share Buttons -->
      <div class="share-buttons mt-4">
        <span class="me-2 fw-semibold">Chia sẻ bài viết:</span>
        </a>
        <button id="copyLinkBtn" class="btn btn-sm btn-outline-dark">
          <i class="fas fa-link"></i> Sao chép link
        </button>
      </div>

    </div>
  </div>

  <!-- Hiển thị bình luận -->
  <h4>Bình luận</h4>
  <?php if ($comments): ?>
    <?php foreach ($comments as $c): ?>
      <div class="mb-3 border-bottom pb-2">
        <strong><?= htmlspecialchars($c['user_name']) ?></strong>
        <?php if ($c['user_email']): ?>
          <small>(<?= htmlspecialchars($c['user_email']) ?>)</small>
        <?php endif; ?>
        <p class="mb-1"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></small>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-muted">Chưa có bình luận nào.</p>
  <?php endif; ?>

  <!-- Form gửi bình luận -->
  <hr>
  <h5>Để lại bình luận</h5>
  <?php if (!empty($commentError)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($commentError) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Tên của bạn <span class="text-danger">*</span></label>
      <input type="text" name="user_name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email (không bắt buộc)</label>
      <input type="email" name="user_email" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung <span class="text-danger">*</span></label>
      <textarea name="content" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
  </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<!-- Copy Link Script -->
<script>
  document.getElementById('copyLinkBtn').addEventListener('click', function() {
    const url = '<?= BASE_URL ?>post_detail.php?id=<?= $post['id'] ?>';
    navigator.clipboard.writeText(url).then(() => {
      alert('Đã sao chép link: ' + url);
    }).catch(err => console.error('Lỗi khi sao chép: ', err));
  });
</script>

<?php include 'footer.php'; ?>
