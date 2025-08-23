<?php
require_once 'config.php';
include 'header.php';

// Validate category ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Chuyên mục không hợp lệ.");
}
$catId = (int)$_GET['id'];

// Lấy tên chuyên mục
$stmtCat = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$stmtCat->execute([$catId]);
$cat = $stmtCat->fetch(PDO::FETCH_ASSOC);
if (!$cat) {
    die("Chuyên mục không tồn tại.");
}

// Lấy bài viết thuộc chuyên mục đã publish
$sql = "
    SELECT 
      p.id, p.title, p.content, p.image, p.created_at,
      c.name AS category_name,
      u.username AS author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users    u ON p.author_id   = u.id
    WHERE p.category_id = ? AND p.status = 'published'
    ORDER BY p.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$catId]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
  <h2 class="mb-4">Chuyên mục: <?= htmlspecialchars($cat['name']) ?></h2>

  <?php if (empty($posts)): ?>
    <p class="text-muted">Chưa có bài viết nào trong chuyên mục này.</p>
  <?php else: ?>
    <div class="row">
      <?php foreach ($posts as $post): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm border-0">
            <?php if (!empty($post['image'])): ?>
              <img src="uploads/<?= htmlspecialchars($post['image']) ?>"
                   class="card-img-top"
                   style="height:200px;object-fit:cover;"
                   alt="<?= htmlspecialchars($post['title']) ?>">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
              <p class="card-text text-muted small mb-2">
                🕒 <?= date('d/m/Y', strtotime($post['created_at'])) ?>
              </p>
              <p class="card-text"><?= mb_strimwidth(strip_tags($post['content']), 0, 100, '...') ?></p>
              <a href="post_detail.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary mt-auto">Xem chi tiết</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
