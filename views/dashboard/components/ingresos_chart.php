<?php if (count($this->ingresosPorCategoria) == 0): ?>
        <div class="alert alert-warning">No hay datos disponibles para mostrar el gráfico de ingresos.</div>
<?php 
        return;
        endif; ?>



<div class="col-md-6">
    <h5>Gráfico de Ingresos</h5>
    <canvas id="ingresosChart" height="200"></canvas>
</div>

<script>
$(document).ready(function() {
    const ingresosData = {
        labels: <?= json_encode(array_keys($this->ingresosPorCategoria)) ?>,
        datasets: [{
            label: 'Ingresos',
            data: <?= json_encode(array_values($this->ingresosPorCategoria)) ?>,
            backgroundColor: 'rgba(40, 167, 69, 0.5)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1
        }]
    };

    const chartConfig = {
        type: 'bar',
        data: ingresosData,
        options: {
            responsive: true,
            plugins: { 
                legend: { display: false }
            }
        }
    };

    const ctxIngresos = $('#ingresosChart')[0].getContext('2d');
    new Chart(ctxIngresos, chartConfig);
});
</script>
