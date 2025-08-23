<?php
require_once '../config.php';
session_start();

// Kiểm tra đã đăng nhập và là admin
if (!isset($_SESSION['admin_user']) || $_SESSION['admin_user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Lấy ID và cập nhật status
$id = $_GET['id'] ?? null;
if ($id && is_numeric($id)) {
    $stmt = $pdo->prepare('UPDATE posts SET status = ? WHERE id = ?');
    $stmt->execute(['published', $id]);
}

header('Location: index.php');
exit;
?>
