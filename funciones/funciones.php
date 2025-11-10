<?php

require_once 'funciones/conexion.php';

if (!isset($_SESSION)) {
    session_start();
}

function Redireccionar($Ruta)
{
    header('Location: ' . $Ruta);
    exit;
}

function DatosLogin($vUsuario, $vClave, $vConexion)
{
    $Usuario = array();

    $SQL = "SELECT U.id, U.apellido, U.nombre, U.usuario, U.clave, U.id_nivel, U.imagen, U.activo,
            N.denominacion AS NivelNombre
            FROM usuarios U, niveles N
            WHERE U.id_nivel = N.id
            AND U.usuario = '$vUsuario'
            AND U.clave = '$vClave'";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $data = mysqli_fetch_array($rs);

        if (!empty($data)) {
            $Usuario['ID'] = $data['id'];
            $Usuario['APELLIDO'] = $data['apellido'];
            $Usuario['NOMBRE'] = $data['nombre'];
            $Usuario['USUARIO'] = $data['usuario'];
            $Usuario['CLAVE'] = $data['clave'];
            $Usuario['NIVEL'] = $data['id_nivel'];
            $Usuario['NIVEL_NOMBRE'] = $data['NivelNombre'];
            $Usuario['ACTIVO'] = $data['activo'];
            $Usuario['IMG'] = !empty($data['imagen']) ? $data['imagen'] : 'user.png';
            $Usuario['SALUDO'] = 'Hola';
        }
    }

    return $Usuario;
}

function GuardarSesionUsuario($DatosUsuario)
{
    $_SESSION['Usuario_ID'] = !empty($DatosUsuario['ID']) ? $DatosUsuario['ID'] : 0;
    $_SESSION['Usuario_Nombre'] = !empty($DatosUsuario['NOMBRE']) ? $DatosUsuario['NOMBRE'] : '';
    $_SESSION['Usuario_Apellido'] = !empty($DatosUsuario['APELLIDO']) ? $DatosUsuario['APELLIDO'] : '';
    $_SESSION['Usuario_Usuario'] = !empty($DatosUsuario['USUARIO']) ? $DatosUsuario['USUARIO'] : '';
    $_SESSION['Usuario_Nivel'] = !empty($DatosUsuario['NIVEL']) ? $DatosUsuario['NIVEL'] : 0;
    $_SESSION['Usuario_NombreNivel'] = !empty($DatosUsuario['NIVEL_NOMBRE']) ? $DatosUsuario['NIVEL_NOMBRE'] : '';
    $_SESSION['Usuario_Img'] = !empty($DatosUsuario['IMG']) ? $DatosUsuario['IMG'] : 'user.png';
    $_SESSION['Usuario_Saludo'] = !empty($DatosUsuario['SALUDO']) ? $DatosUsuario['SALUDO'] : 'Hola';
    $_SESSION['Usuario_Activo'] = !empty($DatosUsuario['ACTIVO']) ? $DatosUsuario['ACTIVO'] : 0;
}

function CerrarSesionUsuario()
{
    $_SESSION = array();
    session_destroy();
}

function ObtenerUsuarioEnSesion()
{
    $Usuario = array();

    if (!empty($_SESSION['Usuario_ID'])) {
        $Usuario['id'] = $_SESSION['Usuario_ID'];
        $Usuario['apellido'] = !empty($_SESSION['Usuario_Apellido']) ? $_SESSION['Usuario_Apellido'] : '';
        $Usuario['nombre'] = !empty($_SESSION['Usuario_Nombre']) ? $_SESSION['Usuario_Nombre'] : '';
        $Usuario['usuario'] = !empty($_SESSION['Usuario_Usuario']) ? $_SESSION['Usuario_Usuario'] : '';
        $Usuario['id_nivel'] = !empty($_SESSION['Usuario_Nivel']) ? $_SESSION['Usuario_Nivel'] : 0;
        $Usuario['imagen'] = !empty($_SESSION['Usuario_Img']) ? $_SESSION['Usuario_Img'] : 'user.png';
    }

    return $Usuario;
}

function RequiereSesion()
{
    if (empty($_SESSION['Usuario_ID'])) {
        Redireccionar('login.php');
    }
}

function UsuarioEstaLogueado()
{
    return !empty($_SESSION['Usuario_ID']);
}

function NombreCompletoUsuario($Usuario)
{
    $Apellido = isset($Usuario['apellido']) ? $Usuario['apellido'] : '';
    $Nombre = isset($Usuario['nombre']) ? $Usuario['nombre'] : '';

    if ($Apellido != '' && $Nombre != '') {
        return $Apellido . ', ' . $Nombre;
    }

    return trim($Apellido . ' ' . $Nombre);
}

function DenominacionNivel($IdNivel)
{
    switch ((int) $IdNivel) {
        case 1:
            return 'Admin';
        case 2:
            return 'Operador';
        case 3:
            return 'Chofer';
    }

    return 'Usuario';
}

function DescripcionFuncionesNivel($IdNivel)
{
    switch ((int) $IdNivel) {
        case 1:
            return 'transportes, choferes y viajes';
        case 2:
            return 'transportes y viajes';
        case 3:
            return 'el seguimiento de los viajes asignados';
    }

    return 'la información disponible en el panel';
}

function EsAdministrador()
{
    return !empty($_SESSION['Usuario_Nivel']) && (int) $_SESSION['Usuario_Nivel'] === 1;
}

function EsOperador()
{
    return !empty($_SESSION['Usuario_Nivel']) && (int) $_SESSION['Usuario_Nivel'] === 2;
}

function EsChofer()
{
    return !empty($_SESSION['Usuario_Nivel']) && (int) $_SESSION['Usuario_Nivel'] === 3;
}

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

function Listar_Transportes($vConexion)
{
    $Listado = array();

    $SQL = "SELECT t.id, m.denominacion AS marca, t.modelo, t.patente
            FROM transportes t, marcas m
            WHERE m.id = t.marca_id
            AND t.disponible = 1
            ORDER BY m.denominacion, t.modelo, t.patente";

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

function Validar_Datos_Chofer($vConexion)
{
    $Mensaje = '';

    $Apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $Nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $Dni = isset($_POST['dni']) ? trim($_POST['dni']) : '';
    $Usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $Clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

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
    } else {
        $Usuario = strtolower($Usuario);
        if (ExisteUsuario($Usuario, $vConexion)) {
            $Mensaje .= 'El usuario ingresado ya existe. <br />';
        }
    }

    if ($Clave === '') {
        $Mensaje .= 'Debes ingresar la clave. <br />';
    } elseif (strlen($Clave) < 5) {
        $Mensaje .= 'La clave debe tener al menos 5 caracteres. <br />';
    }

    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }

    if (!empty($Usuario)) {
        $_POST['usuario'] = $Usuario;
    }

    return $Mensaje;
}

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

function Validar_Datos_Transporte($vConexion)
{
    $Mensaje = '';

    $MarcaId = isset($_POST['marca_id']) ? (int) $_POST['marca_id'] : 0;
    $Modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $Anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';
    $Patente = isset($_POST['patente']) ? strtoupper(trim($_POST['patente'])) : '';
    $Disponible = !empty($_POST['disponible']) ? 1 : 0;

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

    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }

    if ($Patente !== '') {
        $_POST['patente'] = $Patente;
    }

    $_POST['disponible'] = $Disponible;

    return $Mensaje;
}

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

function Validar_Datos_Viaje($vConexion)
{
    $Mensaje = '';

    $Chofer = isset($_POST['chofer_id']) ? (int) $_POST['chofer_id'] : 0;
    $Transporte = isset($_POST['transporte_id']) ? (int) $_POST['transporte_id'] : 0;
    $Destino = isset($_POST['destino_id']) ? (int) $_POST['destino_id'] : 0;
    $Fecha = isset($_POST['fecha_programada']) ? trim($_POST['fecha_programada']) : '';
    $Costo = isset($_POST['costo']) ? trim($_POST['costo']) : '';
    $Porcentaje = isset($_POST['porcentaje_chofer']) ? trim($_POST['porcentaje_chofer']) : '';

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

    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }

    return $Mensaje;
}

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

function Listar_Viajes($vConexion, $ChoferId = null)
{
    $Listado = array();

    $SQL = "SELECT v.id, v.fecha_programada, d.denominacion AS destino, v.costo, v.porcentaje_chofer,
                   c.apellido AS chofer_apellido, c.nombre AS chofer_nombre, c.dni AS chofer_dni,
                   m.denominacion AS marca, t.modelo, t.patente
            FROM viajes v, usuarios c, transportes t, marcas m, destinos d
            WHERE c.id = v.chofer_id
            AND t.id = v.transporte_id
            AND m.id = t.marca_id
            AND d.id = v.destino_id";

    if (!empty($ChoferId)) {
        $SQL .= " AND v.chofer_id = " . (int) $ChoferId;
    }

    $SQL .= " ORDER BY v.fecha_programada, d.denominacion";

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

function ExisteUsuario($Usuario, $vConexion)
{
    $SQL = "SELECT id FROM usuarios WHERE usuario = '" . $Usuario . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    return !empty($Existe);
}

function ExisteDNI($Dni, $vConexion)
{
    $SQL = "SELECT id FROM usuarios WHERE dni = '" . $Dni . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    return !empty($Existe);
}

function ExistePatente($Patente, $vConexion)
{
    $SQL = "SELECT id FROM transportes WHERE patente = '" . $Patente . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);

    return !empty($Existe);
}

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
