<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h2 class="mb-4 text-center">Iniciar Sesión</h2>

      <?php if ($this->error != ''): ?>
        <div class="alert alert-danger"><?= $this->error; ?></div>
      <?php endif; ?>
      <form method="post" action="<?= BASE_URL ?>login" autocomplete="off">
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
      </form>
      <div class="mt-3 text-center">
        <a href="<?= BASE_URL ?>login/register">¿No tienes cuenta? Regístrate</a>
      </div>
    </div>
  </div>
</div>

