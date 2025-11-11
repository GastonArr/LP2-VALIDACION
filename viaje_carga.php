<?php
// Iniciamos la sesión para poder validar al usuario antes de mostrar el contenido.
session_start();

// Agregamos el archivo con la lógica de conexión a la base de datos.
require_once 'funciones/conexion.php';
// Agregamos las funciones auxiliares que utilizaremos en este formulario.
require_once 'funciones/funciones.php';

// Si no hay un usuario autenticado, redirigimos al formulario de login.
if (empty($_SESSION['Usuario_ID'])) {
    // Redirigimos utilizando cabeceras HTTP al formulario de autenticación.
    header('Location: login.php');
    // Finalizamos la ejecución del script para proteger la página.
    exit;
}

// Bloqueamos a quienes tengan un nivel superior al operador (nivel 2) para permitir la carga a administradores y operadores.
if (empty($_SESSION['Usuario_Nivel']) || (int) $_SESSION['Usuario_Nivel'] > 2) {
    // Redirigimos al listado de viajes para quienes no tienen permiso de carga.
    header('Location: viajes_listado.php');
    // Detenemos la ejecución para evitar que puedan cargar viajes.
    exit;
}

// Obtenemos la conexión a la base de datos para ejecutar consultas.
$MiConexion = ConexionBD();

// Definimos el título de la página para la cabecera y la pestaña.
$pageTitle = 'Registrar un nuevo viaje';
// Indicamos qué elemento del menú lateral debe aparecer activo.
$activePage = 'viaje_carga';

// Recuperamos los datos necesarios para completar el formulario.
$choferes = Listar_Choferes($MiConexion); // Devuelve los choferes activos.
$transportes = Listar_Transportes($MiConexion); // Devuelve transportes disponibles.
$destinos = Listar_Destinos($MiConexion); // Devuelve destinos posibles.

// Inicializamos el mensaje a mostrar al usuario.
$Mensaje = '';
// Configuramos el estilo de la alerta que se mostrará en pantalla.
$Estilo = 'warning';

// Si el formulario fue enviado procedemos a validarlo y guardarlo.
if (!empty($_POST['BotonRegistrar'])) {
    // Validamos la información recibida desde el formulario.
    $Mensaje = Validar_Datos_Viaje($MiConexion);
    // Solo continuamos si no hubo errores.
    if (empty($Mensaje)) {
        // Intentamos insertar el viaje en la base de datos.
        if (Insertar_Viaje($MiConexion) != false) {
            // Indicamos al usuario que la operación fue exitosa.
            $Mensaje = 'Se ha registrado correctamente.';
            // Vaciamos los datos enviados para limpiar el formulario.
            $_POST = array();
            // Cambiamos el estilo de la alerta a éxito.
            $Estilo = 'success';
        }
    }
}

// Calculamos cuántas opciones hay para cada listado desplegable.
$CantidadChoferes = count($choferes);
$CantidadTransportes = count($transportes);
$CantidadDestinos = count($destinos);

// Incluimos la cabecera HTML con estilos y scripts comunes.
require_once 'includes/header.php';
// Incluimos la barra superior de navegación.
require_once 'includes/topbar.php';
// Incluimos el menú lateral.
require_once 'includes/sidebar.php';
?>
<!-- Contenedor principal del formulario de registro de viajes -->
<main id="main" class="main">
    <!-- Encabezado con título y navegación -->
    <div class="pagetitle">
        <!-- Título de la sección actual -->
        <h1>Registrar un nuevo viaje</h1>
        <!-- Breadcrumb para indicar la ubicación del usuario -->
        <nav>
            <!-- Lista ordenada que representa el recorrido de navegación -->
            <ol class="breadcrumb">
                <!-- Enlace que lleva al inicio -->
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <!-- Paso intermedio que agrupa las opciones relacionadas con viajes -->
                <li class="breadcrumb-item">Viajes</li>
                <!-- Elemento activo que describe la pantalla actual -->
                <li class="breadcrumb-item active">Carga</li>
            </ol>
        </nav>
    </div>
    <!-- Sección principal con el formulario -->
    <section class="section">
        <!-- Fila del layout para centrar el formulario -->
        <div class="row">
            <!-- Columna que define el ancho en pantallas grandes -->
            <div class="col-lg-6">
                <!-- Tarjeta de Bootstrap que contiene el formulario -->
                <div class="card">
                    <!-- Cuerpo de la tarjeta -->
                    <div class="card-body">
                        <!-- Subtítulo descriptivo -->
                        <h5 class="card-title">Ingresa los datos</h5>
                        <!-- Sección condicional para mostrar mensajes de estado -->
                        <?php if (!empty($Mensaje)): ?>
                            <!-- Alerta cuyo estilo depende del resultado del proceso -->
                            <div class="alert alert-<?php echo $Estilo; ?>" role="alert">
                                <!-- Mensaje de error o éxito -->
                                <?php echo $Mensaje; ?>
                            </div>
                        <?php else: ?>
                            <!-- Alerta informativa con instrucciones generales -->
                            <div class="alert alert-info" role="alert">
                                <!-- Icono de información y recordatorio de campos obligatorios -->
                                <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                            </div>
                        <?php endif; ?>
                        <!-- Formulario para crear un nuevo viaje -->
                        <form class="row g-3" method="post" action="" novalidate>
                            <!-- Selector de chofer -->
                            <div class="col-12">
                                <!-- Etiqueta para el campo del chofer -->
                                <label for="chofer_id" class="form-label">Chofer (*)</label>
                                <!-- Lista desplegable con todos los choferes habilitados -->
                                <select class="form-select" id="chofer_id" name="chofer_id">
                                    <!-- Opción vacía para obligar a seleccionar un chofer -->
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    // Guardamos la opción seleccionada anteriormente por el usuario.
                                    $ChoferSeleccionado = !empty($_POST['chofer_id']) ? $_POST['chofer_id'] : '';
                                    // Recorremos cada chofer disponible para crear una opción en el selector.
                                    for ($i = 0; $i < $CantidadChoferes; $i++) {
                                        // Determinamos si este chofer debe quedar marcado como seleccionado.
                                        $Seleccionado = (!empty($ChoferSeleccionado) && $ChoferSeleccionado == $choferes[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <!-- Opción con apellido, nombre y DNI del chofer -->
                                        <option value="<?php echo $choferes[$i]['id']; ?>" <?php echo $Seleccionado; ?>>
                                            <?php echo $choferes[$i]['apellido'] . ', ' . $choferes[$i]['nombre'] . ' - DNI ' . $choferes[$i]['dni']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Selector de transporte -->
                            <div class="col-12">
                                <!-- Etiqueta para el campo del transporte -->
                                <label for="transporte_id" class="form-label">Transporte (*)</label>
                                <!-- Lista desplegable con los transportes disponibles -->
                                <select class="form-select" id="transporte_id" name="transporte_id">
                                    <!-- Opción vacía inicial -->
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    // Guardamos el transporte elegido anteriormente.
                                    $TransporteSeleccionado = !empty($_POST['transporte_id']) ? $_POST['transporte_id'] : '';
                                    // Iteramos la lista de transportes habilitados.
                                    for ($i = 0; $i < $CantidadTransportes; $i++) {
                                        // Definimos si la opción actual debe aparecer seleccionada.
                                        $Seleccionado = (!empty($TransporteSeleccionado) && $TransporteSeleccionado == $transportes[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <!-- Opción con marca, modelo y patente -->
                                        <option value="<?php echo $transportes[$i]['id']; ?>" <?php echo $Seleccionado; ?>>
                                            <?php echo $transportes[$i]['marca'] . ' - ' . $transportes[$i]['modelo'] . ' - ' . $transportes[$i]['patente']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Campo para la fecha programada del viaje -->
                            <div class="col-12">
                                <!-- Etiqueta de la fecha -->
                                <label for="fecha_programada" class="form-label">Fecha programada (*)</label>
                                <!-- Input de texto que conserva el valor ingresado -->
                                <input type="text" class="form-control" id="fecha_programada" name="fecha_programada" placeholder="dd/mm/aaaa" value="<?php echo !empty($_POST['fecha_programada']) ? $_POST['fecha_programada'] : ''; ?>">
                            </div>
                            <!-- Selector del destino del viaje -->
                            <div class="col-12">
                                <!-- Etiqueta del campo destino -->
                                <label for="destino_id" class="form-label">Destino (*)</label>
                                <!-- Lista desplegable con las opciones de destino -->
                                <select class="form-select" id="destino_id" name="destino_id">
                                    <!-- Opción predeterminada -->
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    // Guardamos la selección previa para mostrarla si hubo un error.
                                    $DestinoSeleccionado = !empty($_POST['destino_id']) ? $_POST['destino_id'] : '';
                                    // Recorremos todos los destinos obtenidos de la base de datos.
                                    for ($i = 0; $i < $CantidadDestinos; $i++) {
                                        // Determinamos si este destino debe mostrarse como seleccionado.
                                        $Seleccionado = (!empty($DestinoSeleccionado) && $DestinoSeleccionado == $destinos[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <!-- Opción que muestra el nombre del destino -->
                                        <option value="<?php echo $destinos[$i]['id']; ?>" <?php echo $Seleccionado; ?>><?php echo $destinos[$i]['denominacion']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Campo para el costo total del viaje -->
                            <div class="col-6">
                                <!-- Etiqueta que describe el costo -->
                                <label for="costo" class="form-label">Costo (*)</label>
                                <!-- Input que almacena el costo y conserva el valor ingresado -->
                                <input type="text" class="form-control" id="costo" name="costo" value="<?php echo !empty($_POST['costo']) ? $_POST['costo'] : ''; ?>">
                            </div>
                            <!-- Campo para el porcentaje asignado al chofer -->
                            <div class="col-6">
                                <!-- Etiqueta del porcentaje -->
                                <label for="porcentaje_chofer" class="form-label">Porcentaje chofer (*)</label>
                                <!-- Input numérico limitado entre 0 y 100 -->
                                <input type="number" class="form-control" id="porcentaje_chofer" name="porcentaje_chofer" min="0" max="100" value="<?php echo !empty($_POST['porcentaje_chofer']) ? $_POST['porcentaje_chofer'] : ''; ?>">
                            </div>
                            <!-- Sección de acciones del formulario -->
                            <div class="text-center">
                                <!-- Botón que envía los datos al servidor -->
                                <button class="btn btn-primary" type="submit" name="BotonRegistrar" value="Registrar">Registrar</button>
                                <!-- Enlace para limpiar el formulario recargando la página -->
                                <a href="viaje_carga.php" class="btn btn-secondary">Limpiar Campos</a>
                                <!-- Enlace para regresar al panel principal -->
                                <a href="index.php" class="text-primary fw-bold">Volver al index</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Incluimos el pie de página compartido del sitio.
require_once 'includes/footer.php';
?>
