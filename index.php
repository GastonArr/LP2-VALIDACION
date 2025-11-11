<?php
// Iniciamos o retomamos la sesión para acceder a los datos del usuario conectado.
session_start();

// Incluimos el archivo que prepara la conexión a la base de datos.
require_once 'funciones/conexion.php';
// Incluimos las funciones comunes que se utilizan en todo el sistema.
require_once 'funciones/funciones.php';

// Validamos que exista un usuario logueado; de lo contrario, no se permite el acceso al panel.
if (empty($_SESSION['Usuario_ID'])) {
    // Enviamos al usuario a la pantalla de inicio de sesión.
    header('Location: login.php');
    // Terminamos el script porque el usuario no está autenticado.
    exit;
}

// Definimos el título que se mostrará en la pestaña y cabecera.
$pageTitle = 'Panel de Administración';
// Indicamos que la sección activa del menú es el dashboard para resaltar la opción.
$activePage = 'dashboard';

// Recuperamos el apellido del usuario si está disponible en la sesión
$apellidoUsuario = '';
if (!empty($_SESSION['Usuario_Apellido'])) {
    $apellidoUsuario = $_SESSION['Usuario_Apellido'];
}
// Recuperamos el nombre del usuario almacenado en la sesión
$nombreUsuario = !empty($_SESSION['Usuario_Nombre']) ? $_SESSION['Usuario_Nombre'] : '';

// Construimos el nombre completo concatenando apellido y nombre según estén disponibles.
if ($apellidoUsuario !== '' && $nombreUsuario !== '') {
    $nombreCompleto = $apellidoUsuario . ', ' . $nombreUsuario;
} elseif ($apellidoUsuario !== '') {
    $nombreCompleto = $apellidoUsuario;
} elseif ($nombreUsuario !== '') {
    $nombreCompleto = $nombreUsuario;
} else {
    $nombreCompleto = 'Usuario';
}

// Guardamos el nivel del usuario para personalizar el mensaje de bienvenida.
$nivelActual = !empty($_SESSION['Usuario_Nivel']) ? (int) $_SESSION['Usuario_Nivel'] : 0;
// También obtenemos la descripción textual del nivel para mostrarla.
$denominacionNivel = !empty($_SESSION['Usuario_NombreNivel']) ? $_SESSION['Usuario_NombreNivel'] : 'Usuario';

// Definimos un texto inicial con las funcionalidades disponibles.
$funcionesPermitidas = 'la información disponible en el panel';
// Según el nivel del usuario personalizamos la lista de tareas habilitadas.
switch ($nivelActual) {
    case 1:
        // Nivel 1 (por ejemplo administrador) puede gestionar todos los recursos del sistema.
        $funcionesPermitidas = 'transportes, choferes y viajes';
        break;
    case 2:
        // Nivel 2 puede manejar transportes y viajes.
        $funcionesPermitidas = 'transportes y viajes';
        break;
    case 3:
        // Nivel 3 tiene acceso limitado al seguimiento de viajes asignados.
        $funcionesPermitidas = 'el seguimiento de los viajes asignados';
        break;
}

// Incorporamos la cabecera común del sitio.
require_once 'includes/header.php';
// Incorporamos la barra superior con accesos rápidos.
require_once 'includes/topbar.php';
// Incorporamos el menú lateral de navegación.
require_once 'includes/sidebar.php';
?>
<!-- Área principal del contenido -->
<main id="main" class="main">
    <!-- Encabezado de la página con título y breadcrumb -->
    <div class="pagetitle">
        <!-- Título de bienvenida -->
        <h1>Bienvenido</h1>
        <!-- Navegación jerárquica para indicar la ubicación dentro del sitio -->
        <nav>
            <!-- Estructura de migas de pan -->
            <ol class="breadcrumb">
                <!-- Enlace a la página principal -->
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <!-- Elemento activo que indica el panel actual -->
                <li class="breadcrumb-item active">Panel</li>
            </ol>
        </nav>
    </div>
    <!-- Sección principal del dashboard -->
    <section class="section dashboard">
        <!-- Fila que ocupa todo el ancho -->
        <div class="row">
            <!-- Columna que contiene la tarjeta de bienvenida -->
            <div class="col-lg-12">
                <!-- Tarjeta informativa -->
                <div class="card">
                    <!-- Cuerpo de la tarjeta -->
                    <div class="card-body">
                        <!-- Saludo personalizado que incluye el nombre y el nivel del usuario -->
                        <h5 class="card-title">Hola, <?php echo $nombreCompleto; ?> (<?php echo $denominacionNivel; ?>)!</h5>
                        <!-- Párrafo que explica las funcionalidades disponibles según el nivel -->
                        <p class="card-text">Desde este panel podrás gestionar la operación diaria del sistema. Según tu función, podrás gestionar: <?php echo $funcionesPermitidas; ?>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Incluimos el pie de página con los scripts compartidos.
require_once 'includes/footer.php';
?>
