<?php
session_start();

require_once 'funciones/conexion.php';
require_once 'funciones/funciones.php';

if (empty($_SESSION['Usuario_ID'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Panel de Administración';
$activePage = 'dashboard';

$apellidoUsuario = !empty($_SESSION['Usuario_Apellido']) ? $_SESSION['Usuario_Apellido'] : '';
$nombreUsuario = !empty($_SESSION['Usuario_Nombre']) ? $_SESSION['Usuario_Nombre'] : '';

$nombreCompleto = trim($apellidoUsuario . ', ' . $nombreUsuario);
if ($apellidoUsuario === '' || $nombreUsuario === '') {
    $nombreCompleto = trim($apellidoUsuario . ' ' . $nombreUsuario);
}
if ($nombreCompleto === '') {
    $nombreCompleto = 'Usuario';
}

$nivelActual = !empty($_SESSION['Usuario_Nivel']) ? (int) $_SESSION['Usuario_Nivel'] : 0;
$denominacionNivel = !empty($_SESSION['Usuario_NombreNivel']) ? $_SESSION['Usuario_NombreNivel'] : 'Usuario';

$funcionesPermitidas = 'la información disponible en el panel';
switch ($nivelActual) {
    case 1:
        $funcionesPermitidas = 'transportes, choferes y viajes';
        break;
    case 2:
        $funcionesPermitidas = 'transportes y viajes';
        break;
    case 3:
        $funcionesPermitidas = 'el seguimiento de los viajes asignados';
        break;
}

require_once 'includes/header.php';
require_once 'includes/topbar.php';
require_once 'includes/sidebar.php';
?>
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Bienvenido</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Panel</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Hola, <?php echo htmlspecialchars($nombreCompleto); ?> (<?php echo htmlspecialchars($denominacionNivel); ?>)!</h5>
                        <p class="card-text">Desde este panel podrás gestionar la operación diaria del sistema. Según tu función, podrás gestionar: <?php echo htmlspecialchars($funcionesPermitidas); ?>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
require_once 'includes/footer.php';
?>
