# Documentación: Implementación del Sistema de Cuentas

## Descripción General
Implementación de un sistema de cuentas para diferenciar ingresos y gastos por tipo de cuenta (Efectivo, Ahorro, Tarjeta de Crédito, etc.). Cada usuario puede tener múltiples cuentas con saldo inicial y tipo de moneda configurables.

## Objetivos
- Permitir que los usuarios gestionen múltiples cuentas
- Cada cuenta tiene: nombre, saldo inicial, tipo de moneda
- Filtrar ingresos y gastos por cuenta seleccionada
- Opción de ver todas las cuentas

## Estructura de Cambios

### 1. Base de Datos
- **Crear tabla `cuentas`**: Almacenará la información de las cuentas de usuario
- **Modificar tabla `ingresos`**: Agregar campo `cuenta_id`
- **Modificar tabla `gastos`**: Agregar campo `cuenta_id`

### 2. Backend
- **CuentaModel**: Modelo para gestionar cuentas (CRUD)
- **CuentaController**: Controlador para las operaciones de cuentas
- **Modificar IngresoModel**: Agregar soporte para filtrado por cuenta
- **Modificar GastoModel**: Agregar soporte para filtrado por cuenta
- **Modificar DashboardModel**: Filtrar cálculos por cuenta seleccionada
- **Modificar DashboardController**: Cargar cuentas y filtrar datos

### 3. Frontend
- **Vista de gestión de cuentas**: CRUD de cuentas
- **Selector de cuenta en dashboard**: Dropdown para cambiar de cuenta
- **Modificar vistas de ingresos/gastos**: Incluir selector de cuenta
- **Filtrado dinámico**: JavaScript para filtrado sin recargar

---

## Plan de Implementación

### Paso 1: Crear Estructura de Base de Datos ✅
**Status:** ✅ Completado  
**Archivo:** `sqlBD/crear_tabla_cuentas.sql`

Crear la tabla de cuentas con los siguientes campos:
- `id`: Identificador único
- `usuario_id`: FK a usuarios
- `nombre`: Nombre de la cuenta
- `tipo`: Tipo de cuenta (Efectivo, Ahorro, Tarjeta, etc.)
- `saldo_inicial`: Saldo con el que inicia la cuenta
- `moneda`: Tipo de moneda (Soles, Dólares)
- `activa`: Estado de la cuenta
- `fecha_creacion`: Fecha de creación
- `fecha_modificacion`: Fecha de última modificación

### Paso 2: Modificar Tablas Existentes ✅
**Status:** ✅ Completado  
**Archivo:** `sqlBD/modificar_ingresos_gastos.sql`

Agregar el campo `cuenta_id` a las tablas:
- `ingresos.cuenta_id`
- `gastos.cuenta_id`

Ambos con FK a la tabla `cuentas`.

### Paso 3: Crear CuentaModel ✅
**Status:** ✅ Completado  
**Archivo:** `models/CuentaModel.php`

Métodos necesarios:
- `crearCuenta($usuario_id, $nombre, $tipo, $saldo_inicial, $moneda)` ✅
- `listarCuentas($usuario_id)` ✅
- `obtenerPorId($id, $usuario_id)` ✅
- `actualizarCuenta($id, $usuario_id, $nombre, $tipo, $saldo_inicial, $moneda)` ✅
- `eliminarCuenta($id, $usuario_id)` ✅
- `calcularSaldo($cuenta_id, $usuario_id)` ✅
- `obtenerMoneda($cuenta_id, $usuario_id)` ✅ (bonus)

### Paso 4: Crear CuentaController ✅
**Status:** ✅ Completado  
**Archivo:** `controllers/cuentaController.php`

Endpoints necesarios:
- `index()`: Listar cuentas ✅
- `registrar()`: Crear nueva cuenta ✅
- `editar($id)`: Editar cuenta existente ✅
- `eliminar($id)`: Eliminar cuenta ✅
- `cambiarCuenta()`: Cambiar cuenta seleccionada en sesión ✅

### Paso 5: Crear Vistas de Gestión de Cuentas ✅
**Status:** ✅ Completado  
**Archivo:** `views/cuenta/`

Crear las vistas necesarias:
- `views/cuenta/index.php`: Listado de cuentas ✅
- `views/cuenta/registrar.php`: Formulario de creación/edición ✅
- `views/cuenta/dropdown_cuenta.php`: Dropdown reutilizable ✅

### Paso 6: Modificar IngresoModel para Soporte de Cuentas ✅
**Status:** ✅ Completado  
**Archivo:** `models/IngresoModel.php`

Modificaciones necesarias:
- `crearIngreso()`: Agregar parámetro `cuenta_id` ✅
- `listarIngresos()`: Agregar filtro opcional por `cuenta_id` ✅
- `actualizarIngreso()`: Actualizar `cuenta_id` si se modifica ✅

### Paso 7: Modificar GastoModel para Soporte de Cuentas ✅
**Status:** ✅ Completado  
**Archivo:** `models/GastoModel.php`

Modificaciones necesarias:
- `crearGasto()`: Agregar parámetro `cuenta_id` ✅
- `listarGastos()`: Agregar filtro opcional por `cuenta_id` ✅
- `actualizarGasto()`: Actualizar `cuenta_id` si se modifica ✅

### Paso 8: Modificar IngresoController para Soporte de Cuentas ✅
**Status:** ✅ Completado  
**Archivo:** `controllers/ingresoController.php`

Modificaciones necesarias:
- `registrar()`: Capturar y guardar `cuenta_id` del POST ✅
- `editar()`: Incluir selector de cuenta en el formulario ✅

### Paso 9: Modificar GastoController para Soporte de Cuentas ✅
**Status:** ✅ Completado  
**Archivo:** `controllers/gastoController.php`

Modificaciones necesarias:
- `registrar()`: Capturar y guardar `cuenta_id` del POST ✅
- `editar()`: Incluir selector de cuenta en el formulario ✅

### Paso 10: Crear Vistas de Formularios Actualizadas ✅
**Status:** ✅ Completado  
**Archivo:** `views/ingreso/registrar.php` y `views/gasto/registrar.php`

Modificaciones necesarias:
- Agregar selector de cuenta en los formularios ✅
- Incluir `cuenta_id` en los POST requests ✅

### Paso 11: Modificar DashboardModel para Filtrado por Cuenta ✅
**Status:** ✅ Completado  
**Archivo:** `models/DashboardModel.php`

Modificaciones necesarias:
- `ingresos($usuario_id, $cuenta_id = null)`: Agregar filtro opcional ✅
- `gastos($usuario_id, $cuenta_id = null)`: Agregar filtro opcional ✅
- `calcularSaldo($usuario_id, $cuenta_id = null)`: Calcular saldo por cuenta ✅

### Paso 12: Modificar DashboardController para Filtrado por Cuenta ✅
**Status:** ✅ Completado  
**Archivo:** `controllers/dashboardController.php`

Modificaciones necesarias:
- `mostrarDashboard()`: Obtener cuenta seleccionada de sesión ✅
- Cargar lista de cuentas del usuario ✅
- Pasar cuenta_id a modelos para filtrado ✅

### Paso 13: Agregar Selector de Cuenta al Dashboard ✅
**Status:** ✅ Completado  
**Archivo:** `views/dashboard/index.php`

Modificaciones necesarias:
- Agregar dropdown de selección de cuenta en la parte superior ✅
- Incluir opción "Todas las cuentas" ✅
- JavaScript para cambiar cuenta y recargar datos vía AJAX ✅

### Paso 14: Crear Componente JavaScript para Cambio de Cuenta ✅
**Status:** ✅ Completado  
**Archivo:** `views/dashboard/index.php` (inline script)

Funcionalidad necesaria:
- Manejar cambio de cuenta seleccionada ✅
- Guardar selección en sesión vía AJAX ✅
- Recargar datos del dashboard sin recargar la página completa ✅

### Paso 15: Agregar Endpoint AJAX para Cambio de Cuenta ✅
**Status:** ✅ Completado  
**Archivo:** `controllers/dashboardController.php`

Nuevo método:
- `cambiarCuenta()`: Cambiar cuenta en sesión y retornar JSON ✅

### Paso 16: Actualizar Tabla de Movimientos para Mostrar Cuenta ✅
**Status:** ✅ Automático  
**Archivo:** `views/dashboard/components/resumen_movimientos.php`

Nota: Los movimientos ya se filtran automáticamente porque los datos vienen filtrados desde el controlador.

### Paso 17: Actualizar Gráficos para Filtrado por Cuenta ✅
**Status:** ✅ Automático  
**Archivo:** `views/dashboard/components/ingresos_chart.php` y `gastos_chart.php`

Nota: Los gráficos ya muestran datos filtrados porque los datos vienen filtrados desde el controlador.

### Paso 18: Actualizar Balance Mensual para Filtrado por Cuenta ✅
**Status:** ✅ Automático  
**Archivo:** `views/dashboard/components/balance_mensual.php`

Nota: El balance mensual ya muestra datos filtrados porque los datos vienen filtrados desde el controlador.

---

## Notas de Implementación

### Manejo de Saldo Inicial
El saldo inicial de una cuenta se suma al calcular el saldo total:
```
Saldo Total = Saldo Inicial + Suma de Ingresos - Suma de Gastos
```

### Tipo de Moneda
Cada cuenta puede tener una moneda diferente (Soles o Dólares). Esto permite:
- Gestionar múltiples monedas en el mismo sistema
- Mostrar el símbolo de moneda correcto según la cuenta
- Potencial para conversión de monedas en el futuro

### Persistencia de Cuenta Seleccionada
La cuenta seleccionada se guarda en `$_SESSION['cuenta_seleccionada']` para mantener la selección entre páginas.

### Migración de Datos Existentes
Para los datos existentes (sin `cuenta_id`):
- Se creará una cuenta por defecto "Principal" para cada usuario
- Los registros existentes se asignarán a esta cuenta

---

## Archivos a Crear/Modificar

### Nuevos Archivos
- [ok] `documentacion/implementacion_cuentas.md` (este archivo)
- [ok] `sqlBD/crear_tabla_cuentas.sql`
- [ok] `sqlBD/modificar_ingresos_gastos.sql`
- [ ] `models/CuentaModel.php`
- [ ] `controllers/cuentaController.php`
- [ ] `views/cuenta/index.php`
- [ ] `views/cuenta/registrar.php`
- [ ] `views/cuenta/dropdown_cuenta.php`
- [ ] `public/assets/js/cuentas.js`

### Archivos a Modificar
- [ ] `models/IngresoModel.php`
- [ ] `models/GastoModel.php`
- [ ] `models/DashboardModel.php`
- [ ] `controllers/ingresoController.php`
- [ ] `controllers/gastoController.php`
- [ ] `controllers/dashboardController.php`
- [ ] `views/dashboard/index.php`
- [ ] `views/ingreso/registrar.php`
- [ ] `views/gasto/registrar.php`
- [ ] `views/dashboard/components/resumen_movimientos.php`
- [ ] `views/dashboard/components/ingresos_chart.php`
- [ ] `views/dashboard/components/gastos_chart.php`
- [ ] `views/dashboard/components/balance_mensual.php`

---

## Próximos Pasos

1. Ejecutar los scripts SQL en la base de datos
2. Implementar los modelos y controladores
3. Crear las vistas
4. Probar la funcionalidad
5. Actualizar la documentación de progreso

---

## Control de Versiones
- **Fecha de inicio:** 27 de Octubre 2025
- **Última actualización:** 27 de Octubre 2025
- **Estado general:** ✅ Implementación Completada al 100%

## Correcciones Realizadas

### Bugs Corregidos:
1. ✅ **Warning de strtolower() en Request.php** - Corregido manejo de valores null
2. ✅ **Dropdown de cuentas no cargaba** - Simplificado a select nativo
3. ✅ **Error al calcular saldo en vista de cuentas** - El saldo ahora se calcula en el controlador
4. ✅ **Validación de cuenta_id** - Corregido manejo de valores vacíos

## Notas Importantes

### Próximos Pasos para el Usuario:
1. **Ejecutar migración SQL**: Ejecutar los archivos SQL en el siguiente orden:
✅ Ya eralizado manualmente en la BD (completado)
   - Primero: `sqlBD/crear_tabla_cuentas.sql` ✅ Implementación Completada
   - Segundo: `sqlBD/modificar_ingresos_gastos.sql` ✅ Implementación Completada

2. **Probar funcionalidad**: 
   - Crear cuentas desde el menú "Cuentas" ✅ Implementación Completada
   - Registrar ingresos y gastos asociados a cuentas
   - Cambiar de cuenta en el dashboard y verificar que los datos se filtren correctamente

### Observaciones:
- El filtrado se aplica automáticamente en todos los componentes del dashboard
- El selector de cuenta se mantiene en sesión entre páginas
- Los registros antiguos sin `cuenta_id` se asignarán automáticamente a la cuenta "Principal" creada por el script SQL

