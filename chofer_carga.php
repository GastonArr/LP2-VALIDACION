<?php
// Iniciamos o retomamos la sesión para poder leer datos del usuario autenticado
session_start();

// Cargamos el archivo que crea y devuelve la conexión con la base de datos
require_once 'funciones/conexion.php';
// Cargamos las funciones auxiliares que encapsulan reglas del negocio
require_once 'funciones/funciones.php';

// Si la sesión no tiene un identificador de usuario significa que no inició sesión
if (empty($_SESSION['Usuario_ID'])) {
    // Redirigimos a la página de login para forzar la autenticación
    header('Location: login.php');
    // Interrumpimos el script porque el usuario no tiene acceso
    exit;
}

// Solo los usuarios con nivel 1 pueden ingresar a esta pantalla de registro de choferes
if (empty($_SESSION['Usuario_Nivel']) || (int) $_SESSION['Usuario_Nivel'] !== 1) {
    // En caso contrario los enviamos al panel principal
    header('Location: index.php');
    // Terminamos la ejecución para evitar que se renderice el resto del contenido
    exit;
}

// Obtenemos un objeto de conexión para realizar consultas a la base de datos
$MiConexion = ConexionBD();

// Definimos el título de la pestaña y cabecera
$pageTitle = 'Registrar un nuevo chofer';
// Indicamos cuál opción del menú debe quedar activa
$activePage = 'choferes';

// Inicializamos la variable que mostrará mensajes al usuario
$Mensaje = '';
// Configuramos el estilo visual de la alerta
$Estilo = 'warning';

// Si se hizo clic en el botón Registrar procesamos el formulario
if (!empty($_POST['BotonRegistrar'])) {
    // Validamos los datos enviados y guardamos el texto resultante
    $Mensaje = Validar_Datos_Chofer($MiConexion);
    // Si no hubo mensajes de error continuamos con la inserción
    if (empty($Mensaje)) {
        // Intentamos guardar el chofer y verificamos el resultado de la operación
        if (Insertar_Chofer($MiConexion) != false) {
            // Informamos que la operación se realizó correctamente
            $Mensaje = 'Se ha registrado correctamente.';
            // Limpiamos el arreglo $_POST para resetear el formulario
            $_POST = array();
            // Ajustamos el estilo a "success" para la alerta
            $Estilo = 'success';
        }
    }
}

// Incluimos la cabecera con los recursos comunes del sitio
require_once 'includes/header.php';
// Incluimos la barra de navegación superior
require_once 'includes/topbar.php';
// Incluimos el menú lateral de la aplicación
require_once 'includes/sidebar.php';
?>
<!-- Contenedor principal del área visible -->
<main id="main" class="main">
    <!-- Encabezado con el título y la navegación jerárquica -->
    <div class="pagetitle">
        <!-- Título que explica la acción a realizar -->
        <h1>Registrar un nuevo chofer</h1>
        <!-- Navegación secundaria para ubicar al usuario -->
        <nav>
            <!-- Estructura de breadcrumbs de Bootstrap -->
            <ol class="breadcrumb">
                <!-- Enlace al inicio del sistema -->
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <!-- Nodo intermedio que agrupa funcionalidades de transporte -->
                <li class="breadcrumb-item">Transportes</li>
                <!-- Estado actual dentro de la jerarquía -->
                <li class="breadcrumb-item active">Carga Chofer</li>
            </ol>
        </nav>
    </div>
    <!-- Sección que contiene el formulario -->
    <section class="section">
        <!-- Fila principal del layout -->
        <div class="row">
            <!-- Columna que define el ancho del formulario -->
            <div class="col-lg-6">
                <!-- Tarjeta de Bootstrap que agrupa los controles -->
                <div class="card">
                    <!-- Cuerpo de la tarjeta -->
                    <div class="card-body">
                        <!-- Subtítulo informativo -->
                        <h5 class="card-title">Ingresa los datos</h5>
                        <!-- Mensaje informativo sobre los campos obligatorios -->
                        <div class="alert alert-info" role="alert">
                            <!-- Icono de información y texto aclaratorio -->
                            <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                        </div>
                        <!-- Bloque PHP que muestra el mensaje si existe -->
                        <?php if (!empty($Mensaje)) { ?>
                            <!-- Alerta que cambia de color según el estado -->
                            <div class="alert alert-<?php echo $Estilo; ?>" role="alert">
                                <!-- Texto del mensaje a mostrar -->
                                <?php echo $Mensaje; ?>
                            </div>
                        <?php } ?>
                        <!-- Formulario de alta de chofer -->
                        <form class="row g-3" method="post" action="" novalidate>
                            <!-- Campo para el apellido del chofer -->
                            <div class="col-12">
                                <!-- Etiqueta asociada al input de apellido -->
                                <label for="apellido" class="form-label">Apellido (*)</label>
                                <!-- Input que conserva el valor ingresado previamente -->
                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo !empty($_POST['apellido']) ? $_POST['apellido'] : ''; ?>">
                            </div>
                            <!-- Campo para el nombre del chofer -->
                            <div class="col-12">
                                <!-- Etiqueta para el campo nombre -->
                                <label for="nombre" class="form-label">Nombre (*)</label>
                                <!-- Input que persiste el valor tras la validación -->
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo !empty($_POST['nombre']) ? $_POST['nombre'] : ''; ?>">
                            </div>
                            <!-- Campo para el DNI del chofer -->
                            <div class="col-12">
                                <!-- Etiqueta descriptiva del DNI -->
                                <label for="dni" class="form-label">DNI (*)</label>
                                <!-- Input restringido a texto que mantiene el dato ingresado -->
                                <input type="text" class="form-control" id="dni" name="dni" value="<?php echo !empty($_POST['dni']) ? $_POST['dni'] : ''; ?>">
                            </div>
                            <!-- Campo para el nombre de usuario del chofer -->
                            <div class="col-12">
                                <!-- Etiqueta del campo usuario -->
                                <label for="usuario" class="form-label">Usuario (*)</label>
                                <!-- Input con persistencia del valor de usuario -->
                                <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo !empty($_POST['usuario']) ? $_POST['usuario'] : ''; ?>">
                            </div>
                            <!-- Campo para la contraseña de acceso -->
                            <div class="col-12">
                                <!-- Etiqueta que identifica el campo clave -->
                                <label for="clave" class="form-label">Clave (*)</label>
                                <!-- Input de tipo password para ocultar los caracteres -->
                                <input type="password" class="form-control" id="clave" name="clave">
                            </div>
                            <!-- Zona con las acciones del formulario -->
                            <div class="text-center">
                                <!-- Botón que envía la información al servidor -->
                                <button class="btn btn-primary" type="submit" name="BotonRegistrar" value="Registrar">Registrar</button>
                                <!-- Enlace que limpia los campos recargando la página -->
                                <a href="chofer_carga.php" class="btn btn-secondary">Limpiar Campos</a>
                                <!-- Enlace de retorno al índice general -->
                                <a href="index.php" class="text-primary fw-bold">Volver al index</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Incluimos el pie de página con scripts y cierre del layout
require_once 'includes/footer.php';
?>
