<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL ?>public/assets/img/icon.png">
  <title>Gestión Gastos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo BASE_URL ?>public/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="<?php echo BASE_URL ?>public/assets/js/jquery-3.6.0.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?php echo BASE_URL ?>dashboard">
      <i class="bi bi-wallet2 me-2"></i> Gestión Gastos
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL ?>dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL ?>ingreso/registrar"><i class="bi bi-cash-stack"></i> Ingresos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL ?>gasto/registrar"><i class="bi bi-credit-card"></i> Gastos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL ?>meta/registrar"><i class="bi bi-bullseye"></i> Metas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL ?>categoria"><i class="bi bi-tags"></i> Categorías</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
