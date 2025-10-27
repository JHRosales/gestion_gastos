<?php if (count($this->gastosPorCategoria) == 0): ?>
        <div class="alert alert-warning">No hay datos disponibles para mostrar el gráfico de gastos.</div>
<?php 
        return;
        endif; ?>
<div class="col-md-6">
    <h5>Gráfico de Gastos</h5>
    <canvas id="gastosChart" height="200"></canvas>
</div>

<script>
$(document).ready(function() {
    const gastosData = {
        labels: <?= json_encode(array_keys($this->gastosPorCategoria)) ?>,
        datasets: [{
            label: 'Gastos',
            data: <?= json_encode(array_values($this->gastosPorCategoria)) ?>,
            backgroundColor: 'rgba(220, 53, 69, 0.5)',
            borderColor: 'rgba(220, 53, 69, 1)',
            borderWidth: 1
        }]
    };

    const chartConfig = {
        type: 'bar',
        data: gastosData,
        options: {
            responsive: true,
            plugins: { 
                legend: { display: false }
            }
        }
    };

    const ctxGastos = $('#gastosChart')[0].getContext('2d');
    new Chart(ctxGastos, chartConfig);
});
</script>
