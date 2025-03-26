# Documentación del Sistema appConsultaHogar
*Fecha: 23 de marzo de 2025*

## Índice
1. [Introducción](#introducción)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Estructura del Proyecto](#estructura-del-proyecto)
4. [Módulos Principales](#módulos-principales)
5. [Flujo de Datos](#flujo-de-datos)
6. [Componentes de Interfaz](#componentes-de-interfaz)
7. [Base de Datos](#base-de-datos)
8. [Procedimientos Almacenados](#procedimientos-almacenados)
9. [Sistema de Logros](#sistema-de-logros)

## Introducción

El sistema **appConsultaHogar** es una aplicación web desarrollada con Laravel que permite la consulta y gestión de información relacionada con hogares, integrantes y sus logros en programas sociales. La aplicación está diseñada para facilitar el seguimiento de los avances de cada integrante del hogar en diferentes dimensiones y logros establecidos.

El sistema es una evolución de un proyecto anterior desarrollado en CodeIgniter, manteniendo la funcionalidad principal pero mejorando la estructura, rendimiento y experiencia de usuario.

## Arquitectura del Sistema

La aplicación sigue el patrón de arquitectura MVC (Modelo-Vista-Controlador) proporcionado por el framework Laravel:

- **Modelos**: Representan la estructura de datos y la lógica de negocio.
- **Vistas**: Implementadas con Blade, el motor de plantillas de Laravel, para la presentación de la información.
- **Controladores**: Gestionan las peticiones del usuario y coordinan la interacción entre modelos y vistas.

El sistema se comunica con múltiples bases de datos para obtener información de hogares, integrantes y logros.

## Estructura del Proyecto

La estructura del proyecto sigue la convención estándar de Laravel:

```
appConsultaHogar/
├── app/                    # Lógica principal de la aplicación
│   ├── Http/
│   │   ├── Controllers/    # Controladores de la aplicación
│   ├── Models/             # Modelos de datos
├── bootstrap/              # Archivos de inicialización
├── config/                 # Configuraciones de la aplicación
├── database/               # Migraciones y semillas
├── public/                 # Punto de entrada y assets públicos
├── resources/              # Vistas, assets sin compilar
│   ├── views/              # Plantillas Blade
│   │   ├── components/     # Componentes reutilizables
├── routes/                 # Definición de rutas
│   ├── web.php             # Rutas web
├── storage/                # Archivos generados por la aplicación
└── tests/                  # Pruebas automatizadas
```

## Módulos Principales

### 1. Consulta de Hogares

Permite buscar hogares por folio o documento de identidad. Incluye:
- Formulario de búsqueda
- Visualización de resultados en tabla
- Detalles del hogar seleccionado

### 2. Gestión de Integrantes

Muestra información detallada de los integrantes de un hogar:
- Listado de integrantes
- Información personal
- Relación con el titular del hogar

### 3. Sistema de Logros

Gestiona los logros de cada integrante en diferentes dimensiones:
- Visualización de logros por dimensión
- Estado inicial y final de cada logro
- Detalles específicos de cada logro

## Flujo de Datos

1. **Búsqueda inicial**: El usuario ingresa un folio o documento para buscar un hogar.
2. **Visualización de resultados**: Se muestran los hogares que coinciden con la búsqueda.
3. **Selección de hogar**: Al seleccionar un hogar, se muestra información detallada y sus integrantes.
4. **Consulta de logros**: Al seleccionar un integrante, se muestran sus logros organizados por dimensiones.
5. **Detalle de logro**: Al seleccionar un logro específico, se muestra información detallada sobre el mismo.

## Componentes de Interfaz

### Componentes Blade

1. **integrantes-list.blade.php**
   - Muestra la lista de integrantes de un hogar en formato de tabla.
   - Incluye botones para ver los logros de cada integrante.
   - Implementa un modal para mostrar los logros del integrante seleccionado.

2. **logros-integrante.blade.php**
   - Muestra los logros de un integrante organizados por dimensiones.
   - Utiliza un acordeón para mostrar/ocultar las dimensiones.
   - Implementa un sistema de badges con colores para indicar el estado de los logros.

3. **tabla-resultados.blade.php**
   - Muestra los resultados de la búsqueda de hogares.
   - Incluye botones para ver detalles del hogar.

4. **detalle-logro.blade.php**
   - Muestra información detallada de un logro específico.
   - Incluye datos del integrante, estado del logro y observaciones.

## Base de Datos

El sistema interactúa con múltiples esquemas de base de datos:

1. **familiam modulo cif**
   - Contiene información básica de hogares e integrantes.
   - Tablas principales: t1_principalhogar, t1_principalintegrantes.

2. **familiam_bdprotocoloservidor**
   - Contiene información sobre logros, dimensiones y estaciones.
   - Tablas principales: t4_dimensionlogros, t_historicoestacionestadoservidor.

## Procedimientos Almacenados

El sistema utiliza varios procedimientos almacenados para obtener y procesar información:

1. **sp4listarlogros**
   - Obtiene la lista completa de logros disponibles.

2. **sp4totallogros**
   - Obtiene el estado de los logros para un folio y logro específico.

3. **sp4logroresultado**
   - Obtiene información detallada de un logro para un folio específico.

4. **spdatosintegrante**
   - Obtiene información detallada de un integrante específico.

## Sistema de Logros

El sistema de logros es una parte fundamental de la aplicación y se basa en:

### Dimensiones

Los logros están organizados en dimensiones temáticas. Cada dimensión agrupa varios logros relacionados.

### Estados de Logros

Los logros pueden tener diferentes estados, representados por colores:

- **Rojo (0)**: No cumple
- **Verde (1)**: Cumple
- **Gris (2)**: No aplica
- **Azul (3)**: En proceso
- **Café (4)**: Pendiente
- **Blanco (5)**: Recalculo nuevamente

### Seguimiento de Progreso

El sistema mantiene un registro del estado inicial (DI) y el estado final o actual (PF) de cada logro, permitiendo visualizar el progreso de cada integrante.

El sistema se esta diseñando para ser escalable y permitir la adición de nuevas funcionalidades según sea necesario.

---
 
