<?php
require_once 'config.php';

// Lấy danh mục để hiển thị menu
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= SITE_NAME ?></title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <!-- Font Awesome for search icon -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
      <?= SITE_NAME ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto">
        <?php foreach($categories as $cat): ?>
          <li class="nav-item">
            <a class="nav-link"
               href="<?= BASE_URL ?>category.php?id=<?= $cat['id'] ?>">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- Form tìm kiếm có icon kính lúp -->
      <form class="d-flex" method="GET" action="<?= BASE_URL ?>tim-kiem.php">
        <input class="form-control me-2"
               type="search"
               name="q"
               placeholder="Tìm kiếm..."
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button class="btn btn-outline-secondary" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </form>

      <ul class="navbar-nav ms-3">
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>admin/index.php">
            Quản lý
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
