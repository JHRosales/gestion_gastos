<div class="container mt-5">
  <div class="row">
    <div class="col-12 text-center mb-4">
      <a href="<?php echo BASE_URL ?>ingreso/registrar" class="btn btn-success me-2"><i class="bi bi-plus-circle me-1"></i>Registrar nuevo ingreso</a>
      <a href="<?php echo BASE_URL ?>gasto/registrar" class="btn btn-danger me-2"><i class="bi bi-dash-circle me-1"></i>Registrar nuevo gasto</a>
      <a href="<?php echo BASE_URL ?>meta/registrar" class="btn btn-primary"><i class="bi bi-bullseye me-1"></i>Registrar nueva meta</a>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-12 text-center">
      <div class="card mx-auto shadow" style="max-width:400px;">
        <div class="card-body">
          <h5 class="card-title mb-2">Saldo disponible:</h5>
          <p class="display-6 fw-bold mb-0">S/. <?= number_format($this->saldo, 2) ?></p>
        </div>
      </div>
      <?php if ($this->saldo < 0): ?>
        <div class="alert alert-danger mt-3 fw-bold">¡Atención! Tienes más gastos que ingresos. Tu saldo es negativo.</div>
      <?php endif; ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-center">
      <h2>Bienvenido, <?= htmlspecialchars($this->nombre) ?> a Gestion de Gastos</h2>
      <p>Gestiona tus finanzas personales de manera sencilla.</p>
      <a href="<?php echo BASE_URL ?>login/logout" class="btn btn-danger mt-3">Cerrar sesión</a>
    </div>
  </div>

  <!-- Gráficos de Ingresos y Gastos -->
 <div class="row mt-5 mb-4">
 <div class="col-12">
  <div class="card shadow rounded-4">
    
      <div class="card-body pt-3">
          <div class="row">
            <?php include_once __DIR__ . '/components/ingresos_chart.php'; ?>
            <?php include_once __DIR__ . '/components/gastos_chart.php'; ?>
        </div>
      </div>
    </div>
  </div>
  </div>

<!-- Resumen de Ingresos y Gastos Recientes -->
  <?php include_once __DIR__ . '/components/resumen_movimientos.php'; ?>

  <!-- Balance Mensual -->
  <?php include_once __DIR__ . '/components/balance_mensual.php'; ?>

  <!-- Resumen de Metas -->
  <?php include_once __DIR__ . '/components/resumen_metas.php'; ?>
</div>

