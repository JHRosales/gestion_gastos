<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <div class="card-header bg-success text-white text-center">
          <h4><?= $this->esEdicion ? 'Editar Ingreso' : 'Registrar Ingreso' ?></h4>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= BASE_URL ?>ingreso/<?= $this->esEdicion ? 'editar/' . $this->ingreso['id'] : 'registrar' ?>">
            <!-- Monto -->
            <div class="mb-3">
              <label for="monto" class="form-label">Monto</label>
              <input type="number" step="0.01" inputmode="decimal" class="form-control" id="monto" name="monto" required 
                     value="<?= $this->esEdicion ? htmlspecialchars($this->ingreso['monto']) : '' ?>">
            </div>
            
            <!-- Categoría (Dropdown reutilizable) -->
            <?php
              $inputName = 'categoria';
              $dropdownId = 'dropdownCategoriaIngreso';
              $label = 'Categorías';
              $selectedCategoria = $this->esEdicion ? $this->ingreso['categoria'] : '';
              include __DIR__ . '/../categoria/dropdown_categoria.php';
            ?>

            <!-- Cuenta -->
            <?php
              $inputName = 'cuenta_id';
              $dropdownId = 'dropdownCuentaIngreso';
              $label = 'Cuenta';
              $selectedCuentaId = ($this->esEdicion && isset($this->ingreso['cuenta_id']) && $this->ingreso['cuenta_id'] !== '') ? $this->ingreso['cuenta_id'] : '';
              include __DIR__ . '/../cuenta/dropdown_cuenta.php';
            ?>

            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <input type="text" class="form-control" id="descripcion" name="descripcion" required 
                     value="<?= $this->esEdicion ? htmlspecialchars($this->ingreso['descripcion']) : '' ?>">
            </div>

            <div class="mb-3">
              <label for="fecha" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="fecha" name="fecha" required 
                     value="<?= $this->esEdicion ? htmlspecialchars($this->ingreso['fecha']) : date('Y-m-d') ?>">
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-success btn-lg py-4 py-md-2">
                <?= $this->esEdicion ? 'Guardar Cambios' : 'Registrar Ingreso' ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>