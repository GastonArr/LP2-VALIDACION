<?php
// Guardamos la página recibida desde el archivo que incluye el menú
$currentPage = isset($activePage) ? $activePage : '';

// Recuperamos el nivel almacenado en la sesión y lo comparamos como hacía la profe
$nivelSesion = !empty($_SESSION['Usuario_Nivel']) ? (int) $_SESSION['Usuario_Nivel'] : 0;
// En las clases el nivel 1 representaba al administrador
$esAdministrador = ($nivelSesion === 1);

// En el material de ejemplo se abrían los submenús comparando directamente el nombre de la página
$transportNavAbierto = $esAdministrador && ($currentPage === 'camion_carga' || $currentPage === 'choferes');
$viajesNavAbierto = ($currentPage === 'viaje_carga' || $currentPage === 'viajes_listado');
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

        <!-- Sección de transportes solo disponible para el administrador (nivel 1) -->
        <?php if ($esAdministrador): ?>
            <li class="nav-item">
                <!-- Cabecera que abre o cierra el submenú de transportes -->
                <a class="nav-link <?php echo $transportNavAbierto ? '' : 'collapsed'; ?>" data-bs-target="#transportes-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-truck"></i><span>Transportes</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <!-- Submenú de transportes con sus opciones -->
                <ul id="transportes-nav" class="nav-content collapse <?php echo $transportNavAbierto ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <!-- Enlace para registrar un nuevo transporte, disponible solo para el administrador -->
                        <a href="camion_carga.php" class="<?php echo $currentPage === 'camion_carga' ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-plus"></i><span>Cargar nuevo transporte</span>
                        </a>
                    </li>
                    <li>
                        <!-- Enlace para registrar un nuevo chofer tal como se mostró en clase -->
                        <a href="chofer_carga.php" class="<?php echo $currentPage === 'choferes' ? 'active' : ''; ?>">
                            <i class="bi bi-person-plus"></i><span>Cargar nuevo chofer</span>
                        </a>
                    </li>
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
                <?php if ($esAdministrador): ?>
                    <li>
                        <!-- Enlace para registrar un nuevo viaje, reservado al administrador como en el ejemplo -->
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
