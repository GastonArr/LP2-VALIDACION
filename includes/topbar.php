<?php
$imagenPerfil = 'profile-img.jpg';
if (!empty($_SESSION['Usuario_Img'])) {
    $imagenPerfil = $_SESSION['Usuario_Img'];
}

$apellidoSesion = !empty($_SESSION['Usuario_Apellido']) ? $_SESSION['Usuario_Apellido'] : '';
$nombreSesion = !empty($_SESSION['Usuario_Nombre']) ? $_SESSION['Usuario_Nombre'] : '';

$nombreCompletoSesion = trim($apellidoSesion . ', ' . $nombreSesion);
if ($apellidoSesion === '' || $nombreSesion === '') {
    $nombreCompletoSesion = trim($apellidoSesion . ' ' . $nombreSesion);
}
if ($nombreCompletoSesion === '') {
    $nombreCompletoSesion = 'Usuario';
}

$nivelNombreSesion = !empty($_SESSION['Usuario_NombreNivel']) ? $_SESSION['Usuario_NombreNivel'] : 'Usuario';
?>
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
            <img src="assets/img/logo.png" alt="">
            <span class="d-none d-lg-block">NiceAdmin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="assets/img/<?php echo htmlspecialchars($imagenPerfil); ?>" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($nombreCompletoSesion); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?php echo htmlspecialchars($nombreCompletoSesion); ?></h6>
                        <span><?php echo htmlspecialchars($nivelNombreSesion); ?></span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-person"></i>
                            <span>Mi perfil</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-gear"></i>
                            <span>Configuraciones</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Cerrar sesión</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
