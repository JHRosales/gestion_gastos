<?php
/**
 * Dropdown reutilizable para seleccionar cuenta
 * 
 * Variables esperadas:
 * @var string $inputName - Nombre del input hidden
 * @var string $dropdownId - ID único para el dropdown
 * @var string $label - Etiqueta del campo
 * @var string $selectedCuentaId - ID de la cuenta seleccionada
 * @var bool $showLabel - Mostrar o no la etiqueta (default: true)
 */

// Variables con valores por defecto
$label = isset($label) ? $label : 'Cuenta';
$showLabel = isset($showLabel) ? $showLabel : true;
$dropdownId = isset($dropdownId) ? $dropdownId : 'dropdown_cuenta_' . uniqid();

// Obtener cuentas disponibles con validación según convenciones
$cuentas = (is_array($this->cuentas) && count($this->cuentas) > 0) ? $this->cuentas : [];

// Buscar cuenta seleccionada
$selectedCuenta = null;
if (isset($selectedCuentaId) && $selectedCuentaId !== '' && $selectedCuentaId !== null) {
    foreach ($cuentas as $cuenta) {
        if (isset($cuenta['id']) && $cuenta['id'] == $selectedCuentaId) {
            $selectedCuenta = $cuenta;
            break;
        }
    }
}

// Si no hay cuenta seleccionada, usar la cuenta de la sesión
if (!$selectedCuenta && isset($_SESSION['cuenta_seleccionada']) && $_SESSION['cuenta_seleccionada'] > 0) {
    $selectedCuentaId = $_SESSION['cuenta_seleccionada'];
    foreach ($cuentas as $cuenta) {
        if (isset($cuenta['id']) && $cuenta['id'] == $selectedCuentaId) {
            $selectedCuenta = $cuenta;
            break;
        }
    }
}
?>

<div class="mb-3">
  <?php if ($showLabel): ?>
    <label for="<?= $dropdownId ?>_select" class="form-label"><?= htmlspecialchars($label) ?></label>
  <?php endif; ?>
  
  <select class="form-select" id="<?= $dropdownId ?>_select" name="<?= $inputName ?>">
    <option value="">Seleccione una cuenta</option>
    <?php if (is_array($cuentas) && count($cuentas) > 0): ?>
      <?php foreach ($cuentas as $cuenta): ?>
        <?php if (isset($cuenta['id']) && isset($cuenta['nombre']) && isset($cuenta['moneda'])): ?>
          <option value="<?= $cuenta['id'] ?>" <?= ($selectedCuenta && isset($selectedCuenta['id']) && $cuenta['id'] == $selectedCuenta['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cuenta['nombre']) ?> (<?= $cuenta['moneda'] === 'USD' ? 'USD' : 'PEN' ?>)
          </option>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
  
  <?php if (!is_array($cuentas) || count($cuentas) === 0): ?>
    <small class="text-muted">
      <i class="bi bi-info-circle"></i> 
      <a href="<?= BASE_URL ?>cuenta/registrar">Crea una cuenta primero</a>
    </small>
  <?php endif; ?>
</div>

