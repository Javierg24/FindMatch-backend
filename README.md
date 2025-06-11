# FindMatch

Este repositorio contiene el código backend del proyecto FindMatch, una plataforma web desarrollada con PHP y MySQL que permite a los usuarios crear, unirse y gestionar partidos deportivos entre equipos en centros deportivos.

# Estructura del Proyecto

FindMatch-backend/
│
├── PHP/
│   ├── config/          # Configuraciones generales del proyecto
│   ├── connection/      # Archivo de conexión a la base de datos (MySQL)
│   ├── controller/      # Controladores que gestionan la lógica de negocio (Match, Team, User, etc.)
│   └── model/           # Modelos que representan las entidades del sistema
│
├── sql/
│   ├── SCRIPT.sql             # Script de creación de base de datos y tablas
│   └── inserts_findmatch.sql # Datos iniciales (insert para pruebas)


## 🛠️ Tecnologías Usadas

- **PHP (versión 7.4 o superior)**
- **MySQL** (recomendado usar MySQL Workbench para administración)
- **JSON** para la comunicación entre frontend y backend
- **REST-like** estructura de endpoints
- **Apache/Nginx** como servidor web (XAMPP, MAMP, etc. para desarrollo local)

## 📦 Instalación y Ejecución

1. **Clonar el repositorio:**

```bash
git clone https://github.com/tu-usuario/findmatch-backend.git

2. **Importar la base de datos:**

Abre sql/SCRIPT.sql en MySQL Workbench o phpMyAdmin para crear la base de datos y tablas.

Ejecuta sql/inserts_findmatch.sql para insertar datos de prueba.

2. **Configurar la conexión a la base de datos: **
Ve a PHP/connection/Database.php (o similar) y configura los datos de conexión:

$host = 'localhost';
$dbname = 'findmatch';
$user = 'root';
$password = '';
