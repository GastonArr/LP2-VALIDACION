<?php
// La profe en clase tomaba la página actual desde una variable simple
$currentPage = isset($activePage) ? $activePage : '';

// También comparaba el nivel directamente contra el valor guardado en la sesión
$nivelSesion = isset($_SESSION['Usuario_Nivel']) ? (int) $_SESSION['Usuario_Nivel'] : 0;
?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Panel</span>
            </a>
        </li>

        <?php if ($nivelSesion === 1): ?>
            <!-- Igual que en clase: el administrador (nivel 1) ve las cargas internas -->
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'camion_carga' ? 'active' : ''; ?>" href="camion_carga.php">
                    <i class="bi bi-truck"></i>
                    <span>Cargar transporte</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'choferes' ? 'active' : ''; ?>" href="chofer_carga.php">
                    <i class="bi bi-person-plus"></i>
                    <span>Cargar chofer</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'viaje_carga' ? 'active' : ''; ?>" href="viaje_carga.php">
                    <i class="bi bi-file-earmark-plus"></i>
                    <span>Cargar viaje</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'viajes_listado' ? 'active' : ''; ?>" href="viajes_listado.php">
                <i class="bi bi-layout-text-window-reverse"></i>
                <span>Listado de viajes</span>
            </a>
        </li>
    </ul>
</aside>
