<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_user']) || $_SESSION['admin_user']['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

$errors = [];
$username = '';
$role = 'staff';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';

    if ($username === '') $errors[] = 'Vui lòng nhập username.';
    if ($password === '') $errors[] = 'Vui lòng nhập password.';
    if (!in_array($role, ['admin','staff'])) $errors[] = 'Role không hợp lệ.';

    // Kiểm tra trùng username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $errors[] = 'Username đã tồn tại.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $stmt->execute([$username, $hash, $role]);
        header('Location: index.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thêm Người dùng</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><div class="container mt-4">
  <h2>Thêm Người dùng Mới</h2>
  <?php if ($errors): ?><div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username) ?>"></div>
    <div class="mb-3"><label class="form-label">Password</label>
      <input type="password" name="password" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Role</label>
      <select name="role" class="form-select">
        <option value="staff" <?= $role==='staff'?'selected':'' ?>>Staff</option>
        <option value="admin" <?= $role==='admin'?'selected':'' ?>>Admin</option>
      </select></div>
    <button type="submit" class="btn btn-primary">Thêm</button>
    <a href="index.php" class="btn btn-secondary">Quay lại</a>
  </form>
</div></body></html>
