<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <h2 class="mb-4 text-center">Registro de Usuario</h2>

      <?php if ($this->error !== ''): ?>
        <div class="alert alert-danger"><?= $this->error; ?></div>
      <?php endif; ?>
      <form method="post" action="<?= BASE_URL ?>login/register" autocomplete="off">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirmar contraseña</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Registrar cuenta</button>
      </form>
      <div class="mt-3 text-center">
        <a href="<?= BASE_URL ?>login">¿Ya tienes cuenta? Inicia sesión</a>
      </div>
    </div>
  </div>
</div>

