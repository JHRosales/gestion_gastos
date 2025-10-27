<div class="container mt-5">
  <!-- Selector de Cuenta -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-4 mb-2 mb-md-0">
              <label class="fw-bold"><i class="bi bi-wallet2 me-2"></i>Cuenta seleccionada:</label>
            </div>
            <div class="col-md-8">
              <select class="form-select form-select-lg" id="selector-cuenta" onchange="cambiarCuenta()">
                <option value="0" <?= !isset($_SESSION['cuenta_seleccionada']) || $_SESSION['cuenta_seleccionada'] == 0 ? 'selected' : '' ?>>
                  Todas las cuentas
                </option>
                <?php if (is_array($this->cuentas) && count($this->cuentas) > 0): ?>
                  <?php foreach ($this->cuentas as $cuenta): ?>
                    <option value="<?= $cuenta['id'] ?>" <?= isset($_SESSION['cuenta_seleccionada']) && $_SESSION['cuenta_seleccionada'] == $cuenta['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cuenta['nombre']) ?> (<?= $cuenta['moneda'] === 'USD' ? 'USD' : 'PEN' ?>)
                    </option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <!-- No hay cuentas disponibles, mostrar opción de crear -->
                <?php endif; ?>
              </select>
              <?php if (!is_array($this->cuentas) || count($this->cuentas) == 0): ?>
                <small class="text-muted d-block mt-2">
                  <i class="bi bi-info-circle"></i> 
                  <a href="<?php echo BASE_URL ?>cuenta/registrar">Crea una cuenta para empezar</a>
                </small>
              <?php endif; ?>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-12 text-end">
              <a href="<?php echo BASE_URL ?>cuenta/index" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-gear me-1"></i>Gestionar cuentas
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Botones de Acceso Rápido -->
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

<script>
$(document).ready(function() {
  // Función para cambiar de cuenta con jQuery según convenciones
  window.cambiarCuenta = function() {
    const cuenta_id = $('#selector-cuenta').val();
    
    // Mostrar loading con jQuery
    const $loadingAlert = $('<div>')
      .addClass('alert alert-info alert-dismissible fade show')
      .html(`
        <div class="d-flex align-items-center">
          <div class="spinner-border spinner-border-sm me-2" role="status"></div>
          <span>Cambiando cuenta...</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `);
    
    $('.container').first().prepend($loadingAlert);
    
    // Hacer petición AJAX con jQuery según convenciones
    $.ajax({
      url: '<?php echo BASE_URL ?>dashboard/cambiarCuenta',
      method: 'GET',
      data: { cuenta_id: cuenta_id },
      dataType: 'json',
      success: function(data) {
        $loadingAlert.remove();
        
        if (data.success) {
          // Recargar la página para actualizar todos los datos
          window.location.reload();
        } else {
          alert('Error al cambiar cuenta: ' + data.message);
          // Revertir selección con jQuery
          $('#selector-cuenta').val('<?php echo isset($_SESSION["cuenta_seleccionada"]) ? $_SESSION["cuenta_seleccionada"] : 0 ?>');
        }
      },
      error: function(xhr, status, error) {
        $loadingAlert.remove();
        console.error('Error:', error);
        alert('Error al cambiar cuenta');
        // Revertir selección en caso de error con jQuery
        $('#selector-cuenta').val('<?php echo isset($_SESSION["cuenta_seleccionada"]) ? $_SESSION["cuenta_seleccionada"] : 0 ?>');
      }
    });
  };
});
</script>

