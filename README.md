# Gestión de Facultades y Docentes — PHP MVC + ORM

Práctica calificada de laboratorio (UD2 – Desempeño 2), Desarrollo de Aplicaciones Web II – UNTRM.

## Instalación
1. Copiar la carpeta en `C:\xampp\htdocs\proyecto-mvc-base`.
2. Ejecutar `database/evaluacion_daw2.sql` en phpMyAdmin (crea la BD, las tablas, la relación y los datos de prueba).
3. Revisar `BASE_URL` (Apache en el puerto 8080) y los datos de conexión (MySQL en el puerto 4404) en `Config/Config.php`.
4. Abrir `http://localhost:8080/proyecto-mvc-base/`.

## Arquitectura
| Capa | Archivos |
| --- | --- |
| Front Controller y rutas | `.htaccess`, `index.php`, `Libraries/Core/Load.php` |
| ORM (PDO + sentencias preparadas) | `Libraries/Core/Model.php`, `Libraries/Core/Conexion.php` |
| Modelos | `Models/FacultadesModel.php`, `Models/DocentesModel.php` (JOIN permanente `docente.id_facultad → facultad.id`) |
| Controladores | `Controllers/FacultadesController.php`, `Controllers/DocentesController.php` |
| Vistas | `Views/Facultades/`, `Views/Docentes/`, `Views/Template/` |

Las vistas no contienen SQL: todas las operaciones pasan por controlador → modelo/ORM.

## Rutas
| Ruta | Método | Requerimiento |
| --- | --- | --- |
| `/facultades` | GET | REQ-01 Listar facultades activas |
| `/facultades/crear` → `/facultades/guardar` | GET → POST | REQ-02 Registrar facultad |
| `/facultades/editar/{id}` → `/facultades/actualizar/{id}` | GET → POST | REQ-03 Editar nombre y abreviatura |
| `/facultades/eliminar/{id}` | POST | REQ-04 Eliminación lógica (estado 1 → 0) |
| `/docentes` | GET | REQ-05 / REQ-06 Docentes activos con su facultad |
| `/docentes/crear` → `/docentes/guardar` | GET → POST | REQ-07 a REQ-10 Registrar docente |
| `/docentes/editar/{id}` → `/docentes/actualizar/{id}` | GET → POST | REQ-11 Editar docente |
| `/docentes/eliminar/{id}` | POST | REQ-12 Eliminación lógica (estado 1 → 0) |

## Validaciones
- Facultad: nombre de 3 a 120 caracteres; abreviatura de 2 a 20 letras.
- Docente: facultad activa elegida desde un `<select>`; DNI de 8 dígitos y no repetido entre docentes activos; apellidos y nombres de 2 a 100 caracteres; correo válido.
- Categoría ∈ {AUXILIAR, ASOCIADO, PRINCIPAL} y dedicación ∈ {DEDICACION EXCLUSIVA-DE, TIEMPO COMPLETO-TC, TIEMPO PARCIAL-TP}, validadas en el servidor con lista blanca.

## Seguridad
- Sentencias preparadas en todo el ORM (inyección SQL).
- `htmlspecialchars()` en toda salida (XSS).
- Registrar, editar y eliminar solo por POST con token CSRF (`hash_equals`).
- Patrón PRG: `header(Location)` + `exit` después de cada escritura.
