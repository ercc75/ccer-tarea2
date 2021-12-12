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
<?php session_start(); ?>
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
        <h1>Test online</h1>
        <p style="font-weight: bold">Reglas de cumplimentación del test:</p>
            <ul>
                <li>Las preguntas no contestadas no penalizan nota, simplemente no puntuan</li>
                <li>Tenga en cuenta que hay preguntas con más de una respuesta</li>
                <li>Las preguntas con más de una respuesta solamente puntuan si se seleccionan las dos respuestas correctas</li>
                <li>Dispone de un máximo de <b>3 intentos</b> para realizar el test</li>
            </ul>
            <p><b>Usuarios creados:</b></p>
            <p class="text-primary">Administrador -> admin  (contraseña 1234)</p>
            <p class="text-primary">Usuario1 sin ningún test hecho -> usuario1  (contraseña 123456789)</p>
            <p class="text-primary">Usuario2 con 2 tests hechos -> usuario2  (contraseña 987654321)</p>
            <p class="text-primary">Usuario3 con 3 tests hechos -> usuario3  (contraseña 0102030405)</p>
            <?php
                if (!isset($_SESSION) || empty($_SESSION)) {
            ?>
                <p>Bienvenido, <b>invitado</b>:</p>
                <p>Inicie sesión como usuario o administrador. Si aún no está dado de alta, por favor regístrese.</p>
                <p>Tenga en cuenta las siguientes reglas funcionales, exclusivas para cada tipo de usuario:</p>
                <ul>
                    <li>Como <b>administrador</b>, podrá comprobar las notas y respuestas de cada alumno, así como generar informes</li>
                    <li>Como <b>usuario</b>, únicamente podrá realizar la cumplimentación del test online</li>
                </ul>
                <a href="iniciar_sesion.php" class="btn btn-primary">Iniciar sesión</a>
                <a href="registro.php" class="btn btn-primary">Registrarse</a>
            <?php
                }
                else { //Si ya había una sesión iniciada
            ?>        
                <p>Hola de nuevo, <b><?php echo $_SESSION["usuario"] ?></b>:</p>
                <p>Tiene una sesión iniciada. Por precaución, <b>cierre la sesión</b>.</p>
                <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>    
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