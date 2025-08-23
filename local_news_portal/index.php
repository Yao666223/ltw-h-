<?php
require_once 'config.php';
include 'header.php';

// Phân trang
$perPage = 6;
$page    = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset  = ($page - 1) * $perPage;

// Đếm tổng bài đã publish
$countSql  = "SELECT COUNT(*) FROM posts WHERE status = 'published'";
$totalPosts = $pdo->query($countSql)->fetchColumn();
$totalPages = ceil($totalPosts / $perPage);

// Lấy danh sách bài đã publish
$sql = "
    SELECT 
      p.id, p.title, p.content, p.image, p.created_at,
      c.name AS category_name,
      u.username AS author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users    u ON p.author_id   = u.id
    WHERE p.status = 'published'
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
  <h2 class="mb-4 text-center">Bản Tin Địa Phương</h2>

  <?php if (empty($posts)): ?>
    <p class="text-muted text-center">Chưa có bài viết nào.</p>
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
                🗂 <?= htmlspecialchars($post['category_name'] ?? 'Chưa phân loại') ?> |
                🕒 <?= date('d/m/Y', strtotime($post['created_at'])) ?>
              </p>
              <p class="card-text"><?= mb_strimwidth(strip_tags($post['content']), 0, 100, '...') ?></p>
              <a href="post_detail.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary mt-auto">
                Xem chi tiết
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Phân trang -->
  <?php if ($totalPages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
          <a class="page-link" href="index.php?page=<?= $page - 1 ?>">&laquo;</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item<?= $i === $page ? ' active' : '' ?>">
            <a class="page-link" href="index.php?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
          <a class="page-link" href="index.php?page=<?= $page + 1 ?>">&raquo;</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
