<?php
// Iniciamos la sesión para asegurarnos de que el usuario esté autenticado.
session_start();

// Incluimos el archivo con la lógica de conexión a la base de datos.
require_once 'funciones/conexion.php';
// Incluimos las funciones de negocio que utilizaremos para listar viajes.
require_once 'funciones/funciones.php';

// Si no hay un usuario conectado, no se permite acceder al listado.
if (empty($_SESSION['Usuario_ID'])) {
    // Redirigimos a la pantalla de login.
    header('Location: login.php');
    // Terminamos el script para proteger el contenido.
    exit;
}

// Obtenemos un objeto de conexión a la base de datos.
$MiConexion = ConexionBD();

// Definimos el título de la página actual.
$pageTitle = 'Listado de viajes registrados';
// Indicamos qué opción del menú lateral debe mostrarse activa.
$activePage = 'viajes_listado';

// Determinamos el nivel de acceso del usuario logueado como se vio en clase
$nivelActual = 0;
if (!empty($_SESSION['Usuario_Nivel'])) {
    $nivelActual = (int) $_SESSION['Usuario_Nivel'];
}

// En el ejemplo de la profe los choferes (nivel 3) solo veían su información, aplicamos ese filtro
$choferFiltradoId = null;
if ($nivelActual === 3) {
    if (!empty($_SESSION['Usuario_ID'])) {
        $choferFiltradoId = (int) $_SESSION['Usuario_ID'];
    }
}

// Recuperamos los viajes desde la base de datos aplicando el filtro si corresponde
$viajes = array();
$viajes = Listar_Viajes($MiConexion, $choferFiltradoId);
$CantidadViajes = count($viajes);

// Determinamos qué columnas mostrar según el nivel del usuario
$mostrarCostoViaje = in_array($nivelActual, [1, 2], true);
$mostrarColumnaMontoChofer = in_array($nivelActual, [1, 3], true);
$mostrarPorcentajeChofer = ($nivelActual === 1);

$CantidadColumnas = 5
    + ($mostrarCostoViaje ? 1 : 0)
    + ($mostrarColumnaMontoChofer ? 1 : 0);

// Cargamos la cabecera del sitio con estilos y scripts necesarios.
require_once 'includes/header.php';
// Cargamos la barra superior de navegación.
require_once 'includes/topbar.php';
// Cargamos el menú lateral con las opciones disponibles.
require_once 'includes/sidebar.php';
?>
<!-- Contenido principal del listado de viajes -->
<main id="main" class="main">
    <!-- Encabezado con título y breadcrumb -->
    <div class="pagetitle">
        <!-- Título que describe la pantalla -->
        <h1>Lista de viajes registrados</h1>
        <!-- Navegación jerárquica -->
        <nav>
            <!-- Estructura del breadcrumb -->
            <ol class="breadcrumb">
                <!-- Enlace al panel principal -->
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <!-- Paso intermedio que agrupa funcionalidades relacionadas a viajes -->
                <li class="breadcrumb-item">Viajes</li>
                <!-- Estado actual dentro del breadcrumb -->
                <li class="breadcrumb-item active">Listado</li>
            </ol>
        </nav>
    </div>
    <!-- Sección que contiene la tabla -->
    <section class="section">
        <!-- Tarjeta que envuelve la tabla -->
        <div class="card">
            <!-- Cuerpo de la tarjeta -->
            <div class="card-body">
                <!-- Título interno de la tarjeta -->
                <h5 class="card-title">Viajes cargados</h5>
                <!-- Contenedor que permite scroll horizontal en pantallas pequeñas -->
                <div class="table-responsive">
                    <!-- Tabla con estilos de Bootstrap -->
                    <table class="table table-bordered table-striped align-middle">
                        <!-- Encabezado de la tabla -->
                        <thead>
                            <tr>
                                <!-- Columna con el número de fila -->
                                <th>#</th>
                                <!-- Columna con la fecha del viaje -->
                                <th>Fecha viaje</th>
                                <!-- Columna con la descripción del destino -->
                                <th>Destino</th>
                                <!-- Columna que muestra el camión asignado -->
                                <th>Camión</th>
                                <!-- Columna con el nombre del chofer -->
                                <th>Chofer</th>
                                <!-- Columnas económicas según nivel -->
                                <?php if ($mostrarCostoViaje) { ?>
                                    <th>Costo viaje</th>
                                <?php } ?>
                                <?php if ($mostrarColumnaMontoChofer) { ?>
                                    <th>Monto Chofer</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <!-- Cuerpo de la tabla donde se listan los viajes -->
                        <tbody>
                            <!-- Si no hay registros mostramos un mensaje -->
                            <?php if ($CantidadViajes == 0) { ?>
                                <tr>
                                    <td colspan="<?php echo $CantidadColumnas; ?>" class="text-center">No hay viajes registrados.</td>
                                </tr>
                            <?php } else { ?>
                                <?php for ($i = 0; $i < $CantidadViajes; $i++) {
                                    // Inicializamos la fecha formateada como cadena vacía
                                    $FechaFormateada = '';
                                    // Si hay una fecha programada válida intentamos formatearla.
                                    if (!empty($viajes[$i]['fecha_programada'])) {
                                        $Timestamp = strtotime($viajes[$i]['fecha_programada']);
                                        if ($Timestamp != false) {
                                            $FechaFormateada = date('d/m/Y', $Timestamp);
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <!-- Número de fila comenzando en 1 -->
                                        <td><?php echo $i + 1; ?></td>
                                        <!-- Fecha del viaje formateada -->
                                        <td><?php echo $FechaFormateada; ?></td>
                                        <!-- Nombre del destino -->
                                        <td><?php echo $viajes[$i]['destino']; ?></td>
                                        <!-- Descripción del camión: marca, modelo y patente -->
                                        <td><?php echo $viajes[$i]['marca'] . ' - ' . $viajes[$i]['modelo'] . ' - ' . $viajes[$i]['patente']; ?></td>
                                        <!-- Nombre completo del chofer -->
                                        <td><?php echo $viajes[$i]['chofer_apellido'] . ', ' . $viajes[$i]['chofer_nombre']; ?></td>
                                        <!-- Costos y porcentaje según nivel -->
                                        <?php
                                        $MontoChofer = ((float) $viajes[$i]['costo'] * (int) $viajes[$i]['porcentaje_chofer']) / 100;
                                        if ($mostrarCostoViaje) {
                                            ?>
                                            <td>$ <?php echo number_format((float) $viajes[$i]['costo'], 2, ',', '.'); ?></td>
                                        <?php }
                                        if ($mostrarColumnaMontoChofer) {
                                            ?>
                                            <td>
                                                $ <?php echo number_format($MontoChofer, 2, ',', '.'); ?>
                                                <?php if ($mostrarPorcentajeChofer) { ?>
                                                    (<?php echo (int) $viajes[$i]['porcentaje_chofer']; ?>%)
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Incluimos el pie de página con scripts y cierre del layout.
require_once 'includes/footer.php';
?>
