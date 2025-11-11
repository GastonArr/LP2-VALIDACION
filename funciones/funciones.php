<?php

// Importa el archivo de conexión para reutilizar la función que abre la base de datos.
require_once 'funciones/conexion.php';

// Comprueba si la variable de sesión no existe para evitar re-declaraciones.
if (!isset($_SESSION)) {
    // Inicia la sesión para disponer de variables persistentes entre peticiones.
    session_start();
}

// Declara la función Redireccionar encargada de llevar al usuario a otra ruta.
function Redireccionar($Ruta)
{
    // Envía al navegador la cabecera Location con la ruta recibida para redirigir al usuario.
    header('Location: ' . $Ruta);
    // Finaliza el script inmediatamente para garantizar que no se ejecute más código tras la redirección.
    exit;
}

// Declara la función DatosLogin que obtiene la información del usuario autenticado.
function DatosLogin($vUsuario, $vClave, $vConexion)
{
    // Crea un arreglo vacío que se utilizará para guardar los datos del usuario autenticado.
    $Usuario = array();

    // Define la consulta SQL que busca al usuario en la tabla usuarios y vincula su nivel en la tabla niveles.
    $SQL = "SELECT U.id AS IdUsuario, U.apellido, U.nombre, U.usuario, U.clave, U.id_nivel,
                   U.imagen, U.activo, N.denominacion AS NombreNivel
            FROM usuarios U, niveles N
            WHERE U.id_nivel = N.id
            AND U.usuario = '$vUsuario'
            AND U.clave = '$vClave'";

    // Ejecuta la consulta contra la conexión recibida para obtener el resultado de la búsqueda.
    $rs = mysqli_query($vConexion, $SQL);

    // Verifica que la consulta no haya fallado antes de procesar los datos.
    if ($rs != false) {
        // Obtiene la primera fila del resultado como un arreglo asociativo para acceder a los campos.
        $data = mysqli_fetch_array($rs);
        // Comprueba que se hayan encontrado datos para evitar errores al mapear.
        if (!empty($data)) {
            // Asigna el identificador del usuario al arreglo de salida para usarlo en la sesión.
            $Usuario['ID'] = $data['IdUsuario'];
            // Copia el apellido del usuario, útil para mostrar información personal.
            $Usuario['APELLIDO'] = $data['apellido'];
            // Guarda el nombre para construir mensajes personalizados.
            $Usuario['NOMBRE'] = $data['nombre'];
            // Registra el nombre de usuario para referencias posteriores.
            $Usuario['USUARIO'] = $data['usuario'];
            // Conserva la clave tal como está almacenada para comparaciones futuras si hiciera falta.
            $Usuario['CLAVE'] = $data['clave'];
            // Almacena el nivel numérico que indica los permisos del usuario.
            $Usuario['NIVEL'] = $data['id_nivel'];
            // Guarda la denominación del nivel para mostrarla en la interfaz.
            $Usuario['NIVEL_NOMBRE'] = $data['NombreNivel'];
            // Indica si el usuario está activo, lo que permite validar su estado.
            $Usuario['ACTIVO'] = $data['activo'];
            // Determina la imagen a utilizar, asignando un avatar por defecto si no hay uno cargado.
            $Usuario['IMG'] = !empty($data['imagen']) ? $data['imagen'] : 'user.png';
            // Agrega un mensaje de saludo básico que puede usarse en la vista.
            $Usuario['SALUDO'] = 'Hola';
        }
    }

    // Devuelve el arreglo con los datos del usuario para indicar el resultado del inicio de sesión.
    return $Usuario;
}

// Declara la función Listar_Choferes que construye el listado de choferes activos.
function Listar_Choferes($vConexion)
{
    // Inicializa un arreglo vacío para almacenar los choferes que se encuentren activos.
    $Listado = array();

    // Define la consulta que selecciona los datos básicos de los usuarios con rol de chofer y estado activo.
    $SQL = "SELECT id, apellido, nombre, dni
            FROM usuarios
            WHERE id_nivel = 3 AND activo = 1
            ORDER BY apellido, nombre";

    // Ejecuta la consulta para obtener la lista de choferes desde la base de datos.
    $rs = mysqli_query($vConexion, $SQL);

    // Comprueba que la consulta no haya fallado antes de recorrer los resultados.
    if ($rs != false) {
        // Inicializa un contador que se usará como índice numérico del arreglo de resultados.
        $i = 0;
        // Recorre cada fila devuelta por la consulta para mapearla al arreglo de salida.
        while ($data = mysqli_fetch_array($rs)) {
            // Registra el identificador del chofer para poder seleccionarlo en formularios.
            $Listado[$i]['id'] = $data['id'];
            // Copia el apellido del chofer para mostrarlo en listados ordenados alfabéticamente.
            $Listado[$i]['apellido'] = $data['apellido'];
            // Guarda el nombre propio del chofer para completar la información personal.
            $Listado[$i]['nombre'] = $data['nombre'];
            // Incluye el DNI para tener un dato único de referencia.
            $Listado[$i]['dni'] = $data['dni'];
            // Incrementa el índice para preparar la próxima posición del arreglo.
            $i++;
        }
    }

    // Devuelve la colección de choferes encontrados, vacía si no hubo coincidencias.
    return $Listado;
}

// Declara la función Listar_Transportes que devuelve los vehículos disponibles.
function Listar_Transportes($vConexion)
{
    // Crea un arreglo vacío que contendrá los transportes disponibles.
    $Listado = array();

    // Prepara la consulta que relaciona transportes con sus marcas y filtra solo los disponibles.
    $SQL = "SELECT T.id, M.denominacion AS marca, T.modelo, T.patente
            FROM transportes T, marcas M
            WHERE M.id = T.marca_id
            AND T.disponible = 1
            ORDER BY M.denominacion, T.modelo, T.patente";

    // Ejecuta la sentencia SQL usando la conexión proporcionada para obtener los vehículos.
    $rs = mysqli_query($vConexion, $SQL);

    // Comprueba que la ejecución no haya producido un error antes de leer los registros.
    if ($rs != false) {
        // Define un índice incremental para ordenar los elementos del listado resultante.
        $i = 0;
        // Recorre cada fila del resultado para transformarla en un elemento del arreglo.
        while ($data = mysqli_fetch_array($rs)) {
            // Guarda el identificador del transporte para poder seleccionarlo posteriormente.
            $Listado[$i]['id'] = $data['id'];
            // Registra el nombre de la marca para mostrarlo junto al vehículo.
            $Listado[$i]['marca'] = $data['marca'];
            // Conserva el modelo del transporte como referencia descriptiva.
            $Listado[$i]['modelo'] = $data['modelo'];
            // Almacena la patente para identificar el vehículo en la documentación.
            $Listado[$i]['patente'] = $data['patente'];
            // Avanza el índice para agregar el próximo transporte en la posición correcta.
            $i++;
        }
    }

    // Retorna el arreglo de transportes disponibles o un arreglo vacío si no se hallaron registros.
    return $Listado;
}

// Declara la función Listar_Marcas que obtiene todas las marcas registradas.
function Listar_Marcas($vConexion)
{
    // Inicializa un arreglo vacío donde se almacenarán las marcas encontradas.
    $Listado = array();

    // Crea la sentencia SQL que obtiene todas las marcas ordenadas alfabéticamente.
    $SQL = "SELECT id, denominacion FROM marcas ORDER BY denominacion";
    // Ejecuta la consulta utilizando la conexión de base de datos recibida.
    $rs = mysqli_query($vConexion, $SQL);

    // Valida que la consulta haya sido exitosa antes de procesar los resultados.
    if ($rs != false) {
        // Establece un índice para colocar cada marca en el arreglo resultante.
        $i = 0;
        // Itera sobre cada fila devuelta por la base de datos para mapearla al arreglo.
        while ($data = mysqli_fetch_array($rs)) {
            // Asigna el identificador de la marca a la posición correspondiente del arreglo.
            $Listado[$i]['id'] = $data['id'];
            // Guarda la denominación de la marca para poder mostrarla en la interfaz.
            $Listado[$i]['denominacion'] = $data['denominacion'];
            // Incrementa el índice para preparar el siguiente elemento del listado.
            $i++;
        }
    }

    // Retorna todas las marcas obtenidas para que puedan poblar controles de selección.
    return $Listado;
}

// Declara la función Listar_Destinos que recupera todos los destinos cargados.
function Listar_Destinos($vConexion)
{
    // Crea un arreglo vacío que almacenará los destinos recuperados.
    $Listado = array();

    // Redacta la consulta SQL que trae todos los destinos ordenados por su denominación.
    $SQL = "SELECT id, denominacion FROM destinos ORDER BY denominacion";
    // Ejecuta la consulta para obtener los datos almacenados en la tabla destinos.
    $rs = mysqli_query($vConexion, $SQL);

    // Revisa que la ejecución de la consulta haya sido satisfactoria antes de continuar.
    if ($rs != false) {
        // Define el índice que se usará para llenar el arreglo de destinos.
        $i = 0;
        // Recorre cada destino recuperado para guardarlo en la estructura de salida.
        while ($data = mysqli_fetch_array($rs)) {
            // Copia el identificador del destino para poder referenciarlo desde otros módulos.
            $Listado[$i]['id'] = $data['id'];
            // Guarda el nombre del destino con el propósito de mostrarlo al usuario.
            $Listado[$i]['denominacion'] = $data['denominacion'];
            // Incrementa el índice para pasar al siguiente registro.
            $i++;
        }
    }

    // Devuelve el arreglo con los destinos existentes para su uso en formularios o listados.
    return $Listado;
}

// Declara la función Validar_Datos_Chofer que revisa la información del formulario.
function Validar_Datos_Chofer($vConexion)
{
    // Define un mensaje vacío que se llenará con los errores detectados durante la validación.
    $Mensaje = '';

    // Recupera el apellido enviado desde el formulario para analizar su contenido.
    $Apellido = $_POST['apellido'];
    // Recupera el nombre ingresado para aplicar las validaciones necesarias.
    $Nombre = $_POST['nombre'];
    // Obtiene el DNI proporcionado por el usuario para evaluar su formato.
    $Dni = $_POST['dni'];
    // Extrae el nombre de usuario propuesto para comprobarlo.
    $Usuario = $_POST['usuario'];
    // Recupera la clave enviada para verificar sus requisitos mínimos.
    $Clave = $_POST['clave'];

    // Evalúa que el apellido tenga la longitud mínima requerida y agrega un mensaje si no la cumple.
    if (strlen($Apellido) < 2) {
        $Mensaje .= 'Debes ingresar un apellido con al menos 2 caracteres. <br />';
    }

    // Comprueba que el nombre posea al menos dos caracteres para garantizar que no quede vacío.
    if (strlen($Nombre) < 2) {
        $Mensaje .= 'Debes ingresar un nombre con al menos 2 caracteres. <br />';
    }

    // Verifica que el DNI haya sido cargado y cumpla con la longitud y formato numérico esperados.
    if ($Dni === '') {
        $Mensaje .= 'Debes ingresar el DNI. <br />';
    } elseif (!is_numeric($Dni) || strlen($Dni) < 7 || strlen($Dni) > 8) {
        $Mensaje .= 'El DNI debe tener 7 u 8 dígitos. <br />';
    }

    // Confirma que se haya escrito un nombre de usuario antes de intentar guardar los datos.
    if ($Usuario === '') {
        $Mensaje .= 'Debes ingresar el usuario. <br />';
    }

    // Revisa que la clave no esté vacía y que cumpla con la longitud mínima necesaria para aceptarla.
    if ($Clave === '') {
        $Mensaje .= 'Debes ingresar la clave. <br />';
    } elseif (strlen($Clave) < 5) {
        $Mensaje .= 'La clave debe tener al menos 5 caracteres. <br />';
    }

    // Retorna el listado de errores concatenados, o una cadena vacía si todas las validaciones pasaron.
    return $Mensaje;
}

// Declara la función Insertar_Chofer que guarda un nuevo chofer en la base.
function Insertar_Chofer($vConexion)
{
    // Recupera el apellido validado desde la variable POST para construir el registro.
    $Apellido = $_POST['apellido'];
    // Recupera el nombre del chofer que será almacenado.
    $Nombre = $_POST['nombre'];
    // Obtiene el DNI que identificará al chofer dentro del sistema.
    $Dni = $_POST['dni'];
    // Recupera el nombre de usuario que utilizará el chofer para acceder.
    $Usuario = $_POST['usuario'];
    // Obtiene la clave que se guardará asociada al chofer.
    $Clave = $_POST['clave'];

    // Construye la sentencia INSERT que almacena al nuevo chofer como usuario activo con nivel 3.
    $SQL = "INSERT INTO usuarios (apellido, nombre, dni, usuario, clave, activo, id_nivel, fecha_creacion)
            VALUES ('" . $Apellido . "', '" . $Nombre . "', '" . $Dni . "', '" . $Usuario . "', '" . $Clave . "', 1, 3, NOW())";

    // Ejecuta la inserción y, si falla, detiene el script para facilitar la detección del error.
    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    // Devuelve true para indicar que el chofer se insertó correctamente.
    return true;
}

// Declara la función Validar_Datos_Transporte que controla los datos de un vehículo.
function Validar_Datos_Transporte($vConexion)
{
    // Inicializa una cadena vacía que acumulará los mensajes de error detectados.
    $Mensaje = '';

    // Convierte el identificador de marca recibido para asegurar que se procese como número entero.
    $MarcaId = (int) $_POST['marca_id'];
    // Recupera el modelo ingresado para comprobar que tenga una longitud válida.
    $Modelo = $_POST['modelo'];
    // Obtiene el año de fabricación para verificar su formato antes de guardarlo.
    $Anio = $_POST['anio'];
    // Recupera la patente enviada para controlar que cumpla con los requisitos.
    $Patente = $_POST['patente'];

    // Valida que se haya seleccionado alguna marca, requisito para crear el transporte.
    if ($MarcaId == 0) {
        $Mensaje .= 'Debes seleccionar una marca. <br />';
    }

    // Comprueba que el modelo no sea demasiado corto para que la información resulte útil.
    if (strlen($Modelo) < 2) {
        $Mensaje .= 'Debes ingresar el modelo. <br />';
    }

    // Solo evalúa el año cuando se informó un valor para permitirlo opcional.
    if ($Anio !== '') {
        // Controla que el año sea numérico y tenga exactamente cuatro dígitos.
        if (!is_numeric($Anio) || strlen($Anio) != 4) {
            $Mensaje .= 'El año debe tener 4 dígitos. <br />';
        }
    }

    // Revisa que la patente se haya proporcionado y que su longitud esté dentro del rango válido.
    if ($Patente === '') {
        $Mensaje .= 'Debes ingresar la patente. <br />';
    } elseif (strlen($Patente) < 6 || strlen($Patente) > 7) {
        $Mensaje .= 'La patente debe tener entre 6 y 7 caracteres. <br />';
    }

    // Devuelve todos los mensajes de error generados para que el formulario pueda mostrarlos.
    return $Mensaje;
}

// Declara la función Insertar_Transporte que registra un vehículo en la base.
function Insertar_Transporte($vConexion)
{
    // Convierte el identificador de marca recibido para trabajar con un valor numérico seguro.
    $Marca = (int) $_POST['marca_id'];
    // Obtiene el modelo del transporte que será almacenado en la base de datos.
    $Modelo = $_POST['modelo'];
    // Recupera la patente ingresada que identifica al vehículo.
    $Patente = $_POST['patente'];
    // Obtiene el año enviado por el formulario, que puede estar vacío.
    $Anio = $_POST['anio'];
    // Determina si el transporte debe quedar marcado como disponible según el checkbox recibido.
    $Disponible = !empty($_POST['disponible']) ? 1 : 0;

    // Ajusta el valor del año a NULL cuando no se proporcionó para respetar la estructura de la base.
    if ($Anio === '') {
        $Anio = 'NULL';
    } else {
        // Convierte el año a entero cuando existe para almacenarlo como número.
        $Anio = (int) $Anio;
    }

    // Genera la sentencia INSERT que registra el transporte con sus atributos principales y la fecha de creación.
    $SQL = "INSERT INTO transportes (marca_id, modelo, patente, anio, disponible, fecha_creacion)
            VALUES (" . $Marca . ", '" . $Modelo . "', '" . $Patente . "', " . $Anio . ", " . $Disponible . ", NOW())";

    // Ejecuta la consulta y detiene la aplicación con un mensaje descriptivo si ocurre un error.
    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    // Retorna true para indicar que el transporte fue insertado correctamente en la base de datos.
    return true;
}

// Declara la función Validar_Datos_Viaje que revisa la información para programar un viaje.
function Validar_Datos_Viaje($vConexion)
{
    // Inicia una cadena vacía para acumular los mensajes de error detectados durante la validación.
    $Mensaje = '';

    // Convierte el identificador del chofer a entero para trabajar con un dato numérico confiable.
    $Chofer = (int) $_POST['chofer_id'];
    // Convierte el identificador del transporte a entero para verificar si se seleccionó un vehículo.
    $Transporte = (int) $_POST['transporte_id'];
    // Convierte el identificador del destino a entero para comprobar que se haya elegido uno válido.
    $Destino = (int) $_POST['destino_id'];
    // Recupera la fecha ingresada por el usuario en formato día/mes/año.
    $Fecha = $_POST['fecha_programada'];
    // Obtiene el costo ingresado como texto para normalizarlo luego.
    $Costo = $_POST['costo'];
    // Recupera el porcentaje destinado al chofer para validar su rango.
    $Porcentaje = $_POST['porcentaje_chofer'];

    // Verifica que se haya seleccionado un chofer y agrega un mensaje si falta.
    if ($Chofer == 0) {
        $Mensaje .= 'Debes seleccionar un chofer. <br />';
    }

    // Confirma que haya un transporte elegido, requisito esencial para programar el viaje.
    if ($Transporte == 0) {
        $Mensaje .= 'Debes seleccionar un transporte. <br />';
    }

    // Revisa que se haya indicado un destino válido.
    if ($Destino == 0) {
        $Mensaje .= 'Debes seleccionar un destino. <br />';
    }

    // Controla que la fecha no esté vacía y que respete el formato esperado.
    if ($Fecha === '') {
        $Mensaje .= 'Debes ingresar la fecha programada. <br />';
    } else {
        // Divide la fecha utilizando la barra como separador para obtener día, mes y año.
        $Partes = explode('/', $Fecha);
        // Comprueba que se hayan obtenido exactamente tres partes.
        if (count($Partes) == 3) {
            // Convierte la primera parte a entero para representar el día.
            $Dia = (int) $Partes[0];
            // Convierte la segunda parte a entero para representar el mes.
            $Mes = (int) $Partes[1];
            // Convierte la tercera parte a entero para representar el año.
            $Anio = (int) $Partes[2];
            // Usa checkdate para validar que la combinación de día, mes y año sea una fecha real.
            if (checkdate($Mes, $Dia, $Anio)) {
                // Construye la fecha en formato compatible con SQL y la guarda en $_POST para reutilizarla.
                $_POST['fecha_sql'] = $Anio . '-' . str_pad($Mes, 2, '0', STR_PAD_LEFT) . '-' . str_pad($Dia, 2, '0', STR_PAD_LEFT);
            } else {
                // Añade un mensaje de error cuando la fecha no es válida.
                $Mensaje .= 'La fecha debe tener un formato válido dd/mm/aaaa. <br />';
            }
        } else {
            // Indica que la fecha es inválida si no se pudieron separar tres componentes.
            $Mensaje .= 'La fecha debe tener un formato válido dd/mm/aaaa. <br />';
        }
    }

    // Revisa que el costo haya sido ingresado y que se pueda interpretar como número.
    if ($Costo === '') {
        $Mensaje .= 'Debes ingresar el costo. <br />';
    } else {
        // Elimina los puntos usados como separadores de miles para normalizar el valor.
        $CostoNormalizado = str_replace('.', '', $Costo);
        // Reemplaza la coma decimal por un punto para adaptarlo al formato numérico de PHP.
        $CostoNormalizado = str_replace(',', '.', $CostoNormalizado);
        // Comprueba que el resultado sea numérico antes de aceptarlo.
        if (!is_numeric($CostoNormalizado)) {
            $Mensaje .= 'El costo debe ser numérico. <br />';
        } else {
            // Guarda el valor normalizado en $_POST para usarlo al momento de insertar el viaje.
            $_POST['costo_normalizado'] = $CostoNormalizado;
        }
    }

    // Controla que el porcentaje del chofer se haya ingresado correctamente.
    if ($Porcentaje === '') {
        $Mensaje .= 'Debes ingresar el porcentaje del chofer. <br />';
    } elseif (!is_numeric($Porcentaje)) {
        // Verifica que el porcentaje sea un número antes de continuar con la validación del rango.
        $Mensaje .= 'El porcentaje debe ser numérico. <br />';
    } else {
        // Convierte el porcentaje a entero para evaluar su rango permitido.
        $Valor = (int) $Porcentaje;
        // Comprueba que el porcentaje esté entre 0 y 100 inclusive.
        if ($Valor < 0 || $Valor > 100) {
            $Mensaje .= 'El porcentaje debe estar entre 0 y 100. <br />';
        }
    }

    // Devuelve todos los mensajes acumulados, o una cadena vacía si los datos son válidos.
    return $Mensaje;
}

// Declara la función Insertar_Viaje que almacena un nuevo viaje programado.
function Insertar_Viaje($vConexion)
{
    // Convierte el identificador del chofer a entero para construir la relación con el viaje.
    $Chofer = (int) $_POST['chofer_id'];
    // Convierte el identificador del transporte a entero para asociar el vehículo correspondiente.
    $Transporte = (int) $_POST['transporte_id'];
    // Convierte el identificador del destino a entero para registrar hacia dónde se dirige el viaje.
    $Destino = (int) $_POST['destino_id'];
    // Obtiene la fecha normalizada en formato compatible con SQL, o una cadena vacía si no se generó.
    $Fecha = !empty($_POST['fecha_sql']) ? $_POST['fecha_sql'] : '';
    // Recupera el costo ya normalizado, o cero si no se pudo calcular.
    $Costo = !empty($_POST['costo_normalizado']) ? $_POST['costo_normalizado'] : 0;
    // Convierte el porcentaje del chofer a entero para almacenar el valor exacto.
    $Porcentaje = (int) $_POST['porcentaje_chofer'];
    // Obtiene el identificador del usuario autenticado que está creando el viaje, o NULL si no existe sesión.
    $CreadoPor = !empty($_SESSION['Usuario_ID']) ? (int) $_SESSION['Usuario_ID'] : 'NULL';

    // Prepara la sentencia INSERT que almacena el viaje con todas sus asociaciones y valores económicos.
    $SQL = "INSERT INTO viajes (chofer_id, transporte_id, fecha_programada, destino_id, costo, porcentaje_chofer, creado_por, fecha_creacion)
            VALUES (" . $Chofer . ", " . $Transporte . ", '" . $Fecha . "', " . $Destino . ", " . $Costo . ", " . $Porcentaje . ", " . $CreadoPor . ", NOW())";

    // Ejecuta la inserción y finaliza el script con un mensaje si la operación falla.
    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    // Retorna true para indicar que el viaje fue guardado exitosamente.
    return true;
}

// Declara la función Listar_Viajes que arma el informe con los viajes registrados.
function Listar_Viajes($vConexion, $ChoferId = null)
{
    // Crea un arreglo vacío que contendrá los viajes obtenidos de la base de datos.
    $Listado = array();

    // Redacta la consulta que reúne los viajes con sus choferes, transportes, marcas y destinos.
    $SQL = "SELECT V.id, V.fecha_programada, D.denominacion AS destino, V.costo, V.porcentaje_chofer,
                   C.apellido AS chofer_apellido, C.nombre AS chofer_nombre, C.dni AS chofer_dni,
                   M.denominacion AS marca, T.modelo, T.patente
            FROM viajes V, usuarios C, transportes T, marcas M, destinos D
            WHERE C.id = V.chofer_id
            AND T.id = V.transporte_id
            AND M.id = T.marca_id
            AND D.id = V.destino_id";

    // Si se proporciona un identificador de chofer, agrega el filtro para obtener solo sus viajes.
    if (!empty($ChoferId)) {
        $SQL .= " AND V.chofer_id = " . (int) $ChoferId;
    }

    // Añade el criterio de ordenamiento por fecha y destino para presentar la información ordenada.
    $SQL .= " ORDER BY V.fecha_programada, D.denominacion";

    // Ejecuta la consulta para recuperar los viajes con toda su información relacionada.
    $rs = mysqli_query($vConexion, $SQL);

    // Valida que la consulta haya sido exitosa antes de procesar los resultados.
    if ($rs != false) {
        // Inicializa un índice que permitirá ubicar cada viaje dentro del arreglo.
        $i = 0;
        // Recorre cada registro devuelto para mapearlo al arreglo final.
        while ($data = mysqli_fetch_array($rs)) {
            // Almacena el identificador único del viaje para futuras operaciones.
            $Listado[$i]['id'] = $data['id'];
            // Guarda la fecha programada para poder mostrar cuándo se realizará el viaje.
            $Listado[$i]['fecha_programada'] = $data['fecha_programada'];
            // Copia el nombre del destino para indicar hacia dónde se dirige el viaje.
            $Listado[$i]['destino'] = $data['destino'];
            // Registra el costo del viaje para proporcionar información económica.
            $Listado[$i]['costo'] = $data['costo'];
            // Guarda el porcentaje asignado al chofer para cálculos posteriores.
            $Listado[$i]['porcentaje_chofer'] = $data['porcentaje_chofer'];
            // Almacena el apellido del chofer para identificar quién realizará el viaje.
            $Listado[$i]['chofer_apellido'] = $data['chofer_apellido'];
            // Guarda el nombre del chofer complementando su identificación personal.
            $Listado[$i]['chofer_nombre'] = $data['chofer_nombre'];
            // Registra el DNI del chofer para disponer de un dato de referencia único.
            $Listado[$i]['chofer_dni'] = $data['chofer_dni'];
            // Guarda la marca del transporte para describir el vehículo utilizado.
            $Listado[$i]['marca'] = $data['marca'];
            // Almacena el modelo del transporte para brindar más detalles del vehículo.
            $Listado[$i]['modelo'] = $data['modelo'];
            // Guarda la patente para identificar de forma inequívoca al transporte.
            $Listado[$i]['patente'] = $data['patente'];
            // Incrementa el índice para colocar el siguiente viaje en la posición adecuada.
            $i++;
        }
    }

    // Devuelve el arreglo que contiene todos los viajes encontrados o vacío si no hay registros.
    return $Listado;
}

?>
