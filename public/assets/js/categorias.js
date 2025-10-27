// Espera a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que BASE_URL está definida
    if (typeof BASE_URL === 'undefined') {
        console.error('BASE_URL no está definida. Asegúrate de que está definida antes de cargar este script.');
        return;
    }

    // Inicialización de elementos
    const btnIngresoListado = document.getElementById('btnIngresoListado');
    const btnGastoListado = document.getElementById('btnGastoListado');
    const listadoCategorias = document.getElementById('listadoCategorias');
    const formCategoria = document.getElementById('formCategoria');
    const modalCategoriaEl = document.getElementById('modalCategoria');
    const modalCategoria = new bootstrap.Modal(modalCategoriaEl);
    
    // Estado inicial
    let tipoActual = new URLSearchParams(window.location.search).get('tipo') || 'gasto';
    actualizarBotonesTipo(tipoActual);

    // Event Listeners
    btnIngresoListado.addEventListener('click', () => cambiarTipoListado('ingreso'));
    btnGastoListado.addEventListener('click', () => cambiarTipoListado('gasto'));
    document.getElementById('btnNuevaCategoria').addEventListener('click', () => abrirModalCategoria());

    // Inicialización
    cargarCategorias();
    setupFormHandler();

    // Funciones principales
    function cargarCategorias() {
        fetch(`${BASE_URL}categoria/listarCategorias?tipo=${tipoActual}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarCategorias(data.categorias);
            } else {
                console.error('Error al cargar categorías:', data.message);
                if (data.error) {
                    console.error('Detalles del error:', data.error);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function renderizarCategorias(categorias) {
        if (!categorias || categorias.length === 0) {
            listadoCategorias.innerHTML = `
                <div class="text-center py-3">
                    No hay categorías registradas
                </div>
            `;
            return;
        }

        const html = categorias.map(cat => `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center"
                         style="width:36px;height:36px;background-color:${cat.color}">
                        <i class="bi ${cat.icono} text-white"></i>
                    </div>
                    <span class="ms-2">${cat.nombre}</span>
                </div>
                <div>
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary me-1"
                            onclick="window.editarCategoria(${cat.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger"
                            onclick="window.eliminarCategoria(${cat.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

        listadoCategorias.innerHTML = html;
    }

    function setupFormHandler() {
        // Configurar selección de iconos
        document.querySelectorAll('.icono-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                seleccionarIcono(this.dataset.icono);
            });
        });

        // Configurar selección de colores
        document.querySelectorAll('.color-paleta').forEach(div => {
            div.addEventListener('click', function() {
                seleccionarColor(this.dataset.color);
            });
        });

        // Configurar color picker
        document.getElementById('colorPicker').addEventListener('input', function() {
            seleccionarColor(this.value);
        });

        // Configurar botones de tipo
        document.getElementById('btnIngreso').addEventListener('click', function() {
            seleccionarTipo('ingreso');
        });
        document.getElementById('btnGasto').addEventListener('click', function() {
            seleccionarTipo('gasto');
        });

        // Manejar envío del formulario
        formCategoria.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) return;
            
            const formData = new FormData(this);
            const id = document.getElementById('idCategoria').value;
            const url = id ? `${BASE_URL}categoria/editar/${id}` : `${BASE_URL}categoria/registrar`;
            
            console.log('Enviando a:', url); // Debug
            console.log('ID de categoría:', id); // Debug
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalCategoria.hide();
                    cargarCategorias();
                    // Limpiar el formulario después de guardar
                    formCategoria.reset();
                    document.getElementById('idCategoria').value = '';
                } else {
                    alert(data.message || 'Error al procesar la categoría');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la categoría');
            });
        });
    }

    function seleccionarTipo(tipo) {
        document.getElementById('tipo').value = tipo;
        document.getElementById('btnIngreso').classList.toggle('active', tipo === 'ingreso');
        document.getElementById('btnGasto').classList.toggle('active', tipo === 'gasto');
    }

    function seleccionarIcono(icono) {
        document.getElementById('icono').value = icono;
        // Remover clase selected de todos los botones
        document.querySelectorAll('.icono-btn').forEach(btn => {
            btn.classList.remove('selected');
        });
        // Agregar clase selected al botón seleccionado
        const iconoBtn = document.querySelector(`.icono-btn[data-icono="${icono}"]`);
        if (iconoBtn) {
            iconoBtn.classList.add('selected');
        }
    }

    function seleccionarColor(color) {
        document.getElementById('color').value = color;
        // Remover clase selected de todos los colores
        document.querySelectorAll('.color-paleta').forEach(div => {
            div.classList.remove('selected');
        });
        // Agregar clase selected al color seleccionado
        const colorPaleta = document.querySelector(`.color-paleta[data-color="${color}"]`);
        if (colorPaleta) {
            colorPaleta.classList.add('selected');
        }
    }

    function abrirModalCategoria(categoria = null) {
        document.getElementById('modalTitulo').textContent = 
            categoria ? 'Editar Categoría' : 'Crear Categoría';
        
        // Limpiar el formulario
        formCategoria.reset();
        
        if (categoria) {
            // Si es edición, establecer los valores
            document.getElementById('idCategoria').value = categoria.id;
            document.getElementById('nombre').value = categoria.nombre;
            document.getElementById('tipo').value = categoria.tipo;
            document.getElementById('icono').value = categoria.icono;
            document.getElementById('color').value = categoria.color;
            
            // Actualizar la selección visual
            seleccionarTipo(categoria.tipo);
            seleccionarIcono(categoria.icono);
            seleccionarColor(categoria.color);
        } else {
            // Si es nuevo registro, limpiar el ID y establecer valores por defecto
            document.getElementById('idCategoria').value = '';
            seleccionarTipo(tipoActual);
            seleccionarIcono('bi-house');
            seleccionarColor('#36A2EB');
        }
        
        modalCategoria.show();
    }

    function cambiarTipoListado(tipo) {
        tipoActual = tipo;
        actualizarBotonesTipo(tipo);
        cargarCategorias();
    }

    function actualizarBotonesTipo(tipo) {
        btnIngresoListado.classList.toggle('active', tipo === 'ingreso');
        btnGastoListado.classList.toggle('active', tipo === 'gasto');
    }

    function validarFormulario() {
        const campos = ['nombre', 'tipo', 'icono', 'color'];
        for (const campo of campos) {
            const valor = document.getElementById(campo).value.trim();
            if (!valor) {
                alert('Por favor complete todos los campos requeridos');
                return false;
            }
        }
        return true;
    }

    // Hacer las funciones disponibles globalmente
    window.editarCategoria = function(id) {
        fetch(`${BASE_URL}categoria/editar/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                abrirModalCategoria(data.data);
            } else {
                alert(data.message || 'Error al cargar la categoría');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar la categoría');
        });
    };

    window.eliminarCategoria = function(id) {
        if (confirm('¿Está seguro de eliminar esta categoría? Esta acción no se puede deshacer.')) {
            fetch(`${BASE_URL}categoria/eliminar/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        ${data.message || 'Categoría eliminada correctamente'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.card').insertBefore(alertDiv, document.getElementById('listadoCategorias'));
                    
                    // Recargar la lista de categorías
                    cargarCategorias();
                } else {
                    alert(data.message || 'Error al eliminar la categoría');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la categoría');
            });
        }
    };
});
  