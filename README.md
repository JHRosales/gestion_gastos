# Gestión de Gastos

Este proyecto, **gestion_gastos**, es una aplicación diseñada para ayudar a los usuarios a gestionar sus gastos de manera eficiente. La aplicación está construida utilizando PHP y MySQL, siguiendo una arquitectura MVC (Modelo-Vista-Controlador) que facilita el mantenimiento y la escalabilidad.

## Estructura del Proyecto

El proyecto tiene la siguiente estructura de carpetas:

- **controllers/**: Contiene los controladores que manejan la lógica de negocio y las peticiones HTTP.
- **models/**: Contiene los modelos que interactúan con la base de datos y manejan la lógica de datos.
- **views/**: Contiene las vistas que definen la interfaz de usuario y la presentación de datos.
- **public/**: Contiene archivos estáticos como CSS, JavaScript e imágenes.
- **sqlBD/**: Contiene los archivos relacionados con la base de datos, incluyendo scripts SQL.
- **application/**: Contiene archivos de configuración y utilidades de la aplicación.
- **index.php**: Punto de entrada principal de la aplicación.
- **.htaccess**: Archivo de configuración de Apache para el manejo de URLs amigables.

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web Apache con mod_rewrite habilitado

## Instalación

1. Clona el repositorio en tu servidor web:
```bash
git clone [URL_DEL_REPOSITORIO]
```

2. Crea una base de datos MySQL y ejecuta los scripts SQL necesarios que se encuentran en la carpeta `sqlBD/`.

3. Configura los parámetros de conexión a la base de datos en el archivo de configuración correspondiente.

4. Asegúrate de que el servidor web tenga permisos de escritura en las carpetas necesarias.

## Uso

1. Accede a la aplicación a través de tu navegador web.
2. La URL base dependerá de tu configuración del servidor web.
3. Inicia sesión con tus credenciales para comenzar a gestionar tus gastos.

## Características Principales

- Registro y gestión de gastos
- Categorización de gastos
- Informes y estadísticas
- Exportación de datos
- Gestión de usuarios y perfiles

## Contribuciones

Las contribuciones son bienvenidas. Si deseas contribuir a este proyecto:

1. Haz un fork del repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Haz commit de tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo LICENSE para más detalles.

## Soporte

Si encuentras algún problema o tienes alguna sugerencia, por favor abre un issue en el repositorio.