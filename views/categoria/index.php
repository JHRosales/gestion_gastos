<?php
$tipoSeleccionado = isset($_GET['tipo']) ? $_GET['tipo'] : 'gasto';
$iconosAgrupados = require __DIR__ . '/iconos.php';
$colores = require __DIR__ . '/colores.php';
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <div class="btn-group" role="group" aria-label="Tipo">
            <a href="<?= BASE_URL ?>categoria?tipo=ingreso" id="btnIngresoListado" 
               class="btn btn-outline-primary<?= isset($_GET['tipo']) && $_GET['tipo']==='ingreso'?' active':'' ?>">
              Ingreso
            </a>
            <a href="<?= BASE_URL ?>categoria?tipo=gasto" id="btnGastoListado" 
               class="btn btn-outline-danger<?= (!isset($_GET['tipo']) || $_GET['tipo']==='gasto')?' active':'' ?>">
              Gasto
            </a>
          </div>
        </div>

        <button class="btn btn-primary" id="btnNuevaCategoria">
          <i class="bi bi-plus-circle"></i> Nueva Categoría
        </button>
      </div>

      <div class="card p-4 mb-4">
        <h5 class="mb-4">Categorías Registradas</h5>
        <div id="listadoCategorias">
          <!-- Render dinámico vía JS -->
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal Bootstrap para crear/editar categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalTitulo" class="modal-title">Crear Categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCategoria" autocomplete="off">
          <div class="mb-3">
            <label class="form-label">Tipo</label><br>
            <div class="btn-group mb-2" role="group" aria-label="Tipo">
              <button type="button" id="btnIngreso" class="btn btn-outline-primary">Ingreso</button>
              <button type="button" id="btnGasto" class="btn btn-outline-danger">Gasto</button>
            </div>
            <input type="hidden" id="tipo" name="tipo" value="gasto">
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Selecciona un Ícono</label><br>
            <?php foreach ($this->iconosAgrupados as $grupo => $iconos): ?>
              <div class="mb-2"><strong><?= $grupo ?></strong></div>
              <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($iconos as $icono => $desc): ?>
                  <button type="button" class="btn btn-light rounded-circle icono-btn" 
                          data-icono="<?= $icono ?>" title="<?= $desc ?>" 
                          style="width:44px; height:44px; font-size:1.3rem;">
                    <i class="bi <?= $icono ?>"></i>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
            <input type="hidden" id="icono" name="icono" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Selecciona un Color</label><br>
            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
              <?php foreach ($this->colores as $hex => $nombre): ?>
                <div class="rounded-circle color-paleta" 
                     data-color="<?= $hex ?>" title="<?= $nombre ?>" 
                     style="background-color: <?= $hex ?>; width:36px; height:36px; cursor:pointer; border:2px solid #fff;"></div>
              <?php endforeach; ?>
              <input type="color" id="colorPicker" style="border:none; width:40px; height:40px; cursor:pointer;">
            </div>
            <input type="hidden" id="color" name="color" required>
          </div>
          <input type="hidden" id="idCategoria" name="idCategoria" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formCategoria" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>


<script>
// Definir la URL base para las peticiones AJAX
const BASE_URL = '<?= BASE_URL ?>';
</script>
<script src="<?= BASE_URL ?>public/assets/js/categorias.js"></script>
