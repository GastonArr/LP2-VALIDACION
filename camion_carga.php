<?php
// Iniciamos la sesión para poder acceder a las variables de sesión en este archivo
session_start();

// Importamos el archivo que crea la conexión a la base de datos
require_once 'funciones/conexion.php';
// Importamos el archivo que contiene funciones auxiliares reutilizables
require_once 'funciones/funciones.php';

// Si no existe un usuario autenticado redirigimos al formulario de inicio de sesión
if (empty($_SESSION['Usuario_ID'])) {
    // Indicamos el destino de la redirección a la página de login
    header('Location: login.php');
    // Terminamos la ejecución del script después de enviar la redirección
    exit;
}

// Siguiendo el ejemplo de la profe, solo el nivel 1 puede cargar datos desde el panel
if (empty($_SESSION['Usuario_Nivel']) || (int) $_SESSION['Usuario_Nivel'] > 1) {
    // Redirigimos al dashboard principal cuando no es administrador
    header('Location: index.php');
    // Interrumpimos la ejecución para evitar que acceda al contenido restringido
    exit;
}

// Guardamos la conexión a la base de datos para reutilizarla más adelante
$MiConexion = ConexionBD();

// Definimos el título de la página que se mostrará en la cabecera
$pageTitle = 'Registrar un nuevo transporte';
// Indicamos qué opción del menú lateral debe aparecer activa
$activePage = 'camion_carga';

// Obtenemos desde la base de datos todas las marcas de vehículos disponibles
$marcas = Listar_Marcas($MiConexion);

// Inicializamos el mensaje para el usuario como cadena vacía
$Mensaje = '';
// Configuramos el estilo del mensaje para usar con las alertas de Bootstrap
$Estilo = 'warning';

// Verificamos si se envió el formulario presionando el botón Registrar
if (!empty($_POST['BotonRegistrar'])) {
    // Ejecutamos la validación de datos y guardamos el resultado para mostrarlo
    $Mensaje = Validar_Datos_Transporte($MiConexion);
    // Si no se generaron mensajes de error procedemos a registrar el transporte
    if (empty($Mensaje)) {
        // Intentamos insertar el nuevo transporte en la base de datos
        if (Insertar_Transporte($MiConexion) != false) {
            // Informamos al usuario que la operación fue exitosa
            $Mensaje = 'Se ha registrado correctamente.';
            // Limpiamos los valores enviados para evitar reenvíos accidentales
            $_POST = array();
            // Cambiamos el estilo del mensaje a éxito
            $Estilo = 'success';
        }
    }
}

// Calculamos cuántas marcas se recuperaron para iterar el listado
$CantidadMarcas = count($marcas);
// Guardamos la marca seleccionada para mantenerla en el formulario al validar
$MarcaSeleccionada = !empty($_POST['marca_id']) ? $_POST['marca_id'] : '';
// Conservamos el modelo escrito anteriormente para no perderlo tras una validación
$ModeloValor = !empty($_POST['modelo']) ? $_POST['modelo'] : '';
// Conservamos el año ingresado por el usuario
$AnioValor = !empty($_POST['anio']) ? $_POST['anio'] : '';
// Conservamos la patente ingresada
$PatenteValor = !empty($_POST['patente']) ? $_POST['patente'] : '';
// Determinamos si el check de disponibilidad debe aparecer marcado
$DisponibleMarcado = !empty($_POST) ? !empty($_POST['disponible']) : true;

// Incluimos la cabecera estándar del sitio
require_once 'includes/header.php';
// Incluimos la barra superior de navegación
require_once 'includes/topbar.php';
// Incluimos el menú lateral de navegación
require_once 'includes/sidebar.php';
?>
<!-- Contenedor principal del contenido -->
<main id="main" class="main">
    <!-- Encabezado de la página con el título y breadcrumb -->
    <div class="pagetitle">
        <!-- Título que describe la acción actual -->
        <h1>Registrar un nuevo transporte</h1>
        <!-- Migas de pan para ubicar al usuario dentro del sitio -->
        <nav>
            <!-- Lista de elementos del breadcrumb -->
            <ol class="breadcrumb">
                <!-- Enlace para regresar al inicio -->
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <!-- Elemento intermedio que identifica la sección general -->
                <li class="breadcrumb-item">Transportes</li>
                <!-- Elemento activo que indica que estamos en la sección de carga -->
                <li class="breadcrumb-item active">Carga</li>
            </ol>
        </nav>
    </div>
    <!-- Sección principal del formulario -->
    <section class="section">
        <!-- Fila principal del diseño responsive -->
        <div class="row">
            <!-- Columna que centrará el formulario en pantallas medianas -->
            <div class="col-lg-6">
                <!-- Tarjeta de Bootstrap que envuelve el formulario -->
                <div class="card">
                    <!-- Cuerpo de la tarjeta que contiene todo el contenido -->
                    <div class="card-body">
                        <!-- Subtítulo que guía al usuario -->
                        <h5 class="card-title">Ingresa los datos</h5>
                        <!-- Alerta informativa con instrucciones para completar el formulario -->
                        <div class="alert alert-info" role="alert">
                            <!-- Icono decorativo dentro de la alerta -->
                            <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                        </div>
                        <!-- Bloque condicional que muestra mensajes de error o éxito -->
                        <?php if (!empty($Mensaje)) { ?>
                            <!-- Alerta dinámica que utiliza el estilo configurado en PHP -->
                            <div class="alert alert-<?php echo $Estilo; ?>" role="alert">
                                <!-- Texto del mensaje generado por el procesamiento del formulario -->
                                <?php echo $Mensaje; ?>
                            </div>
                        <?php } ?>
                        <!-- Formulario para registrar un nuevo transporte -->
                        <form class="row g-3" method="post" action="" novalidate>
                            <!-- Campo de selección de la marca del transporte -->
                            <div class="col-12">
                                <!-- Etiqueta del campo marca -->
                                <label for="marca_id" class="form-label">Marca (*)</label>
                                <!-- Selector desplegable con las opciones de marcas -->
                                <select class="form-select" id="marca_id" name="marca_id">
                                    <!-- Opción por defecto para obligar a elegir una marca -->
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    // Recorremos cada marca disponible para construir las opciones del selector
                                    for ($i = 0; $i < $CantidadMarcas; $i++) {
                                        // Verificamos si la marca actual es la que había seleccionado el usuario
                                        $Seleccionado = (!empty($MarcaSeleccionada) && $MarcaSeleccionada == $marcas[$i]['id'])
                                        ? 'selected' : '';
                                        ?>
                                        <!-- Opción individual del selector con el identificador y la denominación de la marca -->
                                        <option value="<?php echo $marcas[$i]['id']; ?>" <?php echo $Seleccionado; ?>>
                                            <?php echo $marcas[$i]['denominacion']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Campo de texto para ingresar el modelo del vehículo -->
                            <div class="col-12">
                                <!-- Etiqueta del campo modelo -->
                                <label for="modelo" class="form-label">Modelo (*)</label>
                                <!-- Entrada de texto con el valor persistente en caso de error -->
                                <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo !empty($ModeloValor) ? $ModeloValor : ''; ?>">
                            </div>
                            <!-- Campo de texto para registrar el año del vehículo -->
                            <div class="col-6">
                                <!-- Etiqueta del campo año -->
                                <label for="anio" class="form-label">Año</label>
                                <!-- Entrada donde se espera un formato de año de cuatro dígitos -->
                                <input type="text" class="form-control" id="anio" name="anio" value="<?php echo !empty($AnioValor) ? $AnioValor : ''; ?>" placeholder="AAAA">
                            </div>
                            <!-- Campo de texto para registrar la patente -->
                            <div class="col-6">
                                <!-- Etiqueta del campo patente -->
                                <label for="patente" class="form-label">Patente (*)</label>
                                <!-- Entrada que guarda la patente ya ingresada si existió un error -->
                                <input type="text" class="form-control" id="patente" name="patente" value="<?php echo !empty($PatenteValor) ? $PatenteValor : ''; ?>">
                            </div>
                            <!-- Campo para indicar si el transporte está habilitado -->
                            <div class="col-12">
                                <!-- Contenedor del checkbox usando estilos de Bootstrap -->
                                <div class="form-check">
                                    <!-- Checkbox que marca si el transporte está disponible -->
                                    <input class="form-check-input" type="checkbox" id="disponible" name="disponible" <?php echo $DisponibleMarcado ? 'checked' : ''; ?>>
                                    <!-- Etiqueta que describe la funcionalidad del checkbox -->
                                    <label class="form-check-label" for="disponible">
                                        Habilitado
                                    </label>
                                </div>
                            </div>
                            <!-- Zona de acciones finales del formulario -->
                            <div class="text-center">
                                <!-- Botón que envía los datos para registrar el transporte -->
                                <button class="btn btn-primary" type="submit" name="BotonRegistrar" value="Registrar">Registrar</button>
                                <!-- Enlace que recarga la página para limpiar los campos -->
                                <a href="camion_carga.php" class="btn btn-secondary">Limpiar Campos</a>
                                <!-- Enlace rápido para regresar al inicio de la aplicación -->
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
// Cargamos el pie de página estándar del sitio
require_once 'includes/footer.php';
?>
