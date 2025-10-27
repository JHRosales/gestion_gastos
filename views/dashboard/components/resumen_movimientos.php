<?php
$ingresosPorCategoria = $this->ingresosPorCategoria;
$gastosPorCategoria = $this->gastosPorCategoria;
$categorias = $this->categorias;
$ingresos = $this->ingresos;
$gastos = $this->gastos;

// Verificar si hay datos para mostrar
if (!is_array($ingresosPorCategoria) && !is_array($gastosPorCategoria)): ?>
    <div class="alert alert-warning">No hay movimientos registrados.</div>
<?php 
    return;
endif; 

// Crear mapa de categorías
$mapCategorias = [];
if (is_array($categorias)) {
    foreach ($categorias as $cat) {
        $mapCategorias[$cat['nombre']] = $cat;
    }
}
?>

<!-- <div class="card p-4 shadow-sm mb-4"> -->
<div class="card shadow rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pb-0">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-columns me-2"></i>Resumen de Movimientos</h5>
    </div>
    <div class="card-body pt-3">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-success mb-3">Ingresos por Categoría</h6>
                <?php if (is_array($ingresosPorCategoria) && count($ingresosPorCategoria) > 0): ?>
                    <?php foreach ($ingresosPorCategoria as $categoria => $monto): 
                        $cat = isset($mapCategorias[$categoria]) ? $mapCategorias[$categoria] : null;
                        $icono = $cat ? $cat['icono'] : 'bi-question-circle';
                        $color = $cat ? $cat['color'] : '#adb5bd';
                    ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle me-2" 
                                    style="background:<?php echo htmlspecialchars($color); ?>; width:32px; height:32px;">
                                    <i class="bi <?php echo htmlspecialchars($icono); ?> text-white"></i>
                                </span>
                                <span><?php echo htmlspecialchars($categoria); ?></span>
                            </div>
                            <span class="text-success">S/ <?php echo number_format($monto, 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No hay ingresos registrados</p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h6 class="text-danger mb-3">Gastos por Categoría</h6>
                <?php if (is_array($gastosPorCategoria) && count($gastosPorCategoria) > 0): ?>
                    <?php foreach ($gastosPorCategoria as $categoria => $monto): 
                        $cat = isset($mapCategorias[$categoria]) ? $mapCategorias[$categoria] : null;
                        $icono = $cat ? $cat['icono'] : 'bi-question-circle';
                        $color = $cat ? $cat['color'] : '#adb5bd';
                    ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle me-2" 
                                    style="background:<?php echo htmlspecialchars($color); ?>; width:32px; height:32px;">
                                    <i class="bi <?php echo htmlspecialchars($icono); ?> text-white"></i>
                                </span>
                                <span><?php echo htmlspecialchars($categoria); ?></span>
                            </div>
                            <span class="text-danger">S/ <?php echo number_format($monto, 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No hay gastos registrados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
        <div class="card shadow rounded-4">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-columns me-2"></i>Últimos Movimientos</h5>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:48px;">Icono</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $movimientos = [];
                            
                            // Procesar ingresos si existen
                            if (is_array($ingresos) && count($ingresos) > 0) {
                                $movimientos = array_merge(
                                    $movimientos,
                                    array_map(function($ingreso) {
                                        return [
                                            'fecha' => $ingreso['fecha'],
                                            'tipo' => 'Ingreso',
                                            'categoria' => $ingreso['categoria'] ?: 'Sin categoría',
                                            'monto' => $ingreso['monto'],
                                            'descripcion' => $ingreso['descripcion'],
                                            'id' => $ingreso['id']
                                        ];
                                    }, $ingresos)
                                );
                            }
                            
                            // Procesar gastos si existen
                            if (is_array($gastos) && count($gastos) > 0) {
                                $movimientos = array_merge(
                                    $movimientos,
                                    array_map(function($gasto) {
                                        return [
                                            'fecha' => $gasto['fecha'],
                                            'tipo' => 'Gasto',
                                            'categoria' => $gasto['categoria'] ?: 'Sin categoría',
                                            'monto' => $gasto['monto'],
                                            'descripcion' => $gasto['descripcion'],
                                            'id' => $gasto['id']
                                        ];
                                    }, $gastos)
                                );
                            }

                            if (count($movimientos) > 0) {
                                usort($movimientos, function($a, $b) {
                                    return strtotime($b['fecha']) - strtotime($a['fecha']);
                                });

                                $movimientos = array_slice($movimientos, 0, 10);

                                foreach ($movimientos as $movimiento): 
                                    $cat = isset($mapCategorias[$movimiento['categoria']]) ? $mapCategorias[$movimiento['categoria']] : null;
                                    $icono = $cat ? $cat['icono'] : 'bi-question-circle';
                                    $color = $cat ? $cat['color'] : '#adb5bd';
                                ?>
                                    <tr>
                                        <td>
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                                  style="background:<?php echo htmlspecialchars($color); ?>; width:38px; height:38px;">
                                                <i class="bi <?php echo htmlspecialchars($icono); ?> text-white fs-5"></i>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($movimiento['descripcion'] ?: $movimiento['categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($movimiento['categoria']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($movimiento['fecha'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $movimiento['tipo'] === 'Ingreso' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $movimiento['tipo']; ?>
                                            </span>
                                        </td>                                        
                                        <td class="<?php echo $movimiento['tipo'] === 'Ingreso' ? 'text-success' : 'text-danger'; ?>">
                                            S/ <?php echo number_format($movimiento['monto'], 2); ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link text-dark p-0 fs-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="modificarRegistro('<?php echo $movimiento['tipo']; ?>', <?php echo $movimiento['id']; ?>)">
                                                            <i class="bi bi-pencil-square me-2"></i>Modificar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminarRegistro('<?php echo $movimiento['tipo']; ?>', <?php echo $movimiento['id']; ?>)">
                                                            <i class="bi bi-trash me-2"></i>Eliminar
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; 
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay movimientos recientes</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // Funciones de utilidad
    function mostrarAlerta(mensaje, tipo = 'success') {
        const alertDiv = $('<div>', {
            class: `alert alert-${tipo} alert-dismissible fade show`,
            html: `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `
        });
        $('.card').first().before(alertDiv);
    }

    function mostrarLoading() {
        const loadingAlert = $('<div>', {
            class: 'alert alert-info alert-dismissible fade show',
            html: `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <span>Eliminando registro...</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `
        });
        $('.card').first().before(loadingAlert);
        return loadingAlert;
    }

    // Funciones principales
    window.modificarRegistro = function(tipo, id) {
        const baseUrl = '<?php echo BASE_URL; ?>';
        const url = tipo === 'Ingreso' 
            ? `${baseUrl}ingreso/editar/${id}`
            : `${baseUrl}gasto/editar/${id}`;
        
        window.location.href = url;
    };

    window.eliminarRegistro = function(tipo, id) {
        if (!confirm('¿Está seguro de eliminar este registro?')) {
            return;
        }

        const baseUrl = '<?php echo BASE_URL; ?>';
        const url = tipo === 'Ingreso' 
            ? `${baseUrl}ingreso/eliminar/${id}`
            : `${baseUrl}gasto/eliminar/${id}`;

        const $loadingAlert = mostrarLoading();

        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                $loadingAlert.remove();
                
                if (data.success) {
                    mostrarAlerta(data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarAlerta(data.message || 'Error al eliminar el registro', 'danger');
                }
            },
            error: function(xhr, status, error) {
                $loadingAlert.remove();
                mostrarAlerta('Error al eliminar el registro: ' + error, 'danger');
            }
        });
    };
});
</script>
