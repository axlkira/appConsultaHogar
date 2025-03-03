# Documentación del Proyecto AppConsultaHogar

## Índice
1. [Descripción General](#descripción-general)
2. [Tecnologías Utilizadas](#tecnologías-utilizadas)
3. [Estructura del Proyecto](#estructura-del-proyecto)
4. [Estructura de la Base de Datos](#estructura-de-la-base-de-datos)
5. [Funcionalidades Principales](#funcionalidades-principales)
6. [Rutas y Endpoints](#rutas-y-endpoints)
7. [Controladores](#controladores)
8. [Requisitos del Sistema](#requisitos-del-sistema)
9. [Instalación y Configuración](#instalación-y-configuración)
10. [Uso del Sistema](#uso-del-sistema)

## Descripción General
AppConsultaHogar es una aplicación web desarrollada con Laravel que permite realizar consultas y gestionar información relacionada con hogares. El sistema incluye funcionalidades para búsqueda, consulta de detalles, descarga de fichas y gestión de logros por integrante.

## Tecnologías Utilizadas
- **Framework Backend:** Laravel
- **Frontend:** 
  - Tailwind CSS
  - JavaScript
- **Base de Datos:** MySQL
- **Entorno de Desarrollo:** XAMPP
- **Gestor de Dependencias:** Composer y NPM

## Estructura del Proyecto
```
appConsultaHogar/
├── app/
│   └── Http/Controllers/         # Controladores de la aplicación
├── config/                       # Archivos de configuración
├── database/                     # Migraciones y seeders
├── public/                       # Archivos públicos
├── resources/                    # Vistas y assets
├── routes/                       # Definición de rutas
└── storage/                      # Archivos de almacenamiento
```

## Estructura de la Base de Datos

### Tablas del Sistema

1. **users**
   - `id` - ID autoincremental (Primary Key)
   - `name` - Nombre del usuario
   - `email` - Correo electrónico (único)
   - `email_verified_at` - Fecha de verificación del email
   - `password` - Contraseña encriptada
   - `remember_token` - Token para "recordar sesión"
   - `created_at` - Fecha de creación
   - `updated_at` - Fecha de última actualización

2. **password_reset_tokens**
   - `email` - Correo electrónico (Primary Key)
   - `token` - Token de reseteo
   - `created_at` - Fecha de creación

3. **sessions**
   - `id` - ID de sesión (Primary Key)
   - `user_id` - ID del usuario (Foreign Key)
   - `ip_address` - Dirección IP
   - `user_agent` - Agente de usuario
   - `payload` - Datos de la sesión
   - `last_activity` - Última actividad

4. **cache**
   - `key` - Clave de caché (Primary Key)
   - `value` - Valor almacenado
   - `expiration` - Fecha de expiración

5. **jobs**
   - `id` - ID del trabajo (Primary Key)
   - `queue` - Cola de trabajo
   - `payload` - Datos del trabajo
   - `attempts` - Número de intentos
   - `reserved_at` - Fecha de reserva
   - `available_at` - Fecha de disponibilidad
   - `created_at` - Fecha de creación

### Relaciones
- La tabla `sessions` tiene una relación con `users` a través de `user_id`

### Notas sobre la Base de Datos
- El sistema utiliza autenticación nativa de Laravel
- Implementa sistema de caché para optimización de consultas
- Incluye sistema de colas para procesamiento de trabajos en segundo plano
- Manejo de sesiones para control de acceso y seguridad

## Funcionalidades Principales
1. **Consulta de Hogares**
   - Búsqueda de información de hogares
   - Visualización de detalles
   - Descarga de fichas

2. **Gestión de Logros**
   - Consulta de logros por integrante
   - Visualización de dimensiones
   - Seguimiento de progreso

3. **Sistema de Búsqueda**
   - Búsqueda general
   - Búsqueda específica MEF
   - Procesamiento de resultados

## Rutas y Endpoints

### Rutas Principales
- `/` - Página principal (redirección a búsqueda)
- `/search` - Interfaz de búsqueda general
- `/searchMef` - Búsqueda específica MEF
- `/consultaHogar` - Consulta de información de hogares

### Rutas de Procesamiento
- `/search/process` - Procesamiento de búsqueda general
- `/searchMef/process` - Procesamiento de búsqueda MEF
- `/consultaHogar/process` - Procesamiento de consulta de hogares

### Rutas de Detalles y Descargas
- `/consultaHogar/details` - Obtención de detalles
- `/consultaHogar/download` - Descarga de información
- `/consultaHogar/download-ficha` - Descarga de ficha específica

### Rutas de Logros
- `/consulta-hogar/logros-integrante` - Consulta de logros por integrante
- `/getDimensiones` - Obtención de dimensiones

## Controladores
1. **ConsultaHogarController**
   - Gestión principal de consultas de hogares
   - Procesamiento de información
   - Generación de descargas

2. **SearchController**
   - Manejo de búsquedas generales
   - Procesamiento de resultados
   - Obtención de detalles

3. **SearchMefController**
   - Gestión de búsquedas específicas MEF
   - Procesamiento especializado

## Requisitos del Sistema
- PHP >= 8.0
- Composer
- Node.js y NPM
- XAMPP (Apache + MySQL)
- Extensiones PHP requeridas por Laravel

## Instalación y Configuración

1. **Clonar el Repositorio**
```bash
git clone [URL_del_repositorio]
cd appConsultaHogar
```

2. **Instalar Dependencias**
```bash
composer install
npm install
```

3. **Configuración del Entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar Base de Datos**
- Crear base de datos en MySQL
- Configurar credenciales en .env
- Ejecutar migraciones:
```bash
php artisan migrate
```

5. **Compilar Assets**
```bash
npm run dev
```

## Uso del Sistema

### Acceso al Sistema
1. Iniciar los servicios de XAMPP (Apache y MySQL)
2. Acceder a través del navegador: `http://localhost/appConsultaHogar`

### Realizar Consultas
1. Acceder a la página principal
2. Utilizar el formulario de búsqueda correspondiente
3. Ingresar los criterios de búsqueda
4. Procesar la consulta
5. Visualizar o descargar resultados según sea necesario

### Gestión de Logros
1. Acceder a la sección de logros
2. Seleccionar el integrante
3. Consultar dimensiones y progreso
4. Descargar información si es necesario

## Mantenimiento y Soporte
- Realizar respaldos periódicos de la base de datos
- Mantener actualizado el sistema y sus dependencias
- Revisar logs de errores en `storage/logs`
- Mantener actualizadas las variables de entorno según sea necesario
