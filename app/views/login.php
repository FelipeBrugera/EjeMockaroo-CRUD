<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inicio de Sesión</title>
    <link href="web/css/default.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="container">
        <div id="header">
        <h2>Iniciar Sesión</h2>
        </div>
        <form method="POST">
            <div id="content">
            <div>
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</body>
</html>



