<?php

require_once 'funciones/conexion.php';

if (!isset($_SESSION)) {
    session_start();
}

/**
 * Archivo: funciones/funciones.php (línea 12)
 * Propósito: Enviar al navegador una redirección controlada.
 * Descripción: Usa la cabecera HTTP Location para llevar al usuario a la ruta indicada y
 * termina el script inmediatamente, evitando que se siga ejecutando código posterior.
 * Retorna: void — No devuelve valor porque su responsabilidad es cambiar el flujo de
 * ejecución mediante la redirección y el exit.
 */
function Redireccionar($Ruta)
{
    header('Location: ' . $Ruta);
    exit;
}

/**
 * Archivo: funciones/funciones.php (línea 21)
 * Propósito: Validar credenciales de acceso y recuperar los datos del usuario.
 * Descripción: Construye una consulta SELECT sobre las tablas usuarios y niveles,
 * ejecuta la verificación en la base de datos y mapea los campos obtenidos a un arreglo
 * asociativo que se utiliza en la sesión. Normaliza la imagen asignando un valor por
 * defecto cuando no hay foto cargada.
 * Retorna: array — Devuelve un arreglo con los datos del usuario autenticado; si las
 * credenciales no son válidas, regresa un arreglo vacío para poder detectar el fallo.
 */
function DatosLogin($vUsuario, $vClave, $vConexion)
{
    $Usuario = array();

    $SQL = "SELECT U.id AS IdUsuario, U.apellido, U.nombre, U.usuario, U.clave, U.id_nivel,
                   U.imagen, U.activo, N.denominacion AS NombreNivel
            FROM usuarios U, niveles N
            WHERE U.id_nivel = N.id
            AND U.usuario = '$vUsuario'
            AND U.clave = '$vClave'";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $data = mysqli_fetch_array($rs);
        if (!empty($data)) {
            $Usuario['ID'] = $data['IdUsuario'];
            $Usuario['APELLIDO'] = $data['apellido'];
            $Usuario['NOMBRE'] = $data['nombre'];
            $Usuario['USUARIO'] = $data['usuario'];
            $Usuario['CLAVE'] = $data['clave'];
            $Usuario['NIVEL'] = $data['id_nivel'];
            $Usuario['NIVEL_NOMBRE'] = $data['NombreNivel'];
            $Usuario['ACTIVO'] = $data['activo'];
            $Usuario['IMG'] = !empty($data['imagen']) ? $data['imagen'] : 'user.png';
            $Usuario['SALUDO'] = 'Hola';
        }
    }

    return $Usuario;
}

/**
 * Archivo: funciones/funciones.php (línea 47)
 * Propósito: Listar los choferes activos disponibles en el sistema.
 * Descripción: Ejecuta una consulta filtrando usuarios de nivel 3 (rol chofer) y activos,
 * recorre el resultado y arma un arreglo indexado con apellido, nombre y DNI para su uso
 * en combos o listados.
 * Retorna: array — Entrega un arreglo con los choferes obtenidos; si no hay registros,
 * devuelve un arreglo vacío para que la interfaz pueda manejar esa situación.
 */
function Listar_Choferes($vConexion)
{
    $Listado = array();

    $SQL = "SELECT id, apellido, nombre, dni
            FROM usuarios
            WHERE id_nivel = 3 AND activo = 1
            ORDER BY apellido, nombre";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['apellido'] = $data['apellido'];
            $Listado[$i]['nombre'] = $data['nombre'];
            $Listado[$i]['dni'] = $data['dni'];
            $i++;
        }
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 72)
 * Propósito: Obtener los transportes disponibles asociados a sus marcas.
 * Descripción: Realiza un JOIN entre transportes y marcas, aplica el filtro de
 * disponibilidad y prepara un arreglo con los datos principales (marca, modelo y
 * patente) para mostrarlos al usuario al momento de asignar un viaje.
 * Retorna: array — Devuelve el listado de transportes listos para ser utilizados; un
 * arreglo vacío indica que no hay vehículos disponibles.
 */
function Listar_Transportes($vConexion)
{
    $Listado = array();

    $SQL = "SELECT T.id, M.denominacion AS marca, T.modelo, T.patente
            FROM transportes T, marcas M
            WHERE M.id = T.marca_id
            AND T.disponible = 1
            ORDER BY M.denominacion, T.modelo, T.patente";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['marca'] = $data['marca'];
            $Listado[$i]['modelo'] = $data['modelo'];
            $Listado[$i]['patente'] = $data['patente'];
            $i++;
        }
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 98)
 * Propósito: Recuperar todas las marcas de transporte cargadas.
 * Descripción: Ejecuta una consulta simple sobre la tabla marcas ordenando por nombre y
 * construye un arreglo con los identificadores y denominaciones para poblar selectores.
 * Retorna: array — Regresa el listado de marcas; un arreglo vacío indica que aún no se
 * registraron marcas.
 */
function Listar_Marcas($vConexion)
{
    $Listado = array();

    $SQL = "SELECT id, denominacion FROM marcas ORDER BY denominacion";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['denominacion'] = $data['denominacion'];
            $i++;
        }
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 117)
 * Propósito: Listar los destinos configurados en el sistema.
 * Descripción: Consulta la tabla destinos, ordena el resultado alfabéticamente y arma un
 * arreglo con pares id/denominación que luego se utilizan al registrar viajes.
 * Retorna: array — Devuelve todos los destinos encontrados; un arreglo vacío señala que
 * todavía no existen destinos cargados.
 */
function Listar_Destinos($vConexion)
{
    $Listado = array();

    $SQL = "SELECT id, denominacion FROM destinos ORDER BY denominacion";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['denominacion'] = $data['denominacion'];
            $i++;
        }
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 136)
 * Propósito: Validar la información ingresada al crear un chofer.
 * Descripción: Obtiene los datos enviados por POST, verifica longitud de textos y
 * estructura del DNI y consulta funciones auxiliares para confirmar que usuario y DNI no
 * estén repetidos. Trabaja con los valores tal como fueron ingresados en el formulario.
 * Retorna: string — Devuelve un mensaje concatenado con todos los errores detectados; si
 * es una cadena vacía significa que los datos están listos para guardarse.
 */
function Validar_Datos_Chofer($vConexion)
{
    $Mensaje = '';

    $Apellido = isset($_POST['apellido']) ? $_POST['apellido'] : '';
    $Nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $Dni = isset($_POST['dni']) ? $_POST['dni'] : '';
    $Usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $Clave = isset($_POST['clave']) ? $_POST['clave'] : '';

    if (strlen($Apellido) < 2) {
        $Mensaje .= 'Debes ingresar un apellido con al menos 2 caracteres. <br />';
    }

    if (strlen($Nombre) < 2) {
        $Mensaje .= 'Debes ingresar un nombre con al menos 2 caracteres. <br />';
    }

    if ($Dni === '') {
        $Mensaje .= 'Debes ingresar el DNI. <br />';
    } elseif (!is_numeric($Dni) || strlen($Dni) < 7 || strlen($Dni) > 8) {
        $Mensaje .= 'El DNI debe tener 7 u 8 dígitos. <br />';
    } elseif (ExisteDNI($Dni, $vConexion)) {
        $Mensaje .= 'El DNI ingresado ya se encuentra registrado. <br />';
    }

    if ($Usuario === '') {
        $Mensaje .= 'Debes ingresar el usuario. <br />';
    } elseif (ExisteUsuario($Usuario, $vConexion)) {
        $Mensaje .= 'El usuario ingresado ya existe. <br />';
    }

    if ($Clave === '') {
        $Mensaje .= 'Debes ingresar la clave. <br />';
    } elseif (strlen($Clave) < 5) {
        $Mensaje .= 'La clave debe tener al menos 5 caracteres. <br />';
    }

    return $Mensaje;
}

/**
 * Archivo: funciones/funciones.php (línea 188)
 * Propósito: Persistir en la base de datos un nuevo chofer validado.
 * Descripción: Toma los valores recibidos en $_POST, construye un INSERT sobre la tabla
 * usuarios definiendo el nivel correspondiente a chofer y ejecuta la consulta; ante
 * fallas termina la ejecución para facilitar el diagnóstico.
 * Retorna: bool — Devuelve true cuando la inserción se ejecuta correctamente; en caso de
 * error, la función aborta mediante die, por lo que no se retorna false explícitamente.
 */
function Insertar_Chofer($vConexion)
{
    $Apellido = isset($_POST['apellido']) ? $_POST['apellido'] : '';
    $Nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $Dni = isset($_POST['dni']) ? $_POST['dni'] : '';
    $Usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $Clave = isset($_POST['clave']) ? $_POST['clave'] : '';

    $SQL = "INSERT INTO usuarios (apellido, nombre, dni, usuario, clave, activo, id_nivel, fecha_creacion)
            VALUES ('" . $Apellido . "', '" . $Nombre . "', '" . $Dni . "', '" . $Usuario . "', '" . $Clave . "', 1, 3, NOW())";

    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    return true;
}

/**
 * Archivo: funciones/funciones.php (línea 206)
 * Propósito: Revisar que los datos del formulario de transporte sean válidos.
 * Descripción: Evalúa selección de marca, formato de modelo, año y patente; verifica que
 * la patente no esté repetida consultando la base y conserva los valores tal como se
 * enviaron desde el formulario.
 * Retorna: string — Entrega un texto con los errores encontrados; si la cadena queda
 * vacía significa que los datos superaron todas las validaciones.
 */
function Validar_Datos_Transporte($vConexion)
{
    $Mensaje = '';

    $MarcaId = isset($_POST['marca_id']) ? (int) $_POST['marca_id'] : 0;
    $Modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
    $Anio = isset($_POST['anio']) ? $_POST['anio'] : '';
    $Patente = isset($_POST['patente']) ? $_POST['patente'] : '';

    if ($MarcaId == 0) {
        $Mensaje .= 'Debes seleccionar una marca. <br />';
    }

    if (strlen($Modelo) < 2) {
        $Mensaje .= 'Debes ingresar el modelo. <br />';
    }

    if ($Anio !== '') {
        if (!is_numeric($Anio) || strlen($Anio) != 4) {
            $Mensaje .= 'El año debe tener 4 dígitos. <br />';
        }
    }

    if ($Patente === '') {
        $Mensaje .= 'Debes ingresar la patente. <br />';
    } elseif (strlen($Patente) < 6 || strlen($Patente) > 7) {
        $Mensaje .= 'La patente debe tener entre 6 y 7 caracteres. <br />';
    } elseif (ExistePatente($Patente, $vConexion)) {
        $Mensaje .= 'La patente ingresada ya se encuentra registrada. <br />';
    }

    return $Mensaje;
}

/**
 * Archivo: funciones/funciones.php (línea 251)
 * Propósito: Guardar un transporte nuevo junto a sus atributos principales.
 * Descripción: Recupera los valores enviados por el formulario, arma la sentencia INSERT y
 * se encarga de transformar el año a NULL cuando no se indicó. Ejecuta la consulta y
 * detiene el script si ocurre un error para evitar estados inconsistentes.
 * Retorna: bool — Devuelve true al completar el alta correctamente, lo que permite a la
 * lógica llamante saber que puede continuar con normalidad.
 */
function Insertar_Transporte($vConexion)
{
    $Marca = isset($_POST['marca_id']) ? (int) $_POST['marca_id'] : 0;
    $Modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
    $Patente = isset($_POST['patente']) ? $_POST['patente'] : '';
    $Anio = isset($_POST['anio']) ? $_POST['anio'] : '';
    $Disponible = !empty($_POST['disponible']) ? 1 : 0;

    if ($Anio === '') {
        $Anio = 'NULL';
    } else {
        $Anio = (int) $Anio;
    }

    $SQL = "INSERT INTO transportes (marca_id, modelo, patente, anio, disponible, fecha_creacion)
            VALUES (" . $Marca . ", '" . $Modelo . "', '" . $Patente . "', " . $Anio . ", " . $Disponible . ", NOW())";

    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    return true;
}

/**
 * Archivo: funciones/funciones.php (línea 275)
 * Propósito: Verificar que la información de un viaje programado sea coherente.
 * Descripción: Controla que el chofer, transporte y destino existan en la base utilizando
 * funciones de existencia; valida el formato de fecha convirtiéndolo a YYYY-mm-dd y
 * normaliza importes y porcentajes usando los mismos datos ingresados por el usuario.
 * Retorna: string — Devuelve una cadena con todos los mensajes de error encontrados; si
 * no se detecta nada, retorna una cadena vacía indicando que se puede grabar el viaje.
 */
function Validar_Datos_Viaje($vConexion)
{
    $Mensaje = '';

    $Chofer = isset($_POST['chofer_id']) ? (int) $_POST['chofer_id'] : 0;
    $Transporte = isset($_POST['transporte_id']) ? (int) $_POST['transporte_id'] : 0;
    $Destino = isset($_POST['destino_id']) ? (int) $_POST['destino_id'] : 0;
    $Fecha = isset($_POST['fecha_programada']) ? $_POST['fecha_programada'] : '';
    $Costo = isset($_POST['costo']) ? $_POST['costo'] : '';
    $Porcentaje = isset($_POST['porcentaje_chofer']) ? $_POST['porcentaje_chofer'] : '';

    if ($Chofer == 0) {
        $Mensaje .= 'Debes seleccionar un chofer. <br />';
    } elseif (!ExisteChofer($Chofer, $vConexion)) {
        $Mensaje .= 'El chofer seleccionado no es válido. <br />';
    }

    if ($Transporte == 0) {
        $Mensaje .= 'Debes seleccionar un transporte. <br />';
    } elseif (!ExisteTransporte($Transporte, $vConexion)) {
        $Mensaje .= 'El transporte seleccionado no es válido. <br />';
    }

    if ($Destino == 0) {
        $Mensaje .= 'Debes seleccionar un destino. <br />';
    } elseif (!ExisteDestino($Destino, $vConexion)) {
        $Mensaje .= 'El destino seleccionado no es válido. <br />';
    }

    if ($Fecha === '') {
        $Mensaje .= 'Debes ingresar la fecha programada. <br />';
    } else {
        $Partes = explode('/', $Fecha);
        if (count($Partes) == 3) {
            $Dia = (int) $Partes[0];
            $Mes = (int) $Partes[1];
            $Anio = (int) $Partes[2];
            if (checkdate($Mes, $Dia, $Anio)) {
                $_POST['fecha_sql'] = $Anio . '-' . str_pad($Mes, 2, '0', STR_PAD_LEFT) . '-' . str_pad($Dia, 2, '0', STR_PAD_LEFT);
            } else {
                $Mensaje .= 'La fecha debe tener un formato válido dd/mm/aaaa. <br />';
            }
        } else {
            $Mensaje .= 'La fecha debe tener un formato válido dd/mm/aaaa. <br />';
        }
    }

    if ($Costo === '') {
        $Mensaje .= 'Debes ingresar el costo. <br />';
    } else {
        $CostoNormalizado = str_replace('.', '', $Costo);
        $CostoNormalizado = str_replace(',', '.', $CostoNormalizado);
        if (!is_numeric($CostoNormalizado)) {
            $Mensaje .= 'El costo debe ser numérico. <br />';
        } else {
            $_POST['costo_normalizado'] = $CostoNormalizado;
        }
    }

    if ($Porcentaje === '') {
        $Mensaje .= 'Debes ingresar el porcentaje del chofer. <br />';
    } elseif (!is_numeric($Porcentaje)) {
        $Mensaje .= 'El porcentaje debe ser numérico. <br />';
    } else {
        $Valor = (int) $Porcentaje;
        if ($Valor < 0 || $Valor > 100) {
            $Mensaje .= 'El porcentaje debe estar entre 0 y 100. <br />';
        }
    }

    return $Mensaje;
}

/**
 * Archivo: funciones/funciones.php (línea 352)
 * Propósito: Registrar en la base un nuevo viaje listo para ejecutarse.
 * Descripción: Lee los identificadores, fecha normalizada y valores económicos desde
 * $_POST, arma la sentencia INSERT y también guarda quién creó el registro utilizando la
 * sesión. Si la ejecución del SQL falla, aborta el proceso para evitar continuar con un
 * estado inconsistente.
 * Retorna: bool — Devuelve true cuando el registro se inserta con éxito, lo que confirma
 * a la capa superior que el viaje quedó guardado.
 */
function Insertar_Viaje($vConexion)
{
    $Chofer = isset($_POST['chofer_id']) ? (int) $_POST['chofer_id'] : 0;
    $Transporte = isset($_POST['transporte_id']) ? (int) $_POST['transporte_id'] : 0;
    $Destino = isset($_POST['destino_id']) ? (int) $_POST['destino_id'] : 0;
    $Fecha = !empty($_POST['fecha_sql']) ? $_POST['fecha_sql'] : '';
    $Costo = !empty($_POST['costo_normalizado']) ? $_POST['costo_normalizado'] : 0;
    $Porcentaje = isset($_POST['porcentaje_chofer']) ? (int) $_POST['porcentaje_chofer'] : 0;
    $CreadoPor = !empty($_SESSION['Usuario_ID']) ? (int) $_SESSION['Usuario_ID'] : 'NULL';

    $SQL = "INSERT INTO viajes (chofer_id, transporte_id, fecha_programada, destino_id, costo, porcentaje_chofer, creado_por, fecha_creacion)
            VALUES (" . $Chofer . ", " . $Transporte . ", '" . $Fecha . "', " . $Destino . ", " . $Costo . ", " . $Porcentaje . ", " . $CreadoPor . ", NOW())";

    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    return true;
}

/**
 * Archivo: funciones/funciones.php (línea 372)
 * Propósito: Generar un informe con los viajes programados y su información relacionada.
 * Descripción: Construye una consulta que vincula viajes con usuarios, transportes,
 * marcas y destinos. Permite filtrar por un chofer específico y devuelve todos los datos
 * relevantes (fecha, destino, costos, vehículo) para mostrarlos en tablas o reportes.
 * Retorna: array — Devuelve un arreglo indexado con cada viaje como elemento; un arreglo
 * vacío indica que no existen registros que cumplan los filtros aplicados.
 */
function Listar_Viajes($vConexion, $ChoferId = null)
{
    $Listado = array();

    $SQL = "SELECT V.id, V.fecha_programada, D.denominacion AS destino, V.costo, V.porcentaje_chofer,
                   C.apellido AS chofer_apellido, C.nombre AS chofer_nombre, C.dni AS chofer_dni,
                   M.denominacion AS marca, T.modelo, T.patente
            FROM viajes V, usuarios C, transportes T, marcas M, destinos D
            WHERE C.id = V.chofer_id
            AND T.id = V.transporte_id
            AND M.id = T.marca_id
            AND D.id = V.destino_id";

    if (!empty($ChoferId)) {
        $SQL .= " AND V.chofer_id = " . (int) $ChoferId;
    }

    $SQL .= " ORDER BY V.fecha_programada, D.denominacion";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['fecha_programada'] = $data['fecha_programada'];
            $Listado[$i]['destino'] = $data['destino'];
            $Listado[$i]['costo'] = $data['costo'];
            $Listado[$i]['porcentaje_chofer'] = $data['porcentaje_chofer'];
            $Listado[$i]['chofer_apellido'] = $data['chofer_apellido'];
            $Listado[$i]['chofer_nombre'] = $data['chofer_nombre'];
            $Listado[$i]['chofer_dni'] = $data['chofer_dni'];
            $Listado[$i]['marca'] = $data['marca'];
            $Listado[$i]['modelo'] = $data['modelo'];
            $Listado[$i]['patente'] = $data['patente'];
            $i++;
        }
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 414)
 * Propósito: Comprobar si un nombre de usuario ya está registrado.
 * Descripción: Ejecuta una consulta SELECT sobre la tabla usuarios filtrando por el
 * nombre recibido y analiza si existe al menos una fila. Esto ayuda a evitar duplicados
 * al crear nuevos usuarios o choferes.
 * Retorna: bool — Regresa true cuando encuentra un usuario coincidente; false cuando no
 * existe o si la consulta falla.
 */
function ExisteUsuario($Usuario, $vConexion)
{
    $SQL = "SELECT id FROM usuarios WHERE usuario = '" . $Usuario . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    // TODO ESTA LINEA COMPRUEBA DUPLICADOS: SI LA CONSULTA DEVUELVE DATOS, INDICA QUE EL USUARIO YA EXISTE Y RETORNA TRUE PARA BLOQUEAR UNA CREACION REPETIDA
    return !empty($Existe);
}

/**
 * Archivo: funciones/funciones.php (línea 428)
 * Propósito: Verificar si un número de DNI ya fue cargado previamente.
 * Descripción: Consulta la tabla usuarios buscando coincidencias exactas de DNI y usa el
 * resultado para impedir que se registren dos choferes con la misma identidad.
 * Retorna: bool — Devuelve true cuando encuentra un registro con ese DNI; false si no hay
 * coincidencias o si la consulta falla.
 */
function ExisteDNI($Dni, $vConexion)
{
    $SQL = "SELECT id FROM usuarios WHERE dni = '" . $Dni . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    // TODO ESTA VALIDACION EVITA DUPLICADOS DE DNI: AL DETECTAR UNA FILA RESULTANTE SABEMOS QUE EL DOCUMENTO YA ESTA CARGADO Y RESPONDEMOS TRUE PARA IMPEDIR REGISTROS REPETIDOS
    return !empty($Existe);
}

/**
 * Archivo: funciones/funciones.php (línea 442)
 * Propósito: Validar que una patente no esté repetida en la flota.
 * Descripción: Ejecuta una consulta sobre la tabla transportes filtrando la patente. El
 * resultado se usa en la validación del formulario para impedir duplicar vehículos.
 * Retorna: bool — Retorna true si encuentra al menos un transporte con la misma patente;
 * false en caso contrario o si la consulta no puede ejecutarse.
 */
function ExistePatente($Patente, $vConexion)
{
    $SQL = "SELECT id FROM transportes WHERE patente = '" . $Patente . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    // TODO ACA SE CONTROLAN PATENTES DUPLICADAS: SI EXISTE UN TRANSPORTE CON LA MISMA PATENTE SE DEVUELVE TRUE PARA EVITAR CARGAR UN VEHICULO REPETIDO
    return !empty($Existe);
}

/**
 * Archivo: funciones/funciones.php (línea 456)
 * Propósito: Confirmar que un identificador pertenezca a un chofer válido.
 * Descripción: Busca en la tabla usuarios el ID recibido y además verifica que su nivel
 * sea el correspondiente a chofer. Se utiliza al validar viajes para asegurar la
 * integridad de los datos.
 * Retorna: bool — Devuelve true si encuentra un chofer activo con ese ID; false cuando no
 * hay coincidencias o la consulta falla.
 */
function ExisteChofer($ChoferId, $vConexion)
{
    $SQL = "SELECT id FROM usuarios WHERE id = " . (int) $ChoferId . " AND id_nivel = 3";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    return !empty($Existe);
}

/**
 * Archivo: funciones/funciones.php (línea 470)
 * Propósito: Verificar la existencia de un transporte antes de relacionarlo a un viaje.
 * Descripción: Realiza una consulta simple a la tabla transportes buscando por ID. Esta
 * validación evita asignar viajes a registros inexistentes o eliminados.
 * Retorna: bool — Regresa true cuando encuentra el transporte; false si no existe o la
 * consulta devuelve error.
 */
function ExisteTransporte($TransporteId, $vConexion)
{
    $SQL = "SELECT id FROM transportes WHERE id = " . (int) $TransporteId;
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    return !empty($Existe);
}

/**
 * Archivo: funciones/funciones.php (línea 484)
 * Propósito: Chequear que un destino solicitado siga disponible en la base.
 * Descripción: Consulta la tabla destinos por ID y se utiliza durante la validación de
 * viajes para evitar referencias a destinos eliminados o inexistentes.
 * Retorna: bool — Devuelve true cuando el destino existe; false en caso contrario o si se
 * produce un error al ejecutar la consulta.
 */
function ExisteDestino($DestinoId, $vConexion)
{
    $SQL = "SELECT id FROM destinos WHERE id = " . (int) $DestinoId;
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    return !empty($Existe);
}

?>
