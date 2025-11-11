<?php
// Inicializamos la variable que guarda la página activa para marcar la navegación.
$paginaActual = '';
if (!empty($activePage)) {
    // Si el controlador define una página activa la guardamos en esta variable.
    $paginaActual = $activePage;
}

// Guardamos el nivel del usuario autenticado para decidir qué opciones mostrar.
$nivelSesion = 0;
if (!empty($_SESSION['Usuario_Nivel'])) {
    $nivelSesion = $_SESSION['Usuario_Nivel'];
}

// Determinamos si el usuario es administrador (nivel 1) para habilitar secciones exclusivas.
$esAdministrador = ($nivelSesion == 1);

// Habilitamos las opciones de carga de transportes y viajes tanto para administradores (nivel 1) como operadores (nivel 2).
$puedeCargarTransportesYViajes = ($nivelSesion > 0 && $nivelSesion <= 2);
?>
<!-- Menú lateral con las opciones disponibles para el usuario -->
<aside id="sidebar" class="sidebar">
    <!-- Lista principal de navegación -->
    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Enlace al panel principal -->
        <li class="nav-item">
            <a class="nav-link<?php if ($paginaActual != 'dashboard') { echo ' collapsed'; } ?>" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Panel</span>
            </a>
        </li>

        <!-- Sección de transportes disponible para administradores y operadores -->
        <?php if ($puedeCargarTransportesYViajes) { ?>
            <li class="nav-item">
                <!-- Cabecera que abre o cierra el submenú de transportes -->
                <a class="nav-link<?php if ($paginaActual != 'camion_carga' && $paginaActual != 'choferes') { echo ' collapsed'; } ?>" data-bs-target="#transportes-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-truck"></i><span>Transportes</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <!-- Submenú de transportes con sus opciones -->
                <ul id="transportes-nav" class="nav-content collapse<?php if ($paginaActual == 'camion_carga' || $paginaActual == 'choferes') { echo ' show'; } ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <!-- Enlace para registrar un nuevo transporte, habilitado para administradores y operadores -->
                        <a href="camion_carga.php"<?php if ($paginaActual == 'camion_carga') { echo ' class="active"'; } ?>>
                            <i class="bi bi-file-earmark-plus"></i><span>Cargar nuevo transporte</span>
                        </a>
                    </li>
                    <?php if ($esAdministrador) { ?>
                        <li>
                            <!-- Enlace para registrar un nuevo chofer tal como se mostró en clase -->
                            <a href="chofer_carga.php"<?php if ($paginaActual == 'choferes') { echo ' class="active"'; } ?>>
                                <i class="bi bi-person-plus"></i><span>Cargar nuevo chofer</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <!-- Sección de viajes disponible para todos los niveles -->
        <li class="nav-item">
            <!-- Cabecera que muestra las opciones vinculadas a los viajes -->
            <a class="nav-link<?php if ($paginaActual != 'viaje_carga' && $paginaActual != 'viajes_listado') { echo ' collapsed'; } ?>" data-bs-target="#viajes-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-globe2"></i><span>Viajes</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <!-- Submenú con las acciones relacionadas a los viajes -->
            <ul id="viajes-nav" class="nav-content collapse<?php if ($paginaActual == 'viaje_carga' || $paginaActual == 'viajes_listado') { echo ' show'; } ?>" data-bs-parent="#sidebar-nav">
                <?php if ($puedeCargarTransportesYViajes) { ?>
                    <li>
                        <!-- Enlace para registrar un nuevo viaje, habilitado para administradores y operadores -->
                        <a href="viaje_carga.php"<?php if ($paginaActual == 'viaje_carga') { echo ' class="active"'; } ?>>
                            <i class="bi bi-file-earmark-plus"></i><span>Cargar nuevo</span>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <!-- Enlace para consultar el listado de viajes -->
                    <a href="viajes_listado.php"<?php if ($paginaActual == 'viajes_listado') { echo ' class="active"'; } ?>>
                        <i class="bi bi-layout-text-window-reverse"></i><span>Listado de viajes</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>
