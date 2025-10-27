<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h4><?= $this->esEdicion ? 'Editar Meta de Gastos' : 'Registrar Meta de Gastos' ?></h4>
        </div>
        <div class="card-body">
          <form method="post" action="<?= BASE_URL ?>meta/<?= $this->esEdicion ? 'editar/' . $this->id : 'registrar' ?>">
            <div class="mb-3">
              <label for="nombre_meta" class="form-label">Nombre de la meta</label>
              <input type="text" class="form-control" id="nombre_meta" name="nombre_meta" required
                     value="<?= $this->esEdicion ? htmlspecialchars($this->meta['nombre']) : '' ?>">
            </div>
            <?php
              $inputName = 'categoria';
              $dropdownId = 'dropdownCategoriaMeta';
              $label = 'Categoría de gasto asociada';
              $selectedCategoria = $this->esEdicion ? $this->meta['categoria'] : '';
              include __DIR__ . '/../categoria/dropdown_categoria.php';
            ?>
            <div class="mb-3">
              <label for="monto_objetivo" class="form-label">Monto objetivo</label>
              <input type="number" step="0.01" min="0" class="form-control" id="monto_objetivo" name="monto_objetivo" required
                     value="<?= $this->esEdicion ? htmlspecialchars($this->meta['monto_objetivo']) : '' ?>">
            </div>
            <div class="mb-3">
              <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
              <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required
                     value="<?= $this->esEdicion ? htmlspecialchars($this->meta['fecha_inicio']) : '' ?>">
            </div>
            <div class="mb-3">
              <label for="fecha_fin" class="form-label">Fecha de fin</label>
              <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required
                     value="<?= $this->esEdicion ? htmlspecialchars($this->meta['fecha_fin']) : '' ?>">
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">
                <?= $this->esEdicion ? 'Guardar Cambios' : 'Registrar Meta' ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div> 