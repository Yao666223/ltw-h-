<?php
session_start();
require_once '../config.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Lấy danh sách bài viết
$stmt = $pdo->query("
    SELECT posts.*, categories.name AS category_name, users.username AS author_name
    FROM posts
    JOIN categories ON posts.category_id = categories.id
    JOIN users ON posts.author_id = users.id
    ORDER BY posts.created_at DESC
");

$posts = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>
<div class="container mt-4">
    <h3>📚 Danh sách bài viết</h3>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success">✅ Thêm bài viết thành công!</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Tác giả</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td><?= htmlspecialchars($post['category_name']) ?></td>
                <td><?= htmlspecialchars($post['author_name']) ?></td>
                <td><?= $post['created_at'] ?></td>
                <td>
                    <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
