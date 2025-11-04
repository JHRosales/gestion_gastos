# Convenciones de Código - Sistema de Gestión de Gastos

## Contexto General del Proyecto

Este proyecto sigue una arquitectura MVC (Modelo-Vista-Controlador) en PHP con MySQL como base de datos y jQuery para JavaScript en el frontend.

---

El proyecto tiene la siguiente estructura de carpetas:

- **controllers/**: Contiene los controladores que manejan la lógica de negocio y las peticiones HTTP.
- **models/**: Contiene los modelos que interactúan con la base de datos y manejan la lógica de datos.
- **views/**: Contiene las vistas que definen la interfaz de usuario y la presentación de datos.
- **public/**: Contiene archivos estáticos como CSS, JavaScript e imágenes.
- **sqlBD/**: Contiene los archivos relacionados con la base de datos, incluyendo scripts SQL. cuando se realice modificaciones a nivel de BD se debe incluir en este archivo
- **application/**: Contiene archivos de configuración y utilidades de la aplicación.
- **index.php**: Punto de entrada principal de la aplicación.
- **.htaccess**: Archivo de configuración de Apache para el manejo de URLs amigables.
- **documentacion/**: En esta carpeta se encuentran las convensiones, y la documentacion de las funcionalidades, cuando se agregue una nueva funcionalidad se debe registar, y si se modifica el flujo o funcionaidad se debe actualizar en este folder el documento que corresponda.

## Características Principales del sistema
- Dashboard, pagina principal
- Registro y gestión de gastos
- Categorización de gastos
- Informes y estadísticas
- Exportación de datos
- Gestión de usuarios y perfiles

## Convenciones para JavaScript

### 1. Usar jQuery en lugar de JavaScript Vanilla

**✅ HACER:**
```javascript
// Obtener valores
const valor = $('#elemento').val();

// Obtener texto
const texto = $('#elemento').text();

// Modificar contenido
$('#elemento').html('<p>Contenido</p>');

// Agregar clases
$('#elemento').addClass('clase-nueva');

// Eventos
$('#boton').on('click', function() { /* ... */ });
```

**❌ NO HACER:**
```javascript
// No usar vanilla JavaScript
document.getElementById('elemento').value;
element.innerHTML = '<p>Contenido</p>';

// No usar fetch API, usar $.ajax
fetch(url).then(response => response.json());
```

### 2. Validación de Contenido

**✅ HACER:**
```javascript
// Validar arrays con .length
if (Array.isArray(datos) && datos.length > 0) {
  // Procesar datos
}

// Validar strings con .length
if (texto && texto.length > 0) {
  // Procesar texto
}

// Validar elementos jQuery
if ($('#elemento').length > 0) {
  // Elemento existe
}
```

**❌ NO HACER:**
```javascript
// No usar empty() o isset() en JavaScript (es de PHP)
if (empty(elemento)) { /* ERROR */ }

// No confiar solo en truthy/falsy
if (datos) { /* Puede ser un array vacío */ }
```

---

## Convenciones para PHP

### 1. Validación de Datos

**✅ HACER:**
```php
// luego count()
if (is_array($this->cuentas) && count($this->cuentas) > 0) {
  // Procesar
}

// Validar POST
if (isset($_POST['campo']) && $_POST['campo'] !== '') {
  $valor = trim($_POST['campo']);
}
```

**❌ NO HACER:**
```php
// No asumir que existe
if ($this->cuentas) { /* Puede no existir */ }

// No usar empty()
if (empty($_POST['campo'])) { /* Puede dar warning */ }
```

### 2. Comparación de Valores

**✅ HACER:**
```php
// Comparaciones específicas
if ($cuenta_id !== null && $cuenta_id > 0) { /* ... */ }
if ($_POST['campo'] !== '') { /* ... */ }

// Comparar tipo y valor
if (is_array($datos) && count($datos) > 0) { /* ... */ }
```

---

## Convenciones para HTML/PHP en Vistas

### 1. Validación de Datos para Mostrar

**✅ HACER:**
```php
<?php if (is_array($this->cuentas) && count($this->cuentas) > 0): ?>
  <?php foreach ($this->cuentas as $cuenta): ?>
    <!-- Mostrar contenido -->
  <?php endforeach; ?>
<?php else: ?>
  <p>No hay datos</p>
<?php endif; ?>
```

---

## Buenas Prácticas Generales

### 1. Siempre Usar `is_array()` antes de `empty()` en PHP

```php
// ✅ Correcto
if (count($array) == 0) { }

// ❌ Incorrecto
if (empty($variable)) { } // Puede dar warning
```

### 2. Validar Arrays antes de Iterar

```php
// ✅ Correcto
if (is_array($array) && count($array) > 0) {
  foreach ($array as $item) { }
}
```

### 3. Prefijo `$` para Variables jQuery

```javascript
// ✅ Correcto
const $elemento = $('#id');
```

