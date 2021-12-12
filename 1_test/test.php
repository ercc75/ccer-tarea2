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
        <div class="container bg-white p-4" style="width:560px; border: 10px solid #0D6EFD">
            <h3>Test online PHP</h3>
            <?php

            // Iniciar sesión
            session_start();
            
            // Conexion a la base de datos
            require_once("conectar_bd.php");

            // Comprobacion del número de intentos
            $sql = "SELECT MAX(INTENTO) AS INTENTO FROM RESULTADOS WHERE ID_USUARIOS=?";
            $sth = $dbh -> prepare($sql);
            $sth -> execute(array($_SESSION["id_usuario"]));
            $resultado_intento = $sth -> fetch(PDO::FETCH_ASSOC);

            //Si el usuario no ha hecho aún ningún intento, ese id_usuarios no existe aún en la tabla resultados, con lo que le asignaremos un valor 0 para evitar un error en tiempo de ejecucion
            if ($resultado_intento["INTENTO"] == NULL){
                $resultado_intento["INTENTO"] = 0;
            }

            // Si el usuario ya ha hecho tres tests, no podrá hacer más intentos
            if ($resultado_intento["INTENTO"] == 3) {
                ?>
                <p>Hola, <b><?php echo $_SESSION["usuario"]?></b></p>
                    <p>Ha hecho uso de sus 3 intentos para realizar el test. Pulse "Cerrar sesión".</p>
                    <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
            <?php
            }    
            else { // Si el usuario ha hecho ya algún intento
                ?>
                <p style="text-align:right"><b><?php echo $_SESSION["usuario"]?></b>, este es su intento nº <b><?php echo ($resultado_intento["INTENTO"] + 1) ?></b></p>
                <?php

                // Obtención del contenido del test
                $sql = "SELECT * FROM EXAMEN";
                $sth = $dbh -> prepare($sql);
                $sth -> execute();
                $resultado_examen = $sth -> fetchAll();
                ?>
                <form action="test2.php" method="post">
                <?php
                for ($i=0; $i < 10; $i++) {
                        if ($resultado_examen[$i]["RESP_MULTI"]) {
                            ?><br /><p><b><?php echo $resultado_examen[$i]["ID"] ?>.- <?php echo $resultado_examen[$i]["PREGUNTA"] ?></b></p>
                            <input type="checkbox" class="form-check-input" name="resp<?php echo $i+1 ?>[]" value="A"> <?php echo $resultado_examen[$i]["OPC_A"] ?></input><br/>
                            <input type="checkbox" class="form-check-input" name="resp<?php echo $i+1 ?>[]" value="B"> <?php echo $resultado_examen[$i]["OPC_B"] ?></input><br/>
                            <input type="checkbox" class="form-check-input" name="resp<?php echo $i+1 ?>[]" value="C"> <?php echo $resultado_examen[$i]["OPC_C"] ?></input><br/>
                    <?php
                        } else {
                            ?><br /><p><b><?php echo $resultado_examen[$i]["ID"] ?>.- <?php echo $resultado_examen[$i]["PREGUNTA"] ?></b></p>
                            <input type="radio" class="form-check-input" name="resp<?php echo $i+1 ?>" value="A"> <?php echo $resultado_examen[$i]["OPC_A"] ?></input><br/>
                            <input type="radio" class="form-check-input" name="resp<?php echo $i+1 ?>" value="B"> <?php echo $resultado_examen[$i]["OPC_B"] ?></input><br/>
                            <input type="radio" class="form-check-input" name="resp<?php echo $i+1 ?>" value="C"> <?php echo $resultado_examen[$i]["OPC_C"] ?></input><br/>
                    <?php
                        }
                    } ?>
                    <br />
                    <button type="submit" class="btn-primary">Enviar</button>
                </form>
            <?php
            }
            ?>    
            <br /><br /><p style="text-align:center">Pulse el botón "Cerrar sesión" si no quiere enviar el resultado del test.</p>
            <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
        </div>
    </div>
    <footer>
        <div class="container-fluid text-center p-3" style="background-color: lightgrey;">
            <p> Eduardo Rafael Cañizares Caballero - 2º DAWS - 2021</p>
        </div>
    </footer>
</body>
</html>