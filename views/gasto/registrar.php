<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <div class="card-header bg-danger text-white text-center">
          <h4><?= $this->esEdicion ? 'Editar Gasto' : 'Registrar Gasto' ?></h4>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= BASE_URL ?>gasto/<?= $this->esEdicion ? 'editar/' . $this->gasto['id'] : 'registrar' ?>">
            <!-- Monto -->
            <div class="mb-3">
              <label for="monto" class="form-label">Monto</label>
              <input type="number" step="0.01" class="form-control" id="monto" name="monto" required 
                     value="<?= $this->esEdicion ? htmlspecialchars($this->gasto['monto']) : '' ?>">
            </div>
            
            <!-- Categoría (Dropdown reutilizable) -->
            <?php
              $inputName = 'categoria';
              $dropdownId = 'dropdownCategoriaGasto';
              $label = 'Categorías';
              $selectedCategoria = $this->esEdicion ? $this->gasto['categoria'] : '';
              include __DIR__ . '/../categoria/dropdown_categoria.php';
            ?>

            <!-- Cuenta -->
            <?php
              $inputName = 'cuenta_id';
              $dropdownId = 'dropdownCuentaGasto';
              $label = 'Cuenta';
              $selectedCuentaId = ($this->esEdicion && isset($this->gasto['cuenta_id']) && $this->gasto['cuenta_id'] !== '') ? $this->gasto['cuenta_id'] : '';
              include __DIR__ . '/../cuenta/dropdown_cuenta.php';
            ?>

            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <input type="text" class="form-control" id="descripcion" name="descripcion" required 
                     value="<?= $this->esEdicion ? htmlspecialchars($this->gasto['descripcion']) : '' ?>">
            </div>

            <div class="mb-3">
              <label for="fecha" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="fecha" name="fecha" required 
                     value="<?= $this->esEdicion ? htmlspecialchars($this->gasto['fecha']) : '' ?>">
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-danger">
                <?= $this->esEdicion ? 'Guardar Cambios' : 'Registrar Gasto' ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div> 