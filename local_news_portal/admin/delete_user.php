<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_user']) || $_SESSION['admin_user']['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

$id = $_GET['id'] ?? null;
if ($id && is_numeric($id)) {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ? AND role != "admin"');
    $stmt->execute([$id]);
}
header('Location: index.php');
exit;
?>