# Documentación: Implementación del Sistema de Transferencias

## Descripción General
Implementación de un sistema de transferencias entre cuentas. Una transferencia consiste en mover un monto de una cuenta origen a una cuenta destino. En términos contables, se registra como un gasto en la cuenta origen y un ingreso en la cuenta destino, pero se diferencia de los gastos e ingresos normales para mantener la integridad de los reportes y balances.

## Objetivos
- Permitir transferir montos entre cuentas del mismo usuario
- Registrar automáticamente un gasto en la cuenta origen y un ingreso en la cuenta destino
- Diferenciar transferencias de gastos e ingresos normales para reportes precisos
- Mantener la integridad referencial entre el gasto, ingreso y la transferencia

## Ejemplo de Uso
Transferir S/. 500 de la cuenta "Ahorro" a la cuenta "Efectivo":
- Se registra un gasto de S/. 500 en la cuenta "Ahorro" (categoría: Transferencia)
- Se registra un ingreso de S/. 500 en la cuenta "Efectivo" (categoría: Transferencia)
- Ambos registros están vinculados a la misma transferencia para mantener la traza

## Estructura de Cambios

### 1. Base de Datos
- **Crear tabla `transferencias`**: Almacenará la información de las transferencias realizadas
- **Modificar tabla `ingresos`**: Agregar campos `es_transferencia` y `transferencia_id`
- **Modificar tabla `gastos`**: Agregar campos `es_transferencia` y `transferencia_id`

### 2. Backend
- **TransferenciaModel**: Modelo para gestionar transferencias (crear, listar, obtener por ID, eliminar)
- **TransferenciaController**: Controlador para las operaciones de transferencias
- **Modificar IngresoModel**: Soporte para crear ingresos como parte de transferencias
- **Modificar GastoModel**: Soporte para crear gastos como parte de transferencias

### 3. Frontend
- **Vista de registro de transferencias**: Formulario para crear transferencias
- **Botón en dashboard**: Agregar botón "Registrar Transferencia" junto a ingresos, gastos y metas
- **Filtrado en reportes**: Excluir transferencias de reportes normales (opcional, futuro)

---

## Plan de Implementación

### Paso 1: Crear Estructura de Base de Datos
**Status:** ✅ Completado  
**Archivo:** `sqlBD/crear_tabla_transferencias.sql`

Crear la tabla de transferencias con los siguientes campos:
- `id`: Identificador único
- `usuario_id`: FK a usuarios
- `cuenta_origen_id`: FK a cuentas (cuenta desde donde se transfiere)
- `cuenta_destino_id`: FK a cuentas (cuenta hacia donde se transfiere)
- `monto`: Monto de la transferencia
- `descripcion`: Descripción opcional de la transferencia
- `fecha`: Fecha de la transferencia
- `gasto_id`: FK a gastos (gasto generado en cuenta origen)
- `ingreso_id`: FK a ingresos (ingreso generado en cuenta destino)
- `fecha_creacion`: Timestamp de creación
- `fecha_modificacion`: Timestamp de última modificación

Agregar campos a las tablas existentes:
- `ingresos.es_transferencia`: TINYINT(1) DEFAULT 0 (indica si el ingreso es parte de una transferencia)
- `ingresos.transferencia_id`: INT NULL (FK a transferencias)
- `gastos.es_transferencia`: TINYINT(1) DEFAULT 0 (indica si el gasto es parte de una transferencia)
- `gastos.transferencia_id`: INT NULL (FK a transferencias)

### Paso 2: Crear TransferenciaModel
**Status:** ✅ Completado  
**Archivo:** `models/TransferenciaModel.php`

Métodos necesarios:
- `crearTransferencia($usuario_id, $cuenta_origen_id, $cuenta_destino_id, $monto, $descripcion, $fecha)`: Crear transferencia y sus registros asociados (gasto e ingreso)
- `listarTransferencias($usuario_id)`: Listar todas las transferencias del usuario
- `obtenerPorId($id, $usuario_id)`: Obtener una transferencia específica
- `eliminarTransferencia($id, $usuario_id)`: Eliminar transferencia y sus registros asociados

**Nota importante:** La creación de transferencia debe ser una transacción atómica:
1. Crear el registro de transferencia
2. Crear el gasto en la cuenta origen (con es_transferencia=1 y transferencia_id)
3. Crear el ingreso en la cuenta destino (con es_transferencia=1 y transferencia_id)
4. Si alguna operación falla, hacer rollback

### Paso 3: Crear TransferenciaController
**Status:** ✅ Completado  
**Archivo:** `controllers/transferenciaController.php`

Endpoints necesarios:
- `index()`: Redirigir a dashboard (o listar transferencias si se requiere en el futuro)
- `registrar()`: Mostrar formulario y procesar creación de transferencia
- `eliminar($id)`: Eliminar transferencia (AJAX)

### Paso 4: Crear Vista de Registro de Transferencias
**Status:** ✅ Completado  
**Archivo:** `views/transferencia/registrar.php`

Formulario con:
- Selector de cuenta origen (dropdown de cuentas)
- Selector de cuenta destino (dropdown de cuentas, excluyendo la cuenta origen)
- Campo de monto (número decimal)
- Campo de descripción (opcional, textarea)
- Campo de fecha (date picker)

Validaciones:
- La cuenta origen y destino deben ser diferentes
- El monto debe ser mayor a 0
- La fecha es obligatoria
- Ambas cuentas deben pertenecer al usuario actual

### Paso 5: Modificar Dashboard para Incluir Botón de Transferencias
**Status:** ✅ Completado  
**Archivo:** `views/dashboard/index.php`

Agregar botón "Registrar Transferencia" en la sección de botones de acceso rápido, junto a:
- Registrar nuevo ingreso
- Registrar nuevo gasto
- Registrar nueva meta

El botón debe:
- Tener estilo visual diferenciado (color: warning/info)
- Redirigir a `transferencia/registrar`
- Tener icono apropiado (bi-arrow-left-right o similar)

### Paso 6: Actualizar Header/Navegación (Opcional)
**Status:** ⏸️ No implementado (opcional)  
**Archivo:** `views/partials/default/header.php`

**Nota:** Se decidió no agregar el enlace en el header ya que el botón principal en el dashboard es suficiente para el acceso rápido a la funcionalidad.

---

## Flujo de Proceso

### Flujo de Creación de Transferencia

1. **Usuario accede al formulario de transferencias**
   - URL: `/transferencia/registrar`
   - El controlador carga las cuentas del usuario

2. **Usuario completa el formulario**
   - Selecciona cuenta origen
   - Selecciona cuenta destino (validación JS: no puede ser igual a origen)
   - Ingresa monto (validación JS: debe ser > 0)
   - Ingresa descripción (opcional)
   - Selecciona fecha

3. **Envío del formulario (POST)**
   - Validaciones en el controlador:
     - Usuario autenticado
     - Cuenta origen existe y pertenece al usuario
     - Cuenta destino existe y pertenece al usuario
     - Cuenta origen ≠ cuenta destino
     - Monto > 0
     - Fecha válida

4. **Procesamiento en el modelo (transacción atómica)**
   - Iniciar transacción
   - Crear registro en tabla `transferencias`
   - Crear gasto en `gastos`:
     - `usuario_id`: usuario actual
     - `cuenta_id`: cuenta origen
     - `monto`: monto de la transferencia
     - `categoria`: "Transferencia"
     - `descripcion`: "Transferencia a [Nombre cuenta destino]" + descripción del usuario
     - `fecha`: fecha de transferencia
     - `es_transferencia`: 1
     - `transferencia_id`: ID de la transferencia creada
   - Crear ingreso en `ingresos`:
     - `usuario_id`: usuario actual
     - `cuenta_id`: cuenta destino
     - `monto`: monto de la transferencia
     - `categoria`: "Transferencia"
     - `descripcion`: "Transferencia desde [Nombre cuenta origen]" + descripción del usuario
     - `fecha`: fecha de transferencia
     - `es_transferencia`: 1
     - `transferencia_id`: ID de la transferencia creada
   - Actualizar `transferencias` con los IDs de gasto e ingreso creados
   - Commit transacción
   - Si hay error en cualquier paso, hacer rollback

5. **Redirección y mensaje**
   - Si éxito: Redirigir a dashboard con mensaje de éxito
   - Si error: Redirigir a dashboard con mensaje de error

### Flujo de Eliminación de Transferencia

1. **Usuario solicita eliminar transferencia** (futuro: desde listado de transferencias)
2. **Controlador valida permisos** (usuario es propietario)
3. **Modelo elimina en transacción atómica**:
   - Eliminar gasto asociado
   - Eliminar ingreso asociado
   - Eliminar registro de transferencia
4. **Retornar respuesta JSON** (para manejo AJAX)

---

## Consideraciones Importantes

### Integridad Referencial
- Las transferencias deben crearse siempre como una transacción atómica
- Si falla la creación del gasto o ingreso, no debe quedar registro de transferencia
- Al eliminar una transferencia, deben eliminarse también el gasto e ingreso asociados

### Diferenciación en Reportes
- Los gastos e ingresos marcados con `es_transferencia = 1` no deberían afectar el cálculo de saldo total cuando se ve "Todas las cuentas"
- Sin embargo, sí afectan el saldo individual de cada cuenta
- Para reportes futuros, puede ser útil filtrar transferencias para evitar duplicación

### Validaciones
- No permitir transferencias entre la misma cuenta
- Validar que ambas cuentas pertenezcan al usuario
- Validar que el monto sea positivo
- Validar que la cuenta origen tenga fondos suficientes (opcional, futuro)

### Categoría de Transferencia
- Se usará la categoría "Transferencia" para gastos e ingresos de transferencias
- Esta categoría debe existir en la base de datos o crearse automáticamente
- Tipo: puede ser tanto 'ingreso' como 'gasto' (o crear tipo especial 'transferencia')

---

## Archivos a Crear/Modificar

### Nuevos Archivos
- [x] `documentacion/implementacion_transferencias.md` (este archivo) ✅
- [x] `sqlBD/crear_tabla_transferencias.sql` ✅
- [x] `models/TransferenciaModel.php` ✅
- [x] `controllers/transferenciaController.php` ✅
- [x] `views/transferencia/registrar.php` ✅

### Archivos Modificados
- [x] `models/IngresoModel.php` (agregados campos es_transferencia y transferencia_id) ✅
- [x] `models/GastoModel.php` (agregados campos es_transferencia y transferencia_id) ✅
- [x] `views/dashboard/index.php` (agregado botón y mensajes de éxito/error) ✅

### Archivos Opcionales
- [ ] `views/partials/default/header.php` (opcional: agregar enlace en menú - no implementado)

---

## Notas de Implementación

### Manejo de Transacciones
El método `crearTransferencia()` debe usar transacciones PDO:
```php
$this->conn->beginTransaction();
try {
    // 1. Insertar transferencia
    // 2. Insertar gasto
    // 3. Insertar ingreso
    // 4. Actualizar transferencia con IDs
    $this->conn->commit();
    return $transferencia_id;
} catch (Exception $e) {
    $this->conn->rollBack();
    return false;
}
```

### Mensajes al Usuario
- Éxito: "Transferencia realizada correctamente."
- Error: "Error al realizar la transferencia. Verifique que ambas cuentas sean válidas y diferentes."
- Validación: "La cuenta origen y destino deben ser diferentes."

---

## Implementación Completada

### Pasos Realizados

1. ✅ **Script SQL ejecutado**: Se creó la tabla `transferencias` y se modificaron las tablas `ingresos` y `gastos` con los campos necesarios
2. ✅ **Categoría "Transferencia" creada**: Se crearon automáticamente las categorías para ingresos y gastos de tipo transferencia
3. ✅ **TransferenciaModel implementado**: Modelo completo con métodos de crear, listar, obtener por ID y eliminar, usando transacciones atómicas
4. ✅ **TransferenciaController implementado**: Controlador con métodos index, registrar y eliminar, con validaciones completas
5. ✅ **Vista de registro creada**: Formulario completo con validaciones JavaScript en el frontend
6. ✅ **Botón en dashboard agregado**: Botón de acceso rápido "Registrar transferencia" con estilo diferenciado (btn-info)
7. ✅ **Mensajes de éxito/error**: Implementados mensajes de retroalimentación al usuario en el dashboard
8. ✅ **Validaciones implementadas**: 
   - Validación en frontend (JavaScript) para cuentas diferentes y monto > 0
   - Validación en backend (PHP) para todas las reglas de negocio
   - Transacciones atómicas para garantizar integridad de datos

### Funcionalidades Implementadas

- ✅ Transferir montos entre cuentas del mismo usuario
- ✅ Registro automático de gasto en cuenta origen e ingreso en cuenta destino
- ✅ Diferenciación de transferencias mediante campos `es_transferencia` y `transferencia_id`
- ✅ Integridad referencial mediante transacciones atómicas
- ✅ Validaciones completas en frontend y backend
- ✅ Mensajes de retroalimentación al usuario
- ✅ Interfaz de usuario intuitiva y consistente con el resto de la aplicación

### Próximas Mejoras (Opcionales)

1. Listado de transferencias realizadas (vista de historial)
2. Edición de transferencias (futuro)
3. Filtrado de transferencias en reportes para evitar duplicación al ver "Todas las cuentas"
4. Validación de fondos suficientes antes de transferir
5. Transferencias programadas/recurrentes (futuro)

---

## Control de Versiones
- **Fecha de inicio:** Diciembre 2024
- **Última actualización:** Diciembre 2024
- **Estado general:** ✅ Completado y funcional

### Notas de Implementación

#### Funcionalidades Clave Implementadas
1. **Transacciones Atómicas**: Toda creación de transferencia se realiza en una transacción atómica que garantiza que si falla cualquier paso (crear transferencia, crear gasto, crear ingreso), se hace rollback completo.

2. **Validaciones Múltiples**:
   - Frontend: Validación en tiempo real con JavaScript para mejorar UX
   - Backend: Validación exhaustiva antes de procesar la transferencia
   - Base de datos: Constraints y foreign keys para integridad referencial

3. **Diferenciación de Transferencias**: 
   - Los gastos e ingresos creados por transferencias se marcan con `es_transferencia = 1`
   - Se almacena el `transferencia_id` para mantener la traza completa
   - Esto permite filtrar transferencias en reportes futuros si es necesario

4. **Experiencia de Usuario**:
   - Botón de acceso rápido en dashboard con estilo visual diferenciado
   - Validación en tiempo real que previene errores comunes
   - Mensajes claros de éxito y error
   - Deshabilitación automática de la cuenta seleccionada en el otro selector

#### Archivos de Configuración Necesarios
- El script SQL debe ejecutarse en la base de datos antes de usar la funcionalidad
- La categoría "Transferencia" se crea automáticamente al ejecutar el script SQL

---

## Referencias
- Ver `documentacion/implementacion_cuentas.md` para referencia de estructura
- Ver `documentacion/CONVENCIONES_CODIGO.md` para convenciones de código
