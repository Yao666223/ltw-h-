<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $id]);
}
header("Location: index.php");
exit;
