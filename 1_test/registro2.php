<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="utf-8" />
    <title>Desarrollo web en entorno servidor - Unidad 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
</head>
<body>
    <header>
        <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../inicio.html">DWES - Unidad 2</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="inicio.php">Ejercicio 1 - Test online</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Ejercicio 2 - Reservas online de coches</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="##">Ejercicio 3 - Pizzería online</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="container" style="padding-top: 80px; padding-bottom: 30px; height: 90vh; overflow-y: auto">
        <h1>Test online</h1><br />
        <h3>Registrarse</h3>
        <?php

            // Iniciar sesión
            session_start();
            
            // Conexion a la base de datos
            require_once("conectar_bd.php");

            // Saneamiento y vallidación de datos
            function filtrado($datos) { // Filtro de datos general
                $datos = trim($datos); // Elimina espacios antes y después de los datos
                $datos = stripslashes($datos); // Elimina backslashes \
                $datos = htmlspecialchars($datos); // Traduce caracteres especiales en entidades HTML
                return $datos;
            }
            if (empty($_POST["nombre"])) {
                $errores[]="No ha introducido nombre de usuario. Vuelva a la página de registro.";
            }
            if (empty($_POST["contrasena"])) {
                $errores[]="No ha introducido contraseña. Vuelva a la página de registro.";
            }
            if (empty($_POST["correo"])) {
                $errores[]="No ha introducido el correo electrónico. Vuelva a la página de registro.";
            }
            else {
                $nombre=filtrado($_POST["nombre"]);
                $contrasena=filtrado($_POST["contrasena"]);
                $correo=filtrado($_POST["correo"]);
                $valor_contrasena=preg_match("/^.{8,}/i", $contrasena, $resultados_contrasena); // Expresión regular para que la contraseña sea igual o superior a 8 caracteres
                $correo_sanitized=filter_var($correo, FILTER_SANITIZE_EMAIL);
                $valor_correo=filter_var($correo_sanitized, FILTER_VALIDATE_EMAIL);
                if (!$valor_contrasena) {
                    $errores[]="La contraseña introducida es inferior a 8 caracteres. Vuelva a la página de registro.";
                }
                if (!$valor_correo) {
                    $errores[]="El dato del correo introducido no es correcto. Vuelva a la página de registro.";
                }
            }

            // Comprobacion de existencia de usuario
            $sql = "SELECT ID FROM USUARIOS WHERE USUARIO=?";
            $sth = $dbh -> prepare($sql);
            $sth -> execute(array($nombre));
            $resultado_usuario = $sth -> fetch(PDO::FETCH_ASSOC);

            // Comprobacion de existencia de correo electronico
            $sql = "SELECT ID FROM USUARIOS WHERE MAIL=?";
            $sth = $dbh -> prepare($sql);
            $sth -> execute(array($correo));
            $resultado_correo = $sth -> fetch(PDO::FETCH_ASSOC);
            if (!empty($resultado_usuario)) {
                $errores[]="Usuario ya existente. Vuelva a la página de registro.";
            }
            if (!empty($resultado_correo)) {
                $errores[]="Correo electrónico ya existente. Vuelva a la página de registro.";
            }
            if (isset($errores)) { //Si se crea el array $errores es porque ha habido algún error y el bucle foreach imprime los errores
                foreach ($errores as $valor) {
                    echo nl2br($valor . "\n");
                }
            ?>
                <br /><a href="registro.php" class="btn btn-primary">Registrarse</a>    
        <?php
            }
            else { // Si no ha habido errores
                $sql = "INSERT INTO USUARIOS(USUARIO, CONTRASENA, MAIL, ROL) VALUES(?, ?, ?, ?)";
                $sth = $dbh -> prepare($sql);
                $sth -> execute(array($nombre, $contrasena, $correo, "USR"));
        ?>
                <p>El usuario <b><?php echo $nombre?></b> ha sido registrado con éxito.</p>
                <p>Si lo desea, pulse el botón "Iniciar sesión"</p>
                <a href="iniciar_sesion.php" class="btn btn-primary">Iniciar sesión</a>
        <?php
            }
        ?>
    </div>
    <footer>
        <div class="container-fluid text-center p-3" style="background-color: lightgrey;">
            <p> Eduardo Rafael Cañizares Caballero - 2º DAWS - 2021</p>
        </div>
    </footer>
</body>
</html>