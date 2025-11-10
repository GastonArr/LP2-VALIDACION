<?php
// Iniciamos la sesión para poder gestionar datos de autenticación
session_start();

// Incluimos el archivo encargado de generar la conexión a la base de datos
require_once 'funciones/conexion.php';
// Incluimos las funciones reutilizables que encapsulan reglas de negocio
require_once 'funciones/funciones.php';

// Si el usuario ya tiene una sesión activa lo redirigimos directamente al panel principal
if (!empty($_SESSION['Usuario_ID'])) {
    // Usamos la función de ayuda para redirigir al dashboard
    Redireccionar('index.php');
}

// Abrimos la conexión a la base de datos para validar las credenciales
$MiConexion = ConexionBD();

// Establecemos el título de la página de login
$pageTitle = 'Panel de Administración - Login';
// Variable que almacenará mensajes informativos o de error
$Mensaje = '';

// Verificamos si se envió el formulario de ingreso
if (!empty($_POST['BotonLogin'])) {
    // Normalizamos el usuario recibido eliminando espacios y pasando a minúsculas
    $usuario = !empty($_POST['usuario']) ? strtolower(trim($_POST['usuario'])) : '';
    // Tomamos la clave ingresada eliminando espacios extremos
    $clave = !empty($_POST['clave']) ? trim($_POST['clave']) : '';

    // Si faltan datos mostramos un mensaje de aviso
    if ($usuario === '' || $clave === '') {
        $Mensaje = 'Debes ingresar el usuario y la clave.';
    } else {
        // Consultamos en la base de datos si las credenciales son válidas
        $UsuarioLogueado = DatosLogin($usuario, $clave, $MiConexion);

        // Si encontramos un registro procesamos el acceso
        if (!empty($UsuarioLogueado)) {
            // TODO ESTE CHEQUEO REVISA EL ESTADO ACTIVO: SI LA BANDERA ES CERO SE IMPIDE EL ACCESO AUNQUE LAS CREDENCIALES SEAN VALIDAS
            if (isset($UsuarioLogueado['ACTIVO']) && $UsuarioLogueado['ACTIVO'] == 0) {
                $Mensaje = 'Ud. no se encuentra activo en el sistema.';
            } else {
                // Guardamos todos los datos relevantes en la sesión para reutilizarlos
                $_SESSION['Usuario_ID'] = $UsuarioLogueado['ID'];
                $_SESSION['Usuario_Nombre'] = $UsuarioLogueado['NOMBRE'];
                $_SESSION['Usuario_Apellido'] = $UsuarioLogueado['APELLIDO'];
                $_SESSION['Usuario_Usuario'] = $UsuarioLogueado['USUARIO'];
                $_SESSION['Usuario_Nivel'] = $UsuarioLogueado['NIVEL'];
                $_SESSION['Usuario_NombreNivel'] = $UsuarioLogueado['NIVEL_NOMBRE'];
                $_SESSION['Usuario_Img'] = $UsuarioLogueado['IMG'];
                $_SESSION['Usuario_Saludo'] = $UsuarioLogueado['SALUDO'];
                $_SESSION['Usuario_Activo'] = $UsuarioLogueado['ACTIVO'];

                // Redirigimos al usuario al panel una vez autenticado
                Redireccionar('index.php');
            }
        } else {
            // Si las credenciales no son válidas informamos el error
            $Mensaje = 'Datos incorrectos, ingresa nuevamente.';
        }
    }
}

// Incluimos la cabecera HTML común del proyecto
require_once 'includes/header.php';
?>
<!-- Contenido principal del formulario de acceso -->
<main>
    <!-- Contenedor general para centrar el formulario -->
    <div class="container">
        <!-- Sección que ocupa toda la altura de la ventana para centrar verticalmente -->
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <!-- Contenedor adicional para controlar los anchos -->
            <div class="container">
                <!-- Fila centrada en el medio de la página -->
                <div class="row justify-content-center">
                    <!-- Columna que define el ancho máximo del formulario -->
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <!-- Encabezado con el logo y el nombre del panel -->
                        <div class="d-flex justify-content-center py-4">
                            <!-- Enlace que recarga la página actual -->
                            <a href="login.php" class="logo d-flex align-items-center w-auto">
                                <!-- Imagen del logotipo del sistema -->
                                <img src="assets/img/logo.png" alt="">
                                <!-- Nombre del panel visible en pantallas grandes -->
                                <span class="d-none d-lg-block">Panel de Administración</span>
                            </a>
                        </div>
                        <!-- Tarjeta que envuelve el formulario -->
                        <div class="card mb-3 w-100">
                            <!-- Cuerpo de la tarjeta -->
                            <div class="card-body">
                                <!-- Encabezados y descripción del formulario -->
                                <div class="pt-4 pb-2">
                                    <!-- Título centrado invitando a iniciar sesión -->
                                    <h5 class="card-title text-center pb-0 fs-4">Ingresa tu cuenta</h5>
                                    <!-- Texto auxiliar que explica qué hacer -->
                                    <p class="text-center small">Ingresa tus datos de usuario y clave</p>
                                </div>
                                <!-- Bloque condicional para mostrar mensajes -->
                                <?php if (!empty($Mensaje)): ?>
                                    <!-- Alerta amarilla cuando hay un mensaje de error -->
                                    <div class="alert alert-warning" role="alert">
                                        <!-- Icono de advertencia -->
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <!-- Texto del mensaje -->
                                        <?php echo htmlspecialchars($Mensaje); ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Alerta informativa cuando aún no hay errores -->
                                    <div class="alert alert-info" role="alert">
                                        <!-- Icono de información y recordatorio sobre los campos obligatorios -->
                                        <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                                    </div>
                                <?php endif; ?>
                                <!-- Formulario de inicio de sesión -->
                                <form class="row g-3" method="post" action="" novalidate>
                                    <!-- Campo para el nombre de usuario -->
                                    <div class="col-12">
                                        <!-- Etiqueta que identifica el input -->
                                        <label for="usuario" class="form-label">Usuario (*)</label>
                                        <!-- Contenedor que agrega el icono @ antes del input -->
                                        <div class="input-group has-validation">
                                            <!-- Prefijo visual con símbolo de usuario -->
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                            <!-- Input donde se escribe el usuario y persiste el valor ingresado -->
                                            <input type="text" name="usuario" class="form-control" id="usuario" value="<?php echo !empty($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" required>
                                        </div>
                                    </div>
                                    <!-- Campo para la contraseña -->
                                    <div class="col-12">
                                        <!-- Etiqueta para el input de clave -->
                                        <label for="clave" class="form-label">Clave (*)</label>
                                        <!-- Input de tipo password que oculta los caracteres -->
                                        <input type="password" name="clave" class="form-control" id="clave" required>
                                    </div>
                                    <!-- Contenedor para el botón de envío -->
                                    <div class="col-12">
                                        <!-- Botón que envía el formulario al servidor -->
                                        <button class="btn btn-primary w-100" type="submit" name="BotonLogin" value="Login">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Créditos del template base -->
                        <div class="credits">
                            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<?php
// Cargamos el pie de página que incluye los scripts y cierre del HTML
require_once 'includes/footer.php';
?>
