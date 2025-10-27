<?php
require_once 'views/partials/default/header.php';

$esEdicion = isset($this->esEdicion) && $this->esEdicion;
$cuenta = isset($this->cuenta) ? $this->cuenta : [];
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <div class="card-header bg-primary text-white text-center">
          <h4><?= $esEdicion ? 'Editar Cuenta' : 'Nueva Cuenta' ?></h4>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= BASE_URL ?>cuenta/<?= $esEdicion ? 'editar/' . $cuenta['id'] : 'registrar' ?>">
            
            <!-- Nombre -->
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre de la Cuenta *</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required 
                     placeholder="Ej: Caja de Ahorros, Tarjeta Principal"
                     value="<?= $esEdicion ? htmlspecialchars($cuenta['nombre']) : '' ?>">
            </div>

            <!-- Tipo -->
            <div class="mb-3">
              <label for="tipo" class="form-label">Tipo de Cuenta *</label>
              <select class="form-select" id="tipo" name="tipo" required>
                <option value="">Seleccione un tipo</option>
                <option value="Efectivo" <?= isset($cuenta['tipo']) && $cuenta['tipo'] === 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
                <option value="Ahorros" <?= isset($cuenta['tipo']) && $cuenta['tipo'] === 'Ahorros' ? 'selected' : '' ?>>Ahorros</option>
                <option value="Tarjeta de Crédito" <?= isset($cuenta['tipo']) && $cuenta['tipo'] === 'Tarjeta de Crédito' ? 'selected' : '' ?>>Tarjeta de Crédito</option>
                <option value="Tarjeta de Débito" <?= isset($cuenta['tipo']) && $cuenta['tipo'] === 'Tarjeta de Débito' ? 'selected' : '' ?>>Tarjeta de Débito</option>
                <option value="Cuenta Bancaria" <?= isset($cuenta['tipo']) && $cuenta['tipo'] === 'Cuenta Bancaria' ? 'selected' : '' ?>>Cuenta Bancaria</option>
                <option value="Otro" <?= isset($cuenta['tipo']) && $cuenta['tipo'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
              </select>
            </div>

            <!-- Saldo Inicial -->
            <div class="mb-3">
              <label for="saldo_inicial" class="form-label">Saldo Inicial</label>
              <input type="number" step="0.01" class="form-control" id="saldo_inicial" name="saldo_inicial" 
                     placeholder="0.00"
                     value="<?= $esEdicion ? htmlspecialchars($cuenta['saldo_inicial']) : '0' ?>">
              <small class="text-muted">El saldo con el que inicia esta cuenta</small>
            </div>

            <!-- Moneda -->
            <div class="mb-3">
              <label for="moneda" class="form-label">Moneda *</label>
              <select class="form-select" id="moneda" name="moneda" required>
                <option value="PEN" <?= isset($cuenta['moneda']) && $cuenta['moneda'] === 'PEN' ? 'selected' : '' ?>>Soles (PEN)</option>
                <option value="USD" <?= isset($cuenta['moneda']) && $cuenta['moneda'] === 'USD' ? 'selected' : '' ?>>Dólares (USD)</option>
              </select>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">
                <?= $esEdicion ? 'Guardar Cambios' : 'Crear Cuenta' ?>
              </button>
              <a href="<?= BASE_URL ?>cuenta/index" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'views/partials/default/footer.php'; ?>

