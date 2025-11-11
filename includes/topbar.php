<?php
// Definimos la imagen de perfil por defecto que usa el layout cuando no hay foto personalizada.
$imagenPerfil = 'profile-img.jpg';
// Si en la sesión hay una imagen personalizada la utilizamos para que el usuario se reconozca fácilmente.
if (!empty($_SESSION['Usuario_Img'])) {
    $imagenPerfil = $_SESSION['Usuario_Img'];
}

// Recuperamos el apellido del usuario autenticado para mostrarlo en el menú superior.
$apellidoSesion = !empty($_SESSION['Usuario_Apellido']) ? $_SESSION['Usuario_Apellido'] : '';
// Recuperamos el nombre del usuario autenticado.
$nombreSesion = !empty($_SESSION['Usuario_Nombre']) ? $_SESSION['Usuario_Nombre'] : '';

// Construimos el nombre completo mezclando apellido y nombre según estén cargados.
if ($apellidoSesion !== '' && $nombreSesion !== '') {
    $nombreCompletoSesion = $apellidoSesion . ', ' . $nombreSesion;
} elseif ($apellidoSesion !== '') {
    $nombreCompletoSesion = $apellidoSesion;
} elseif ($nombreSesion !== '') {
    $nombreCompletoSesion = $nombreSesion;
} else {
    $nombreCompletoSesion = 'Usuario';
}

// Obtenemos la denominación del nivel del usuario para mostrarlo en el menú desplegable.
$nivelNombreSesion = !empty($_SESSION['Usuario_NombreNivel']) ? $_SESSION['Usuario_NombreNivel'] : 'Usuario';
?>
<!-- Barra superior fija con accesos rápidos y perfil del usuario -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <!-- Sección izquierda con logo y botón para abrir el menú lateral -->
    <div class="d-flex align-items-center justify-content-between">
        <!-- Enlace que lleva al panel principal -->
        <a href="index.php" class="logo d-flex align-items-center">
            <!-- Imagen del logotipo -->
            <img src="assets/img/logo.png" alt="">
            <!-- Texto visible en pantallas grandes -->
            <span class="d-none d-lg-block">NiceAdmin</span>
        </a>
        <!-- Botón que permite contraer o expandir el menú lateral -->
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <!-- Navegación de la barra superior alineada a la derecha -->
    <nav class="header-nav ms-auto">
        <!-- Lista de elementos de navegación -->
        <ul class="d-flex align-items-center">
            <!-- Elemento con el menú desplegable del perfil -->
            <li class="nav-item dropdown pe-3">
                <!-- Enlace que muestra la foto y el nombre del usuario -->
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <!-- Imagen circular del usuario -->
                    <img src="assets/img/<?php echo $imagenPerfil; ?>" alt="Profile" class="rounded-circle">
                    <!-- Nombre del usuario visible a partir de pantallas medianas -->
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $nombreCompletoSesion; ?></span>
                </a>
                <!-- Menú desplegable con opciones relacionadas al perfil -->
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <!-- Encabezado del menú con el nombre y el rol -->
                    <li class="dropdown-header">
                        <h6><?php echo $nombreCompletoSesion; ?></h6>
                        <span><?php echo $nivelNombreSesion; ?></span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <!-- Opción de perfil (pendiente de implementación) -->
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-person"></i>
                            <span>Mi perfil</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <!-- Opción de configuraciones generales -->
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-gear"></i>
                            <span>Configuraciones</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <!-- Enlace que cierra la sesión utilizando logout.php -->
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
