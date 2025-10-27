<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../models/Categoria.php';
$categoriaModel = new Categoria();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$categoria = $categoriaModel->obtenerPorId($id);
if (!$categoria) {
    echo '<div class="alert alert-danger">Categoría no encontrada.</div>';
    exit();
}
$iconosAgrupados = require __DIR__ . '/iconos.php';
$colores = require __DIR__ . '/colores.php';
?>
<?php include_once __DIR__ . '/../partials/header.php'; ?>
<div class="container py-5">
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card p-4 mb-4">
        <h5>Editar Categoría</h5>
        <form method="POST" action="<?= BASE_URL ?>categoria/editar/<?= $categoria['id'] ?>">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
          </div>
          <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-select" id="tipo" name="tipo" required>
              <option value="">Seleccione un tipo</option>
              <option value="ingreso" <?= $categoria['tipo'] === 'ingreso' ? 'selected' : '' ?>>
                Ingreso
              </option>
              <option value="gasto" <?= $categoria['tipo'] === 'gasto' ? 'selected' : '' ?>>
                Gasto
              </option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Selecciona un Ícono</label><br>
            <?php foreach ($iconosAgrupados as $grupo => $iconos): ?>
              <div class="mb-2"><strong><?= $grupo ?></strong></div>
              <div class="d-flex flex-wrap gap-2 mb-3" id="iconosPaleta-<?= preg_replace('/\W/','',$grupo) ?>">
                <?php foreach ($iconos as $icono => $desc): ?>
                  <button type="button" class="btn btn-light rounded-circle icono-btn<?= $categoria['icono']===$icono?' selected':'' ?>" data-icono="<?= $icono ?>" title="<?= $desc ?>" style="width:44px; height:44px; font-size:1.3rem;">
                    <i class="bi <?= $icono ?>"></i>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
            <input type="hidden" id="icono" name="icono" value="<?= htmlspecialchars($categoria['icono']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Selecciona un Color</label><br>
            <div class="d-flex align-items-center flex-wrap gap-2 mb-2" id="paletaColores">
              <?php foreach ($colores as $hex => $nombre): ?>
                <div class="rounded-circle color-paleta<?= $categoria['color']===$hex?' selected':'' ?>" data-color="<?= $hex ?>" title="<?= $nombre ?>" style="background-color: <?= $hex ?>; width: 36px; height: 36px; cursor: pointer; border:2px solid #fff;"></div>
              <?php endforeach; ?>
              <input type="color" id="colorPicker" onchange="seleccionarColor(this.value)" style="border:none; width:40px; height:40px; cursor:pointer;">
            </div>
            <input type="hidden" id="color" name="color" value="<?= htmlspecialchars($categoria['color']) ?>" required>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="<?= BASE_URL ?>categoria" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Selección de iconos
  document.querySelectorAll('.icono-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.icono-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      document.getElementById('icono').value = this.dataset.icono;
    });
  });

  // Selección de colores
  document.querySelectorAll('.color-paleta').forEach(div => {
    div.addEventListener('click', function() {
      document.querySelectorAll('.color-paleta').forEach(d => d.classList.remove('selected'));
      this.classList.add('selected');
      document.getElementById('color').value = this.dataset.color;
    });
  });
});

function seleccionarColor(color) {
  document.getElementById('color').value = color;
  document.querySelectorAll('.color-paleta').forEach(div => {
    div.classList.remove('selected');
  });
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
