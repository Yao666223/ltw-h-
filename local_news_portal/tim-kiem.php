<?php
require_once 'config.php';
include 'header.php';

$search      = trim($_GET['q'] ?? '');
$timeFilter  = $_GET['time'] ?? 'all';
$sort        = $_GET['sort'] ?? 'desc';
$categoryId  = intval($_GET['category_id'] ?? 0);

// Xây dựng điều kiện WHERE
$where  = [];
$params = [];

// Tìm theo tiêu đề
if ($search !== '') {
    $where[]        = "p.title LIKE :search";
    $params[':search'] = "%{$search}%";
}

// Lọc theo thời gian
if ($timeFilter === 'today') {
    $where[] = "p.created_at >= CURDATE()";
} elseif ($timeFilter === 'week') {
    $where[] = "p.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($timeFilter === 'month') {
    $where[] = "p.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}

// Lọc theo chuyên mục
if ($categoryId > 0) {
    $where[]            = "p.category_id = :cat";
    $params[':cat']     = $categoryId;
}

// Chỉ bài đã publish
$where[] = "p.status = 'published'";

// Ghép WHERE & ORDER
$whereSql = 'WHERE ' . implode(' AND ', $where);
$order    = $sort === 'asc' ? 'ASC' : 'DESC';

$sql = "
  SELECT 
    p.id, p.title, p.content, p.image, p.created_at,
    c.name AS category_name
  FROM posts p
  LEFT JOIN categories c ON p.category_id = c.id
  {$whereSql}
  ORDER BY p.created_at {$order}
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách chuyên mục cho dropdown
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
  <h2 class="mb-4">Kết quả tìm kiếm: “<?= htmlspecialchars($search) ?>”</h2>

  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-6">
      <input type="text" name="q" class="form-control" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2">
      <select name="time" class="form-select">
        <option value="all"   <?= $timeFilter === 'all'  ? 'selected' : '' ?>>Tất cả thời gian</option>
        <option value="today" <?= $timeFilter === 'today'? 'selected' : '' ?>>Hôm nay</option>
        <option value="week"  <?= $timeFilter === 'week' ? 'selected' : '' ?>>7 ngày</option>
        <option value="month" <?= $timeFilter === 'month'? 'selected' : '' ?>>1 tháng</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="sort" class="form-select">
        <option value="desc" <?= $sort === 'desc'? 'selected' : '' ?>>Mới nhất</option>
        <option value="asc"  <?= $sort === 'asc' ? 'selected' : '' ?>>Cũ nhất</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="category_id" class="form-select">
        <option value="0">Tất cả chuyên mục</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $categoryId === $cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>

  <?php if (empty($results)): ?>
    <p class="text-muted">Không tìm thấy kết quả phù hợp.</p>
  <?php else: ?>
    <div class="row">
      <?php foreach ($results as $post): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <?php if (!empty($post['image'])): ?>
              <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top" style="height:200px;object-fit:cover;">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
              <p class="card-text text-muted small mb-2">
                <?= htmlspecialchars($post['category_name'] ?: '---') ?> |
                <?= date('d/m/Y', strtotime($post['created_at'])) ?>
              </p>
              <a href="post_detail.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary mt-auto">Xem chi tiết</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
