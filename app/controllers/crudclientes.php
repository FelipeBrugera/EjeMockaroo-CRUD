<?php
require './vendor/autoload.php';
use Mpdf\Mpdf;
define('MAX_UPLOAD', 500000); //Maximo de tamaño de imagen 500Kb
define('DIRIMAGEN',"app/uploads/"); //Ruta Directorio de imgusers

function crudBorrar ($id){    
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ( $resu){
         $_SESSION['msg'] = " El usuario ".$id. " ha sido eliminado.";
    } else {
         $_SESSION['msg'] = " Error al eliminar el usuario ".$id.".";
    }

}

function crudTerminar(){
    AccesoDatos::closeModelo();
    session_destroy();
}
 
function crudAlta(){
    $cli = new Cliente();
    $orden= "Nuevo";
    include_once "app/views/formulario.php";
}

function crudDetalles($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    
    // Obtener la URL de la bandera utilizando el método procesarInfoIP
    if (!$banderaUrl = obtenerBanderaDesdeCodigoPais($cli->id))
    {
        $banderaUrl = "https://flagpedia.net/data/flags/w702/aq.webp";
    }

    if($cli->id >= 1 && $cli->id <= 10)
    {
        if($cli->id < 10)
        {
            $imagenCli = "app/uploads/0000000" . $cli->id . ".jpg";
        }
        else
        {
            $imagenCli = "app/uploads/000000" . $cli->id . ".jpg";
        }
    }
    else
    {
        $imagenCli = generarImagenAleatoria($cli->id);
    }
    include_once "app/views/detalles.php";
}

function crudDetallesSiguiente($id){

    $db = AccesoDatos::getModelo();
    $cliente = $db->getCliente($id);
    $dato = $cliente->{$_SESSION["orden"]};
    if ($cli = $db->getClienteSiguiente($_SESSION["orden"], $dato)) 
    {
        // Obtener la URL de la bandera utilizando el método procesarInfoIP
        if (!$banderaUrl = obtenerBanderaDesdeCodigoPais($cli->id))
        {
            $banderaUrl = "https://flagpedia.net/data/flags/w702/aq.webp";
        }
        
        //Obtener la imagen del cliente
        if($cli->id >= 1 && $cli->id <= 10)
        {
            if($cli->id < 10)
            {
                $imagenCli = "app/uploads/0000000" . $cli->id . ".jpg";
            }
            else
            {
                $imagenCli = "app/uploads/000000" . $cli->id . ".jpg";
            }
        }
        else
        {
            $imagenCli = generarImagenAleatoria($cli->id);
        }

        include_once "app/views/detalles.php";
    } 
    else {
        crudDetalles($id);
    }
}

function crudDetallesAnterior($id){
    $db = AccesoDatos::getModelo();
    $cliente = $db->getCliente($id);
    $dato = $cliente->{$_SESSION["orden"]};
    if ($cli = $db->getClienteAnterior($_SESSION["orden"], $dato)) 
    {
        // Obtener la URL de la bandera utilizando el método procesarInfoIP
        if (!$banderaUrl = obtenerBanderaDesdeCodigoPais($cli->id))
        {
            $banderaUrl = "https://flagpedia.net/data/flags/w702/aq.webp";
        }
        
        //Obtener la imagen
        if($cli->id >= 1 && $cli->id <= 10)
        {
            if($cli->id < 10)
            {
                $imagenCli = "app/uploads/0000000" . $cli->id . ".jpg";
            }
            else
            {
                $imagenCli = "app/uploads/000000" . $cli->id . ".jpg";
            }
        }
        else
        {
            $imagenCli = generarImagenAleatoria($cli->id);
        }

        include_once "app/views/detalles.php";
    } 
    else {
        crudDetalles($id);
    }
}

function crudModificarSiguiente($id){
    $db = AccesoDatos::getModelo();
    $cliente = $db->getCliente($id);
    $dato = $cliente->{$_SESSION["orden"]};
    if ($cli = $db->getClienteSiguiente($_SESSION["orden"], $dato)) {

        $orden="Modificar";
        include_once "app/views/formulario.php";

    } else {
        crudModificar($id);
    }
}

function crudModificarAnterior($id){
    $db = AccesoDatos::getModelo();
    $cliente = $db->getCliente($id);
    $dato = $cliente->{$_SESSION["orden"]};
    if ($cli = $db->getClienteAnterior($_SESSION["orden"], $dato)) {

        $orden="Modificar";
        include_once "app/views/formulario.php";

    } else {
        crudModificar($id);
    }
}


function crudModificar($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden="Modificar";
    include_once "app/views/formulario.php";
}

function crudPostAlta(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    
    $cli = new Cliente();
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];	
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];

    // Validar correo electrónico
    if (!validarEmail($cli->email)) {
        $_SESSION['msg'] = "Correo electrónico no válido o ya registrado.";
        return; // No continúes con el alta si el correo no es válido
    }

    // Validar dirección IP
    if (!validarIP($cli->ip_address)) {
        $_SESSION['msg'] = "Dirección IP no válida.";
        return; // No continúes con el alta si la IP no es válida
    }

    // Validar número de teléfono
    if (!validarTelefono($cli->telefono)) {
        $_SESSION['msg'] = "Número de teléfono no válido. El formato debe ser 999-999-9999.";
        return; // No continúes con el alta si el teléfono no es válido
    }

    // Si todas las validaciones son validas, damos de alta al cliente
    $db = AccesoDatos::getModelo();
    if ($db->addCliente($cli)) {
        $_SESSION['msg'] = "El usuario ".$cli->first_name." se ha dado de alta correctamente.";
    } else {
        $_SESSION['msg'] = "Error al dar de alta al usuario ".$cli->first_name."."; 
    }
}


function crudPostModificar(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $cli = new Cliente();

    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];	
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];

      // Obtenemos el cliente existente para comparar el correo electrónico
      $db = AccesoDatos::getModelo();
      $clienteExistente = $db->getCliente($cli->id);
      
      // Verificar si el correo electrónico ha cambiado
      if ($cli->email != $clienteExistente->email) {
          // Si ha cambiado, realizamos la validación del correo electrónico
          if (!validarEmail($cli->email)) {
              $_SESSION['msg'] = "Correo electrónico no válido o ya registrado.";
              return; // No continúes con la modificación si el correo no es válido
          }
      }

    // Validar dirección IP
    if (!validarIP($cli->ip_address)) {
        $_SESSION['msg'] = "Dirección IP no válida.";
        return; // No continúes con la modificación si la IP no es válida
    }

    // Validar número de teléfono
    if (!validarTelefono($cli->telefono)) {
        $_SESSION['msg'] = "Número de teléfono no válido. El formato debe ser 999-999-9999.";
        return; // No continúes con la modificación si el teléfono no es válido
    }

    // Si todas las validaciones son exitosas, procede a modificar el cliente
    $db = AccesoDatos::getModelo();
    if ($db->modCliente($cli)) {
        $_SESSION['msg'] = "El usuario ha sido modificado correctamente.";
    } else {
        $_SESSION['msg'] = "Error al modificar el usuario.";
    }
}


function validarEmail($email) {
    // Verificar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false; // Formato de correo electrónico no válido
    }

    // Consultar la base de datos para verificar si el correo electrónico ya está registrado
    $db = AccesoDatos::getModelo();
    $clienteExistente = $db->getClientePorEmail($email);
    if ($clienteExistente) {
        return false; // El correo electrónico ya está registrado
    }

    return true; // El correo electrónico es válido y no está repetido
}

function validarIP($ip) {
    // Verificar el formato de la dirección IP
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false; // Formato de IP no válido
    }

    return true; // La IP es válida
}

function validarTelefono($telefono) {
    // Verificar el formato del número de teléfono (999-999-9999)
    if (!preg_match("/^\d{3}-\d{3}-\d{4}$/", $telefono)) {
        return false; // Formato de teléfono no válido
    }

    return true; // El teléfono es válido
}

// Función para generar el PDF con los detalles del cliente
function generarPDF($cliente_id) {
    // Obtener el cliente utilizando el id proporcionado
    $db = AccesoDatos::getModelo();
    $cliente = $db->getCliente($cliente_id);

    // Verificar si se encontró el cliente
    if ($cliente) {
        // Crear una nueva instancia de mPDF
        $mpdf = new Mpdf();

        //Recoger la bandera
        // Obtener la URL de la bandera utilizando el método procesarInfoIP
        if (!$banderaUrl = obtenerBanderaDesdeCodigoPais($cliente->id))
        {
            $banderaUrl = "https://flagpedia.net/data/flags/w702/aq.webp";
        }


        //Recoger la imagen del cliente si no esta entre 1 y 10 el id
        if($cliente->id >= 1 && $cliente->id <= 10)
        {
            if($cliente->id < 10)
            {
                $imagenCli = "app/uploads/0000000" . $cliente->id . ".jpg";
            }
            else
            {
                $imagenCli = "app/uploads/000000" . $cliente->id . ".jpg";
            }
        }
        else
        {
            $imagenCli = generarImagenAleatoria($cliente->id);
        }

        //Establecer el título del documento
        $mpdf->SetTitle('Detalles del Cliente');
        

        // Agregar el contenido del PDF
        $html = '<h1>Detalles del Cliente</h1>';
        $html .= '<p>ID: ' . $cliente->id . '</p>';
        $html .= '<p>Nombre: ' . $cliente->first_name . ' ' . $cliente->last_name . '</p>';
        $html .= '<p>Correo Electrónico: ' . $cliente->email . '</p>';
        $html .= '<p>Género: ' . $cliente->gender . '</p>';
        $html .= '<p>Dirección IP: ' . $cliente->ip_address . '</p>';
        $html .= '<p>Teléfono: ' . $cliente->telefono . '</p>';
        $html .= '<img src="' . $banderaUrl . '" alt="Bandera">';
        $html .= '<img src="' . $imagenCli . '" alt="Bandera">';

        // Agregar el contenido al PDF
        $mpdf->WriteHTML($html);

        // Salida del PDF
        $mpdf->Output('detalles_cliente.pdf', 'I');
    } else {
        die('Cliente no encontrado.');
    }
}

function conseguirIP($id) {
    $db = AccesoDatos::getModelo();
    $cliente = $db->getCliente($id); // Obtener los detalles del cliente
    return $cliente->ip_address; // Devolver la dirección IP del cliente
}

function procesarInfoIP($id)
{
    $resultado = "";
    
    // Dirección IP que deseas consultar
    $ip = conseguirIP($id);

    // URL de la API de ip-api.com con la dirección IP como parámetro
    $url = "http://ip-api.com/json/{$ip}";

    // Realizar la solicitud a la API
    $response = file_get_contents($url);

    // Decodificar la respuesta JSON
    $data = json_decode($response);

    // Verificar si la solicitud fue exitosa y mostrar la información
    if ($data && $data->status == 'success') {
        $resultado = $data->countryCode;
        
    } else {
        echo "Sin bandera = Antartida.";
    }

    return $resultado;

}

function obtenerBanderaDesdeCodigoPais($id)
{
    $banderaUrl = false;

    if ($codigoPais = procesarInfoIP($id))
    {

        //Poner en minusculas las siglas ya que te las da en mayusuculas
        $codigoPais = strtolower($codigoPais);

        // Construir la URL de la imagen de la bandera usando el código de país
        $banderaUrl = "https://flagpedia.net/data/flags/w702/$codigoPais.webp";
    }
    
    return $banderaUrl;
}

//Función para generar la imagen aleatoria de robot del cliente
function generarImagenAleatoria($id) {

    // Codificar la cadena en MD5 para obtener una imagen única para cada cliente
    $hash = md5($id);

    // Construir la URL de la imagen aleatoria utilizando RoboHash
    $robot = "https://robohash.org/" . $hash . ".png";
    
    return $robot;
}

function verificarCredenciales(string $login, string $contraseña): bool {

    $accesoCorrecto = false;
    
    // "Descifrar" la contraseña con md5
    $contraDescif = md5($contraseña);

    $db = AccesoDatos::getModelo();
    $resu = $db->comprobarContra_Login($login, $contraDescif);
    
    // Verificar si se encontró un resultado
    if ($resu === true) {
        // Si se encontró una coincidencia, devuelve true
        $accesoCorrecto = true;
    }

    return $accesoCorrecto;
}

function conseguirRol($login):bool
{
    $rolConseguido = false;

    $db = AccesoDatos::getModelo();
    $resu = $db->recogerRol($login);

    //Verificar si se encontró un resultado
    if($resu !== false)
    {
        // si se encontró una coincidencia, devuelve true y se asigna a la variable de sesion el rol
        $rolConseguido = true;
        $_SESSION['rol'] = $resu;
    }

    return $rolConseguido;
}

// FUNCIONES PARA EL MANEJO DE LA SUBIDA Y MODIFICACIÓN DE LA IMAGEN
function chequeoImagen($datos,$imagen) : bool {
    //Comprobar si estamos en Modificar o Nuevo.
    //Caso 1 (Nuevo): Comprobamos el siguiente Auto Icrement del Id
    //Caso 2 (Modificar): Asiganamos a Id el que nos vino en el POST
    if ($datos['id'] == "") {
        $db = AccesoDatos::getModelo();
        $id = $db->siguienteId();
    } else {
        $id = $datos['id'];
    }

    //Comprobamos qué tipo de error nos puede dar
    $error = $imagen['imagen']['error'];
    if ($error != 4) {
        //El error 0 es caso de éxito por parte de CLIENTE. Si es distinto se avisa el error
        if($error != 0) {
            $_SESSION['msg'] = codErrorImagen($error);
            return false;
        }

        //Comprobar por parte de SERVIDOR. Si hay texto en $comprobar es que hay error
        $comprobar = comprobarImagen($imagen);
        if ($error == 0 && $comprobar != "") {
            $_SESSION['msg'] = $comprobar;
            return false;
        }

        //Si hay texto en $comprobar es que no se ha habido error en mover la imagen
        $comprobar = moverImagen($id,$imagen);
        if ($comprobar != "") {
            $_SESSION['msg'] = $comprobar;
            return false;
        }
        
    }

    return true;

}

//Función para asignar una foto de perfil
function imagenPerfil($id) : string {
    $resu = "";

    $plantilla = "00000000";
    $foto = substr($plantilla,0,-strlen($id)).$id;
    $ruta = file_exists(DIRIMAGEN."$foto.jpg") ? DIRIMAGEN."$foto.jpg" : DIRIMAGEN."$foto.png";

    if (file_exists($ruta)) {
        $resu = $ruta;
    } else {
        $resu = "https://robohash.org/$foto";
    }

    return $resu;
}

//Funcion Para mover una Imagen
function moverImagen($id,$imagen) : string {
    $nombre = generarNombreImagen($id,$imagen['imagen']['name']);
    $temporal = $imagen['imagen']['tmp_name'];
    $msg = '';


    if ( is_dir(DIRIMAGEN) && is_writable (DIRIMAGEN)) {
        if(file_exists(DIRIMAGEN.$nombre)) unlink(DIRIMAGEN.$nombre);
        if (move_uploaded_file($temporal,  DIRIMAGEN . $nombre) == false) {
            $msg .= 'La imagen no se ha guardado correctamente <br />';
        }
    } else {
        $msg .= 'ERROR: No es un directorio correcto o no se tiene permiso de escritura <br />';
    }

    return $msg;
}

//Funcion Axiliar para generar nombre de la imagen para alta ed nuevo usuario
function generarNombreImagen($id,$nombre) : string {
    $plantilla = "00000000";
    $foto = substr($plantilla,0,-strlen($id)).$id;
    $extencion = pathinfo($nombre,PATHINFO_EXTENSION);

    $resu = $foto. "." . $extencion ;

    //Compruebo si existe ya una foto en la carpeta de uploads
    $fotoRepe = glob(DIRIMAGEN.$foto . ".*");
    if(!empty($fotoRepe)) unlink($fotoRepe[0]);

    return $resu;
}

//Devolver el tipo de error
function codErrorImagen($error) : string {
    $codigosErrorSubida= [ 
        UPLOAD_ERR_OK         => 'Subida correcta',  // Valor 0
        UPLOAD_ERR_INI_SIZE   => 'El tamaño del archivo excede el admitido por el servidor',  // directiva upload_max_filesize en php.ini
        UPLOAD_ERR_FORM_SIZE  => 'El tamaño del archivo excede el admitido por el cliente',  // directiva MAX_FILE_SIZE en el formulario HTML
        UPLOAD_ERR_PARTIAL    => 'El archivo no se pudo subir completamente',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo para ser subido',
        UPLOAD_ERR_NO_TMP_DIR => 'No existe un directorio temporal donde subir el archivo',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo guardar el archivo en disco',  // permisos
        UPLOAD_ERR_EXTENSION  => 'Una extensión PHP evito la subida del archivo',  // extensión PHP
    
    ]; 

    return $codigosErrorSubida[$error];
}

//Hacer las diferentes comprobaciones a la imagen
function comprobarImagen($imagen) : string {
    $resu = '';

    $resu .= comprobarTamanio($imagen['imagen']['size']);
    $resu .= comprobarFormato($imagen['imagen']['type']);

    return $resu;
}

//Comprobar Tamaño de imagen
function comprobarTamanio($tamanio) : string {
    return ($tamanio > MAX_UPLOAD) ? "Tamaño Excedido en Imagen<br>" : "";
}

//Comprobar Formato Imagen
function comprobarFormato($formato) : string {
    return ($formato == "image/jpeg" || $formato == "image/png") ? "" : "Error Formato en Imagen";
}


?>