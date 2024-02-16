<?php
require './vendor/autoload.php';
use Mpdf\Mpdf;

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


?>