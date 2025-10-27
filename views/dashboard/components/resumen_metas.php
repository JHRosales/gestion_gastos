<?php if ($this->resumenMetas == 0): ?>
        <div class="alert alert-warning">No hay datos disponibles para mostrar el resumen de metas.</div>
<?php 
        return;
        endif; ?>



        


<div class="row mb-5">
    <div class="col-12">
    <div class="card shadow rounded-4">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-columns me-2"></i>Resumen de Metas de Gastos</h5>
            </div>
            <div class="card-body pt-3">
     
        <?php if ($this->resumenMetas == 0): ?>
            <div class="alert alert-info">No tienes metas registradas.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Monto objetivo</th>
                            <th>Avance</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->resumenMetas as $meta): ?>
                            <tr>
                                <td><?php echo isset($meta['nombre_meta']) ? htmlspecialchars($meta['nombre_meta']) : ''; ?></td>
                                <td><?php echo isset($meta['categoria']) ? htmlspecialchars($meta['categoria']) : ''; ?></td>
                                <td>$<?php echo number_format($meta['monto_objetivo'], 2); ?></td>
                                <td style="min-width:120px;">
                                    <div class="progress" style="height: 22px;">
                                        <div class="progress-bar<?php echo ($meta['porcentaje'] >= 100) ? ' bg-success' : ' bg-info'; ?>" 
                                             role="progressbar" 
                                             style="width: <?php echo $meta['porcentaje']; ?>%;" 
                                             aria-valuenow="<?php echo $meta['porcentaje']; ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?php echo $meta['porcentaje']; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($meta['estado'] === 'cumplida'): ?>
                                        <span class="badge bg-success">Cumplida</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    </div>
    </div>
</div>
