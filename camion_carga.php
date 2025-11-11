<?php
// Iniciamos o retomamos la sesión para trabajar con las variables persistentes del usuario conectado.
session_start();

// Incorporamos el archivo que define la función ConexionBD() para abrir la comunicación con la base de datos MySQL.
require_once 'funciones/conexion.php';
// Incorporamos el archivo con todas las funciones auxiliares que encapsulan reglas de validación y operaciones de guardado.
require_once 'funciones/funciones.php';

// Si la variable de sesión con el identificador del usuario no existe significa que nadie inició sesión todavía.
if (empty($_SESSION['Usuario_ID'])) {
    // Redirigimos al formulario de login para que ingrese sus credenciales.
    header('Location: login.php');
    // Cortamos la ejecución inmediatamente para que no se muestre nada de esta página protegida.
    exit;
}

// Solo permitimos que el nivel 1 (administrador) pueda registrar transportes como se mostró en clase.
if (empty($_SESSION['Usuario_Nivel']) || (int) $_SESSION['Usuario_Nivel'] > 1) {
    // Si no tiene el nivel requerido lo enviamos al panel principal.
    header('Location: index.php');
    // Terminamos el script para evitar accesos indebidos.
    exit;
}

// Abrimos la conexión a la base de datos y guardamos el enlace para reutilizarlo a lo largo del script.
$MiConexion = ConexionBD();

// Definimos el título que se mostrará en la pestaña del navegador y en el encabezado HTML.
$pageTitle = 'Registrar un nuevo transporte';
// Indicamos qué elemento del menú lateral debe resaltarse para marcar la ubicación actual del usuario.
$activePage = 'camion_carga';

// Listar_Marcas() devuelve un arreglo con todas las marcas cargadas en la tabla marcas, lo guardamos para armar el selector.
$marcas = Listar_Marcas($MiConexion);

// Preparamos variables de mensaje para avisos de validación o confirmaciones.
$Mensaje = '';
// Establecemos el estilo por defecto en warning (amarillo) hasta saber el resultado final de la operación.
$Estilo = 'warning';

// Cuando el usuario presiona el botón de enviar el formulario llega el campo BotonRegistrar en $_POST.
if (!empty($_POST['BotonRegistrar'])) {
    // Ejecutamos la rutina de validación que revisa todos los campos requeridos y formatos.
    $Mensaje = Validar_Datos_Transporte($MiConexion);
    // Si la validación no devolvió errores (es decir $Mensaje quedó vacío) procedemos a guardar los datos.
    if (empty($Mensaje)) {
        // Insertar_Transporte() intenta crear el registro en la base y devuelve false si hubo algún problema.
        if (Insertar_Transporte($MiConexion) != false) {
            // Informamos al usuario que el transporte se registró correctamente.
            $Mensaje = 'Se ha registrado correctamente.';
            // Limpiamos $_POST para que los campos del formulario aparezcan vacíos tras la recarga.
            $_POST = array();
            // Cambiamos el estilo de la alerta a success (verde) para mostrar un mensaje positivo.
            $Estilo = 'success';
        }
    }
}

// Calculamos la cantidad de marcas disponibles para controlar el bucle que arma el listado de opciones.
$CantidadMarcas = count($marcas);
// Recuperamos la marca seleccionada anteriormente para dejarla marcada si hubo un error de validación.
$MarcaSeleccionada = !empty($_POST['marca_id']) ? $_POST['marca_id'] : '';
// Guardamos el modelo ingresado por el usuario para que no se pierda si la validación falla.
$ModeloValor = !empty($_POST['modelo']) ? $_POST['modelo'] : '';
// Guardamos el año ingresado por el usuario con el mismo objetivo.
$AnioValor = !empty($_POST['anio']) ? $_POST['anio'] : '';
// Guardamos la patente ingresada previamente.
$PatenteValor = !empty($_POST['patente']) ? $_POST['patente'] : '';
// Si el usuario envió el formulario usamos el valor real del checkbox, de lo contrario lo dejamos marcado por defecto.
$DisponibleMarcado = !empty($_POST) ? !empty($_POST['disponible']) : true;

// Agregamos la cabecera compartida que define el HTML inicial y los recursos.
require_once 'includes/header.php';
// Agregamos la barra superior que muestra el usuario actual y accesos rápidos.
require_once 'includes/topbar.php';
// Agregamos el menú lateral de navegación.
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
                                    // Recorremos cada marca disponible para construir las opciones del selector.
                                    for ($i = 0; $i < $CantidadMarcas; $i++) {
                                        // Verificamos si la marca actual es la que había seleccionado el usuario para dejarla marcada.
                                        $Seleccionado = (!empty($MarcaSeleccionada) && $MarcaSeleccionada == $marcas[$i]['id']) ? 'selected' : '';
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
// Cargamos el pie de página estándar del sitio que cierra la estructura HTML y agrega los scripts compartidos.
require_once 'includes/footer.php';
?>
