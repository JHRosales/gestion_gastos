<?php if ($this->saldo == 0): ?>
        <div class="alert alert-warning">No hay datos disponibles para mostrar el balance mensual.</div>
<?php 
        return;
    endif; 
?>

<div class="card p-4 shadow rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pb-0">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-columns me-2"></i>Balance mensual</h5>
    </div>
    <div id="graficoContainer" class="d-flex justify-content-center position-relative">
        <canvas id="balanceMensualChart" width="100" height="100" style="max-width:100px; max-height:100px; width:100px !important; height:100px !important;"></canvas>
        <div class="position-absolute top-50 start-50 translate-middle text-center" style="width: 120px;">
            <select id="mesSelector" class="form-select form-select-sm mb-2"></select>
            <div>
                <small class="text-muted d-block">Gastos</small>
                <h5 id="totalGastosMes" class="text-primary fw-bold mb-0">S/ 0.00</h5>
            </div>
        </div>
    </div>
    <div id="resumenCategorias" class="mt-3"></div>
</div>


<script>
$(document).ready(function() {
    // Configuración inicial del gráfico
    const chartConfig = {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', 
                    '#B2FF66', '#FF66B2', '#66FFB2', '#B266FF', '#FFB266', '#66B2FF'
                ],
                borderWidth: 2
            }]
        },
        options: {
            cutout: '65%',
            plugins: {
                legend: { display: false }
            },
            responsive: true
        }
    };

    const ctxBalance = $('#balanceMensualChart')[0].getContext('2d');
    let balanceMensualChart = new Chart(ctxBalance, chartConfig);

    // Funciones auxiliares
    function mesNombre(mes) {
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        if (!mes.includes('-')) return mes;
        const [anio, mesNum] = mes.split('-');
        return meses[parseInt(mesNum, 10) - 1] + ' ' + anio;
    }

    function cargarMesesDisponibles() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>balanceMensual/obtenerMeses',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const $mesSelector = $('#mesSelector');
                $mesSelector.empty();
                
                if (data.success && data.meses && data.meses.length > 0) {
                    data.meses.forEach(mes => {
                        $mesSelector.append(`<option value="${mes}">${mesNombre(mes)}</option>`);
                    });
                    cargarDatosMes($mesSelector.val());
                } else {
                    $('#graficoContainer').html('<div class="text-center text-muted p-4">No hay registros disponibles.</div>');
                    $('#totalGastosMes').text('S/ 0.00');
                }
            },
            error: function(error) {
                console.error('Error:', error);
                $('#graficoContainer').html('<div class="text-center text-danger p-4">Error al cargar los datos.</div>');
            }
        });
    }

    function cargarDatosMes(mes) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>balanceMensual/obtenerDatosMes/' + mes,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const $canvas = $('#balanceMensualChart');
                const $contenedorResumen = $('#resumenCategorias');
                const $totalGastosMes = $('#totalGastosMes');
                
                if (data.labels && data.labels.length > 0) {
                    $canvas.show();
                    
                    // Actualizar el gráfico
                    balanceMensualChart.data.labels = data.labels;
                    balanceMensualChart.data.datasets[0].data = data.data;
                    balanceMensualChart.data.datasets[0].backgroundColor = data.colors;
                    balanceMensualChart.update();
                    
                    // Actualizar el total de gastos
                    $totalGastosMes.text('S/ ' + parseFloat(data.total).toFixed(2));
                    
                    // Mostrar resumen de categorías
                    $contenedorResumen.empty();
                    data.resumen.forEach(item => {
                        $contenedorResumen.append(`
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle me-2 d-flex justify-content-center align-items-center" 
                                     style="width: 36px; height: 36px; background-color: ${item.color};">
                                    <i class="bi ${item.icono} text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>${item.nombre}</strong>
                                </div>
                                <div class="text-end text-primary fw-bold">S/ ${item.total.toFixed(2)}</div>
                            </div>
                        `);
                    });
                } else {
                    $canvas.hide();
                    $contenedorResumen.empty();
                    $totalGastosMes.text('S/ 0.00');
                }
            },
            error: function(error) {
                console.error('Error:', error);
                $('#graficoContainer').html('<div class="text-center text-danger p-4">Error al cargar los datos del mes.</div>');
            }
        });
    }

    // Event Listeners
    $('#mesSelector').on('change', function() {
        cargarDatosMes($(this).val());
    });

    // Inicialización
    cargarMesesDisponibles();
});
</script>
