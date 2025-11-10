<?php
// Inicializamos la variable que identifica la página actual
$currentPage = '';
// Si desde el archivo principal se definió $activePage la usamos para resaltar el menú
if (isset($activePage)) {
    $currentPage = $activePage;
}

// Obtenemos el nivel del usuario logueado para definir qué opciones mostrar
$nivelSesion = !empty($_SESSION['Usuario_Nivel']) ? (int) $_SESSION['Usuario_Nivel'] : 0;
// Los niveles 1 y 2 pueden gestionar transportes
$mostrarTransportes = ($nivelSesion === 1 || $nivelSesion === 2);
// Solo el nivel 1 (administrador) puede gestionar choferes
$mostrarChoferes = ($nivelSesion === 1);
// Los niveles 1 y 2 pueden cargar viajes
$mostrarCargaViajes = ($nivelSesion === 1 || $nivelSesion === 2);

// Creamos un listado de páginas relacionadas con la sección transportes
$transportNavPages = array();
if ($mostrarTransportes) {
    $transportNavPages[] = 'camion_carga';
}
if ($mostrarChoferes) {
    $transportNavPages[] = 'choferes';
}

// Sección de páginas relacionadas con viajes
$viajesNavPages = array('viajes_listado');
if ($mostrarCargaViajes) {
    $viajesNavPages[] = 'viaje_carga';
}

// Determinamos si debemos mostrar expandido el menú de transportes
$transportNavAbierto = in_array($currentPage, $transportNavPages, true);
// Determinamos si debemos mostrar expandido el menú de viajes
$viajesNavAbierto = in_array($currentPage, $viajesNavPages, true);
?>
<!-- Menú lateral con las opciones disponibles para el usuario -->
<aside id="sidebar" class="sidebar">
    <!-- Lista principal de navegación -->
    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Enlace al panel principal -->
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'dashboard' ? '' : 'collapsed'; ?>" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Panel</span>
            </a>
        </li>

        <!-- Sección de transportes visible según el nivel del usuario -->
        <?php if ($mostrarTransportes || $mostrarChoferes): ?>
            <li class="nav-item">
                <!-- Cabecera que abre o cierra el submenú de transportes -->
                <a class="nav-link <?php echo $transportNavAbierto ? '' : 'collapsed'; ?>" data-bs-target="#transportes-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-truck"></i><span>Transportes</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <!-- Submenú de transportes con sus opciones -->
                <ul id="transportes-nav" class="nav-content collapse <?php echo $transportNavAbierto ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                    <?php if ($mostrarTransportes): ?>
                        <li>
                            <!-- Enlace para registrar un nuevo transporte -->
                            <a href="camion_carga.php" class="<?php echo $currentPage === 'camion_carga' ? 'active' : ''; ?>">
                                <i class="bi bi-file-earmark-plus"></i><span>Cargar nuevo transporte</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($mostrarChoferes): ?>
                        <li>
                            <!-- Enlace para registrar un nuevo chofer -->
                            <a href="chofer_carga.php" class="<?php echo $currentPage === 'choferes' ? 'active' : ''; ?>">
                                <i class="bi bi-person-plus"></i><span>Cargar nuevo chofer</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <!-- Sección de viajes disponible para todos los niveles -->
        <li class="nav-item">
            <!-- Cabecera que muestra las opciones vinculadas a los viajes -->
            <a class="nav-link <?php echo $viajesNavAbierto ? '' : 'collapsed'; ?>" data-bs-target="#viajes-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-globe2"></i><span>Viajes</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <!-- Submenú con las acciones relacionadas a los viajes -->
            <ul id="viajes-nav" class="nav-content collapse <?php echo $viajesNavAbierto ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                <?php if ($mostrarCargaViajes): ?>
                    <li>
                        <!-- Enlace para registrar un nuevo viaje -->
                        <a href="viaje_carga.php" class="<?php echo $currentPage === 'viaje_carga' ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-plus"></i><span>Cargar nuevo</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <!-- Enlace para consultar el listado de viajes -->
                    <a href="viajes_listado.php" class="<?php echo $currentPage === 'viajes_listado' ? 'active' : ''; ?>">
                        <i class="bi bi-layout-text-window-reverse"></i><span>Listado de viajes</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>
