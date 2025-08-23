<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_user']) || $_SESSION['admin_user']['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: index.php'); exit;
}

// Lấy user cần sửa
$stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { header('Location: index.php'); exit; }

$errors = [];
$username = $user['username'];
$role = $user['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';

    if ($username === '') $errors[] = 'Username không được để trống.';
    if (!in_array($role, ['admin','staff'])) $errors[] = 'Role không hợp lệ.';

    // Kiểm tra đổi username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Username đã tồn tại.';
    }

    if (empty($errors)) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = 'UPDATE users SET username=?, password=?, role=? WHERE id = ?';
            $params = [$username, $hash, $role, $id];
        } else {
            $sql = 'UPDATE users SET username=?, role=? WHERE id = ?';
            $params = [$username, $role, $id];
        }
        $upd = $pdo->prepare($sql);
        $upd->execute($params);
        header('Location: index.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Sửa Người dùng</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><div class="container mt-4">
  <h2>Sửa Người dùng #<?= $id ?></h2>
  <?php if ($errors): ?><div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username) ?>"></div>
    <div class="mb-3"><label class="form-label">New Password (nếu đổi)</label>
      <input type="password" name="password" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Role</label>
      <select name="role" class="form-select">
        <option value="staff" <?= $role==='staff'?'selected':'' ?>>Staff</option>
        <option value="admin" <?= $role==='admin'?'selected':'' ?>>Admin</option>
      </select></div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="index.php" class="btn btn-secondary">Quay lại</a>
  </form>
</div></body></html>