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
<?php
// Iniciar sesion
session_start();
?>
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
            if (!empty($_SESSION) || isset($_)) {
        ?>
            <p>Hola de nuevo, <b><?php echo $_SESSION["usuario"] ?></b>:</p>
            <p>Tiene una sesión iniciada. Por precaución, <b>cierre la sesión</b>.</p>
            <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a> 
        <?php
            }
            else { // Si no había una sesión iniciada
                ?>        
        <div class="bg-white p-3 col-4">
            <form action="registro2.php" method="post">
                <input type="text" class="form-control" name="nombre" placeholder="Nombre de usuario" required="required" /><br />
                <input type="password" class="form-control" name="contrasena" placeholder="Contraseña (igual o superior a 8 caracteres)" required="required" /><br />
                <input type="email" class="form-control" name="correo" placeholder="Correo electrónico" required="required" /><br />
                <button type="submit" class="btn-primary">Enviar</button>                
            </form>
        </div>
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