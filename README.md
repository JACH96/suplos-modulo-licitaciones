# 📦 Módulo de Gestión de Licitaciones y Ofertas Comerciales

Esta es mi solución para la prueba técnica de **Desarrollador FullStack PHP Junior**.

## 🛠️ Tecnologías que usé

- **Backend:** PHP nativo sin usar ningún framework (versión **8.2.12**), organizado con la estructura **MVC** (Modelos, Vistas y Controladores).
- **Base de Datos / ORM:** Usé **Eloquent de forma independiente** (`illuminate/database` versión **8.83.27**) para manejar las tablas y hacer consultas seguras a MySQL sin escribir SQL plano.
- **Frontend:** **Vue.js** (versión **2.6.14**, conectado por CDN) para que la interfaz sea reactiva, junto con **Axios** (versión **1.18.1**, conectado por CDN) para conectar las vistas con la API de PHP.
- **Diseño:** **Bootstrap** (versión **5.3.8**, conectado por CDN) para que se vea limpio, ordenado y funcione bien en cualquier pantalla.
- **Variables de Entorno:** Usé `vlucas/phpdotenv` (versión **5.6.3**) para configurar las contraseñas de la base de datos de forma segura.
- **Excel:** Usé la librería `PhpSpreadsheet` (versión **5.8.0**) para armar el reporte de descarga de ofertas.

---

## 📂 Estructura de Carpetas

```text
suplos-modulo-licitaciones/
├── bd/                      # Carpeta con el script de la base de datos
│   └── estructura.sql       # Script completo con las tablas y los datos listos
├── config/                  # Configuración de la conexión a la base de datos
│   └── database.php
├── Controllers/         # Lógica para procesar las peticiones
│   ├── ActividadController.php
│   ├── OfertaController.php
│   └── OfertaDocumentoController.php
├── Models/              # Archivos que se conectan a las tablas con Eloquent
│   ├── Actividad.php
│   ├── Oferta.php
│   └── OfertaDocumento.php
├── public/                  # Lo que ve el servidor web
│   ├── index.php            # Vista principal con todo el HTML y Vue.js
│   └── uploads/             # Carpeta donde se guardan los archivos PDF/ZIP
├── .env.example             # Plantilla para la configuración de la BD
└── composer.json            # Dependencias de PHP y configuración de rutas
```

---

## 🗄️ Modelos y Base de Datos (Eloquent)

- **`Actividad`:** Conectado a la tabla de productos de la ONU. Como son datos fijos que no cambian, les apagué los timestamps (`created_at`/`updated_at`) para optimizar espacio.
- **`OfertaDocumento`:** Maneja los archivos adjuntos.
- **`Oferta`:** Es el modelo principal. Tiene relaciones directas con las actividades y los documentos. Creé una función interna (`getEstadoCalculadoAttribute`) para que revise la hora de cierre con la hora actual del servidor; si la fecha ya pasó, el estado cambia solo a **'Cerrada'**, de lo contrario se queda en **'Abierta'**.

---

## 🚀 Cómo instalar y correr el proyecto localmente

### 1. Descargar el código
```bash
git clone https://github.com
cd suplos-modulo-licitaciones
```

### 2. Instalar librerías
Instala las dependencias necesarias de Composer ejecutando en la raíz:
```bash
composer install
```

### 3. Configurar la Base de Datos
Copia el archivo de ejemplo para crear tu configuración real:
```bash
cp .env.example .env
```
Abre el archivo `.env` recién creado con un editor de texto y pon los datos de tu servidor MySQL. Ejemplo para XAMPP con puerto especial (3307):
```env
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=suplos_licitaciones
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Importar las tablas y datos
1. Abre tu phpMyAdmin o gestor de base de datos.
2. Crea una base de datos limpia con el nombre `suplos_licitaciones`.
3. Importa el archivo ubicado en **`bd/estructura.sql`**. 
*(Nota: El archivo ya viene con la tabla de las 49,000 actividades de la ONU cargada y con 11 ofertas comerciales reales para probar la paginación y las búsquedas de inmediato).*

### 5. Prender el servidor
Si usas el comando de desarrollo de PHP, ejecútalo en la raíz apuntando a la carpeta pública:
```bash
php -S localhost:8000 -t public/
```
Entra en tu navegador a `http://localhost:8000` y listo.