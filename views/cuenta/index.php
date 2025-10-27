<div class="container py-5">
  <div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h2 class="mb-0"><i class="bi bi-bank me-2"></i>Mis Cuentas</h2>
      <a href="<?= BASE_URL ?>cuenta/registrar" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Nueva Cuenta
      </a>
    </div>
  </div>

  <?php if (isset($_SESSION['success_cuenta'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <?= $_SESSION['success_cuenta'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_cuenta']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_cuenta'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <?= $_SESSION['error_cuenta'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_cuenta']); ?>
  <?php endif; ?>

  <div class="row">
    <?php if (count($this->cuentas) == 0): ?>
      <div class="col-12">
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>No tienes cuentas registradas. 
          <a href="<?= BASE_URL ?>cuenta/registrar">Crea tu primera cuenta</a>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($this->cuentas as $cuenta): 
        $saldo = isset($cuenta['saldo_calculado']) ? $cuenta['saldo_calculado'] : 0;
        $simboloMoneda = $cuenta['moneda'] === 'USD' ? '$' : 'S/';
        $claseSaldo = $saldo >= 0 ? 'text-success' : 'text-danger';
      ?>
        <div class="col-md-6 col-lg-4 mb-3">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="card-title mb-0"><?= htmlspecialchars($cuenta['nombre']) ?></h5>
                <span class="badge bg-secondary"><?= htmlspecialchars($cuenta['tipo']) ?></span>
              </div>
              <p class="card-text text-muted mb-2">
                <i class="bi bi-currency-<?= strtolower($cuenta['moneda']) ?>"></i>
                <strong><?= $cuenta['moneda'] === 'USD' ? 'Dólares' : 'Soles' ?></strong>
              </p>
              <div class="mb-3">
                <p class="mb-1"><small class="text-muted">Saldo Inicial:</small></p>
                <p class="mb-0"><?= $simboloMoneda ?> <?= number_format($cuenta['saldo_inicial'], 2) ?></p>
              </div>
              <div class="mb-3">
                <p class="mb-1"><small class="text-muted">Saldo Actual:</small></p>
                <h4 class="<?= $claseSaldo ?> mb-0">
                  <?= $simboloMoneda ?> <?= number_format($saldo, 2) ?>
                </h4>
              </div>
            </div>
            <div class="card-footer bg-white border-top">
              <div class="btn-group w-100" role="group">
                <a href="<?= BASE_URL ?>cuenta/editar/<?= $cuenta['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCuenta(<?= $cuenta['id'] ?>)">
                  <i class="bi bi-trash me-1"></i>Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
function eliminarCuenta(id) {
  if (!confirm('¿Está seguro de que desea eliminar esta cuenta?')) {
    return;
  }

  $.ajax({
    url: '<?= BASE_URL ?>cuenta/eliminar/' + id,
    method: 'POST',
    dataType: 'json',
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    success: function(data) {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert(data.message);
      }
    },
    error: function(xhr, status, error) {
      alert('Error al eliminar la cuenta: ' + error);
    }
  });
}
</script>

<?php require_once 'views/partials/default/footer.php'; ?>

