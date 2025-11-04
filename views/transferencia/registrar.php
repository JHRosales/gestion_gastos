<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <div class="card-header bg-info text-white text-center">
          <h4><i class="bi bi-arrow-left-right me-2"></i>Registrar Transferencia</h4>
        </div>
        <div class="card-body">
          <?php if (isset($_SESSION['error_transferencia'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              <?= $_SESSION['error_transferencia'] ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_transferencia']); ?>
          <?php endif; ?>

          <form method="POST" action="<?= BASE_URL ?>transferencia/registrar" id="formTransferencia">
            <!-- Cuenta Origen -->
            <div class="mb-3">
              <label for="cuenta_origen_id" class="form-label">Cuenta Origen *</label>
              <select class="form-select" id="cuenta_origen_id" name="cuenta_origen_id" required>
                <option value="">Seleccione una cuenta</option>
                <?php if (is_array($this->cuentas) && count($this->cuentas) > 0): ?>
                  <?php foreach ($this->cuentas as $cuenta): ?>
                    <option value="<?= $cuenta['id'] ?>">
                      <?= htmlspecialchars($cuenta['nombre']) ?> 
                      (<?= $cuenta['moneda'] === 'USD' ? 'USD' : 'PEN' ?>)
                    </option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <option disabled>No hay cuentas disponibles</option>
                <?php endif; ?>
              </select>
            </div>

            <!-- Cuenta Destino -->
            <div class="mb-3">
              <label for="cuenta_destino_id" class="form-label">Cuenta Destino *</label>
              <select class="form-select" id="cuenta_destino_id" name="cuenta_destino_id" required>
                <option value="">Seleccione una cuenta</option>
                <?php if (is_array($this->cuentas) && count($this->cuentas) > 0): ?>
                  <?php foreach ($this->cuentas as $cuenta): ?>
                    <option value="<?= $cuenta['id'] ?>">
                      <?= htmlspecialchars($cuenta['nombre']) ?> 
                      (<?= $cuenta['moneda'] === 'USD' ? 'USD' : 'PEN' ?>)
                    </option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <option disabled>No hay cuentas disponibles</option>
                <?php endif; ?>
              </select>
              <small class="text-danger d-none" id="errorMismaCuenta">La cuenta destino debe ser diferente a la cuenta origen.</small>
            </div>

            <!-- Monto -->
            <div class="mb-3">
              <label for="monto" class="form-label">Monto *</label>
              <input type="number" step="0.01" min="0.01" inputmode="decimal" 
                     class="form-control" id="monto" name="monto" required 
                     placeholder="0.00">
              <small class="text-danger d-none" id="errorMonto">El monto debe ser mayor a 0.</small>
            </div>

            <!-- Descripción -->
            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea class="form-control" id="descripcion" name="descripcion" 
                        rows="3" placeholder="Descripción opcional de la transferencia"></textarea>
            </div>

            <!-- Fecha -->
            <div class="mb-3">
              <label for="fecha" class="form-label">Fecha *</label>
              <input type="date" class="form-control" id="fecha" name="fecha" required 
                     value="<?= date('Y-m-d') ?>">
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-info btn-lg py-4 py-md-2">
                <i class="bi bi-arrow-left-right me-2"></i>Registrar Transferencia
              </button>
              <a href="<?= BASE_URL ?>dashboard" class="btn btn-secondary mt-2">
                Cancelar
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  const formTransferencia = $('#formTransferencia');
  const cuentaOrigen = $('#cuenta_origen_id');
  const cuentaDestino = $('#cuenta_destino_id');
  const monto = $('#monto');
  const errorMismaCuenta = $('#errorMismaCuenta');
  const errorMonto = $('#errorMonto');

  // Validar que las cuentas sean diferentes
  function validarCuentas() {
    const origenVal = cuentaOrigen.val();
    const destinoVal = cuentaDestino.val();
    
    if (origenVal && destinoVal && origenVal === destinoVal) {
      errorMismaCuenta.removeClass('d-none');
      cuentaDestino.addClass('is-invalid');
      return false;
    } else {
      errorMismaCuenta.addClass('d-none');
      cuentaDestino.removeClass('is-invalid');
      return true;
    }
  }

  // Validar monto
  function validarMonto() {
    const montoVal = parseFloat(monto.val());
    
    if (monto.val() && (isNaN(montoVal) || montoVal <= 0)) {
      errorMonto.removeClass('d-none');
      monto.addClass('is-invalid');
      return false;
    } else {
      errorMonto.addClass('d-none');
      monto.removeClass('is-invalid');
      return true;
    }
  }

  // Event listeners
  cuentaOrigen.on('change', validarCuentas);
  cuentaDestino.on('change', validarCuentas);
  monto.on('input', validarMonto);

  // Validar antes de enviar
  formTransferencia.on('submit', function(e) {
    let esValido = true;

    if (!validarCuentas()) {
      esValido = false;
    }

    if (!validarMonto()) {
      esValido = false;
    }

    if (!esValido) {
      e.preventDefault();
      return false;
    }
  });

  // Filtrar cuenta destino cuando se selecciona origen
  cuentaOrigen.on('change', function() {
    const origenId = $(this).val();
    if (origenId) {
      cuentaDestino.find('option').each(function() {
        if ($(this).val() === origenId) {
          $(this).prop('disabled', true);
        } else {
          $(this).prop('disabled', false);
        }
      });
      // Si la cuenta destino seleccionada es la misma, resetear
      if (cuentaDestino.val() === origenId) {
        cuentaDestino.val('');
        validarCuentas();
      }
    } else {
      cuentaDestino.find('option').prop('disabled', false);
    }
  });

  // Filtrar cuenta origen cuando se selecciona destino
  cuentaDestino.on('change', function() {
    const destinoId = $(this).val();
    if (destinoId) {
      cuentaOrigen.find('option').each(function() {
        if ($(this).val() === destinoId) {
          $(this).prop('disabled', true);
        } else {
          $(this).prop('disabled', false);
        }
      });
      // Si la cuenta origen seleccionada es la misma, resetear
      if (cuentaOrigen.val() === destinoId) {
        cuentaOrigen.val('');
        validarCuentas();
      }
    } else {
      cuentaOrigen.find('option').prop('disabled', false);
    }
  });
});
</script>
