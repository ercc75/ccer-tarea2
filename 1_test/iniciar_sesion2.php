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
        <h3>Iniciar sesión</h3>
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
                $errores[]="No ha introducido nombre de usuario. Vuelva a la página de inicio de sesión.";
            }
            if (empty($_POST["contrasena"])) {
                $errores[]="No ha introducido contraseña. Vuelva a la página de inicio de sesión.";
            }
            else {
                $nombre=filtrado($_POST["nombre"]);
                $contrasena=filtrado($_POST["contrasena"]);
            }

            // Comprobacion de existencia de usuario
            $sql = "SELECT ID FROM USUARIOS WHERE USUARIO=?";
            $sth = $dbh -> prepare($sql);
            $sth -> execute(array($nombre));
            $resultado_usuario = $sth -> fetch(PDO::FETCH_ASSOC);

            // Comprobacion de existencia de contraseña
            $sql = "SELECT ID FROM USUARIOS WHERE CONTRASENA=?";
            $sth = $dbh -> prepare($sql);
            $sth -> execute(array($contrasena));
            $resultado_contrasena = $sth -> fetch(PDO::FETCH_ASSOC);
            if (empty($resultado_usuario)) {
                $errores[]="El usuario introducido no existe o no es correcto. Vuelva a la página de inicio de sesión.";
            }
            if (empty($resultado_contrasena) && !empty($resultado_usuario)) {
                $errores[]="La contraseña introducida para ese usuario no es correcta. Vuelva a la página de inicio de sesión.";
            }
            if (isset($errores)) { //Si se crea el array $errores es porque ha habido algún error y el bucle foreach imprime los errores
                foreach ($errores as $valor) {
                    echo nl2br($valor . "\n");
                }
            ?>
                <br /><a href="iniciar_sesion.php" class="btn btn-primary">Iniciar sesión</a>    
            <?php
            }
            else { // Si no ha habido errores
                $_SESSION["id_usuario"] = $resultado_usuario["ID"];
                $_SESSION["usuario"] = $nombre;
                $_SESSION["contrasena"] = $contrasena;

                // Comprobacion del rol de usuario
                $sql = "SELECT ROL FROM USUARIOS WHERE USUARIO=?";
                $sth = $dbh -> prepare($sql);
                $sth -> execute(array($nombre));
                $resultado_rol = $sth -> fetch(PDO::FETCH_ASSOC);

                // Si el usuario es administrador, no podrá hacer examenes
                if ($resultado_rol["ROL"] == "ADM") {
                ?>    
                    <p>Bienvenido, <b><?php echo $nombre?></b></p>
                    <p>Pulse el botón "Información del alumno" para ver notas/generar informes o "Cerrar sesión" si así lo desea</p>
                    <a href="info_alumno.php" class="btn btn-primary">Información del alumno</a>
                    <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
                <?php
                }
                
                // Comprobacion del número de intento del usuario si no es administrador
                if ($resultado_rol["ROL"] == "USR") {
                    $sql = "SELECT MAX(INTENTO) AS INTENTO FROM RESULTADOS WHERE ID_USUARIOS=?";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute(array($resultado_usuario["ID"]));
                    $resultado_intento = $sth -> fetch(PDO::FETCH_ASSOC);
                    //Si el usuario no administrador no ha hecho aún ningún intento, ese id_usuarios no existe aún en la tabla resultados, con lo que le asignaremos un valor 0 para evitar un error en tiempo de ejecucion
                    if ($resultado_intento["INTENTO"] == NULL) {
                        $resultado_intento["INTENTO"] = 0;
                    }
                }

                // Si el usuario no es administrador y el intento es inferior a 3, el usuario podrá hacer un nuevo test
                if ($resultado_rol["ROL"] == "USR" && $resultado_intento["INTENTO"] < 3) {
                ?>
                    <p>Bienvenido, <b><?php echo $nombre?></b></p>
                    <p>Pulse el botón "Comenzar test" o "Cerrar sesión" si así lo desea.</p>
                    <a href="test.php" class="btn btn-primary">Comenzar test</a>
                    <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
                <?php
                    }
                
                // Si el usuario no es administrador y ya ha hecho tres tests, no podrá hacer más intentos
                if ($resultado_rol["ROL"] == "USR" && $resultado_intento["INTENTO"] == 3) {
                ?>
                    <p>Bienvenido, <b><?php echo $nombre?></b></p>
                    <p>Ha hecho uso de sus 3 intentos para realizar el test. Pulse "Cerrar sesión".</p>
                    <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
                <?php    
                    }
                ?>
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