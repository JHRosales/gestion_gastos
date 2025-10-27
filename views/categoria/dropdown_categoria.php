<?php
// Uso: include 'partials/dropdown_categoria.php';
// Requiere: $categorias (array de categorias), $inputName (string, name del input hidden), $dropdownId (string, id único para el dropdown), $label (string, label opcional)
if (count($this->categorias)==0 || !is_array($this->categorias)) {
  echo '<div class="alert alert-danger">No se pasaron categorías al dropdown.</div>';
  return;
}
if (!isset($inputName)) $inputName = 'categoria';
if (!isset($dropdownId)) $dropdownId = 'dropdownCategoria';
if (!isset($label)) $label = 'Categoría';
// Color por nombre (puedes personalizar)
function categoriaColorGlobal($nombre) {
  $map = array(
    'Alimentación' => 'bg-warning',
    'Transporte' => 'bg-info',
    'Servicios' => 'bg-secondary',
    'Entretenimiento' => 'bg-success',
    'Salud' => 'bg-danger',
    'Sueldo' => 'bg-success',
    'Venta' => 'bg-info',
    'Inversión' => 'bg-primary',
    'Otro' => 'bg-dark',
  );
  return isset($map[$nombre]) ? $map[$nombre] : 'bg-primary';
}
?>
<div class="mb-3">
  <label class="form-label"><?= htmlspecialchars($label) ?></label>
  <div class="dropdown w-100">
    <button class="btn btn-light border w-100 d-flex justify-content-between align-items-center dropdown-toggle" type="button" id="<?= htmlspecialchars($dropdownId) ?>" data-bs-toggle="dropdown" aria-expanded="false">
      <div class="d-flex align-items-center">
        <div id="iconSelected-<?= htmlspecialchars($dropdownId) ?>" class="rounded-circle bg-primary d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
          <i class="bi bi-question text-white"></i>
        </div>
        <span id="nombreCategoriaSeleccionada-<?= htmlspecialchars($dropdownId) ?>" class="ms-2">Selecciona una categoría</span>
      </div>
    </button>
    <ul class="dropdown-menu w-100" aria-labelledby="<?= htmlspecialchars($dropdownId) ?>" id="listaCategorias-<?= htmlspecialchars($dropdownId) ?>">
      <?php foreach ($this->categorias as $cat): ?>
        <li>
          <div class="dropdown-item d-flex align-items-center gap-2 categoria-opcion" style="cursor:pointer;" data-nombre="<?= htmlspecialchars($cat['nombre']) ?>" data-icono="<?= htmlspecialchars($cat['icono']) ?>" data-bg="<?= categoriaColorGlobal($cat['nombre']) ?>">
            <div class="rounded-circle <?= categoriaColorGlobal($cat['nombre']) ?> d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
              <i class="bi <?= htmlspecialchars($cat['icono']) ?> text-white"></i>
            </div>
            <span><?= htmlspecialchars($cat['nombre']) ?></span>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <input type="hidden" id="categoriaSeleccionada-<?= htmlspecialchars($dropdownId) ?>" name="<?= htmlspecialchars($inputName) ?>" required value="<?= isset($selectedCategoria) ? htmlspecialchars($selectedCategoria) : '' ?>">
  </div>
</div>
<script>
$(document).ready(function () {
  const dropdownId = '<?= htmlspecialchars($dropdownId) ?>';
  const $selectedInput = $(`#categoriaSeleccionada-${dropdownId}`);
  const selectedValue = $selectedInput.val()?.trim();

  const actualizarCategoriaSeleccionada = (nombre, icono, bg, id) => {
    $(`#nombreCategoriaSeleccionada-${id}`).text(nombre);
    const $iconDiv = $(`#iconSelected-${id}`);
    $iconDiv
      .attr('class', `rounded-circle ${bg} d-flex justify-content-center align-items-center`)
      .html(`<i class="bi ${icono} text-white"></i>`);
    $(`#categoriaSeleccionada-${id}`).val(nombre);
  };

  // Intentar seleccionar automáticamente la categoría previa
  if (selectedValue) {
    $('.categoria-opcion').each(function () {
      const $opcion = $(this);
      if ($opcion.data('nombre')?.trim() === selectedValue) {
        const nombre = $opcion.data('nombre');
        const icono = $opcion.data('icono');
        const bg = $opcion.data('bg');
        actualizarCategoriaSeleccionada(nombre, icono, bg, dropdownId);
        return false; // salir del each
      }
    });
  }

  // Manejar selección manual
  $('.categoria-opcion').on('click', function () {
    const $item = $(this);
    const nombre = $item.data('nombre');
    const icono = $item.data('icono');
    const bg = $item.data('bg');
    const id = $item.closest('.dropdown').find('button').attr('id');
    actualizarCategoriaSeleccionada(nombre, icono, bg, id);
  });
});

</script>
