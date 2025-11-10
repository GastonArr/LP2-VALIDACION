<?php
// Iniciamos o retomamos la sesión para acceder a los datos del usuario conectado
session_start();

// Incluimos el archivo que prepara la conexión a la base de datos
require_once 'funciones/conexion.php';
// Incluimos las funciones comunes que se utilizan en todo el sistema
require_once 'funciones/funciones.php';

// Validamos que exista un usuario logueado; de lo contrario, no se permite el acceso al panel
if (empty($_SESSION['Usuario_Nombre'])) {
    // Enviamos al usuario a la pantalla de inicio de sesión
    header('Location: login.php');
    // Terminamos el script porque el usuario no está autenticado
    exit;
}

// Definimos el título que se mostrará en la pestaña y cabecera
$pageTitle = 'Panel de Administración';
// Indicamos que la sección activa del menú es el dashboard
$activePage = 'dashboard';

$nombreUsuario = $_SESSION['Usuario_Nombre'];
$apellidoUsuario = $_SESSION['Usuario_Apellido'];
$nivelNombre = $_SESSION['Usuario_NombreNivel'];
$saludo = $_SESSION['Usuario_Saludo'];

// Incorporamos la cabecera común del sitio
require_once 'includes/header.php';
// Incorporamos la barra superior con accesos rápidos
require_once 'includes/topbar.php';
// Incorporamos el menú lateral de navegación
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
                        <!-- Saludo que replica la estructura utilizada en las clases -->
                        <h5 class="card-title">Panel - <?php echo $saludo; ?> <?php echo $nombreUsuario; ?></h5>
                        <!-- Información del usuario tal como se mostró en los ejemplos -->
                        <p class="card-text">Usuario: <?php echo $nombreUsuario . ' ' . $apellidoUsuario; ?> - Nivel: <?php echo strtoupper($nivelNombre); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Incluimos el pie de página con los scripts compartidos
require_once 'includes/footer.php';
?>
