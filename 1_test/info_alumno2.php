<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta charset="utf-8" />
    <title>Desarrollo web en entorno servidor - Unidad 2</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
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
        <?php

            // Iniciar sesión
            session_start();
            
            // Conexion a la base de datos
            require_once("conectar_bd.php");

            // Saneamiento y vallidación de datos
            function filtrado($datos)
            { // Filtro de datos general
                $datos = trim($datos); // Elimina espacios antes y después de los datos
                $datos = stripslashes($datos); // Elimina backslashes \
                $datos = htmlspecialchars($datos); // Traduce caracteres especiales en entidades HTML
                return $datos;
            }

            if (empty($_POST["nombre"])) {
                $errores[]="No ha introducido nombre de usuario del alumno. Vuelva a la página de introducción de datos.";
            } else {
                $nombre=filtrado($_POST["nombre"]);
            }

            // Comprobacion de existencia de usuario y tipo
            $sql = "SELECT ID, ROL FROM USUARIOS WHERE USUARIO=?";
            $sth = $dbh -> prepare($sql);
            $sth -> execute(array($nombre));
            $resultado_usuario = $sth -> fetch(PDO::FETCH_ASSOC);

            if (empty($resultado_usuario)) {
                $errores[]="El usuario introducido no existe o no es correcto. Vuelva a la página de introducción de datos.";
            }
            if ($resultado_usuario["ROL"] == "ADM") {
                    $errores[]="El usuario introducido es un administrador. Vuelva a la página de introducción de datos.";
            }
            if (isset($errores)) { //Si se crea el array $errores es porque ha habido algún error y el bucle foreach imprime los errores
                foreach ($errores as $valor) {
                    echo nl2br($valor . "\n");
                } ?>
                <br /><a href="info_alumno.php" class="btn btn-primary">Información del alumno</a>    
            <?php
            } else { // Si no ha habido errores
                $_SESSION["usuario"] = $nombre;

                // Consulta del numero de intento maximo del usuario
                $sql = "SELECT MAX(INTENTO) AS INTENTO FROM RESULTADOS WHERE ID_USUARIOS=?";
                $sth = $dbh -> prepare($sql);
                $sth -> execute(array($resultado_usuario["ID"]));
                $resultado_intento = $sth -> fetch(PDO::FETCH_ASSOC);

                //Si el usuario no ha hecho aun ningun test, no habrá ningun informe que ofrecer
                if ($resultado_intento["INTENTO"] == NULL) {
                    ?>    
                    <p>El usuario <b><?php echo $nombre ?></b> aún no ha hecho ningún test.</p>
                    <p>Pulse sobre el botón "Información del alumno" para consultar informes de otro usuario o sobre el botón "Cerrar sesión" si así lo desea.</p>
                    <a href="info_alumno.php" class="btn btn-primary">Información del alumno</a>
                    <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
                <?php
                } else { // Si el usuario ha hecho algun intento, muestra los informes

                    // Obtención del contenido del test
                    $sql = "SELECT * FROM EXAMEN";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute();
                    $resultado_examen = $sth -> fetchAll();

                    // Obtención de la plantilla de respuestas
                    $sql = "SELECT SOL1, SOL2 FROM RESPUESTAS";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute();
                    $resultado_plantilla = $sth -> fetchAll();

                    //Listado de cada intento con su corrección
                    for ($i=1; $i <= $resultado_intento["INTENTO"]; $i++) {
                        ?>
                        <div class="container bg-white p-4" style="width:560px; border: 10px solid #0D6EFD">
                            <h3>Test nº <?php echo $i ?></h3><br />
                            <h4>Respuestas dadas por el usuario <b><?php echo $nombre ?></b>:</h4>
                            <?php

                            // Consulta de las respuestas dadas por el usuario para el intento $i
                            $sql = "SELECT RESP1, RESP2 FROM RESULTADOS WHERE ID_USUARIOS=? AND INTENTO=?";
                            $sth = $dbh -> prepare($sql);
                            $sth -> execute(array($resultado_usuario["ID"], $i));
                            $resultado_test = $sth -> fetchAll();

                        // Imprimir por pantalla las respuestas del usuario y corrección de las respuestas
                        for ($j=0; $j<10; $j++) {
                            if ($resultado_test[$j]["RESP1"] == NULL && $resultado_test[$j]["RESP2"] == NULL) {// Si el usuario no ha seleccionado ninguna opcion o ha seleccionado 3 opciones
                                    ?>
                                    <p><b><?php echo $resultado_examen[$j]["ID"] ?>.- <?php echo $resultado_examen[$j]["PREGUNTA"] ?></b></p>
                                    <i class="text-danger fas fa-times"></i><span> Mal. El usuario ha seleccionado 3 opciones o ninguna.</span><br /><br />
                                    <?php
                                } else {
                                    if ($resultado_test[$j]["RESP1"] == $resultado_plantilla[$j]["SOL1"] && $resultado_test[$j]["RESP2"] == $resultado_plantilla[$j]["SOL2"]) { // La respuesta está bien
                                        ?>
                                        <p><b><?php echo $resultado_examen[$j]["ID"] ?>.- <?php echo $resultado_examen[$j]["PREGUNTA"] ?></b></p><i class="text-success fas fa-check"></i><span> Bien</span>
                                        <ul>
                                            <li><?php echo $resultado_examen[$j]["OPC_" . $resultado_test[$j]["RESP1"]] ?></li>
                                            <?php
                                            if ($resultado_test[$j]["RESP2"] != NULL) {
                                                ?>
                                                <li><?php echo $resultado_examen[$j]["OPC_" . $resultado_test[$j]["RESP2"]] ?></li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                    <?php
                                    } else { // En cualquier otro caso, la respuesta está mal
                                        ?>
                                        <p><b><?php echo $resultado_examen[$j]["ID"] ?>.- <?php echo $resultado_examen[$j]["PREGUNTA"] ?></b></p><i class="text-danger fas fa-times"></i><span> Mal</span>
                                        <ul>
                                            <li><?php echo $resultado_examen[$j]["OPC_" . $resultado_test[$j]["RESP1"]] ?></li>
                                            <?php
                                            if ($resultado_test[$j]["RESP2"] != NULL) {
                                                ?>
                                                <li><?php echo $resultado_examen[$j]["OPC_" . $resultado_test[$j]["RESP2"]] ?></li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                    <?php
                                    }
                                }
                        }

                        // Cálculo de la nota
                        $sql = "SELECT COUNT(CORRECTA) AS NOTA FROM RESULTADOS WHERE INTENTO=? AND ID_USUARIOS=? AND CORRECTA=1";
                        $sth = $dbh -> prepare($sql);
                        $sth -> execute(array($i, $resultado_usuario["ID"]));
                        $resultado_nota = $sth -> fetch(PDO::FETCH_ASSOC);
                        $notas_intento[$i-1] = $resultado_nota["NOTA"]; // Para guardar la nota de cada intento en un array
                            ?>
                            <p>La nota obtenida en este test es un <b><?php echo $resultado_nota["NOTA"] ?></b></p>
                        </div> <br /><br />
                    <?php
                    } ?>
                    <h3><b>Estadísticas de usuario:</b></h3>
                    <?php

                    // Nota media
                    $suma_notas = 0;
                    for ($i=0; $i < count($notas_intento); $i++) {
                        $suma_notas += $notas_intento[$i];
                    }
                    $media = round($suma_notas / $resultado_intento["INTENTO"], 2); ?>
                    <p><b>Media</b></p>
                    <p>La nota media es un <b><?php echo $media ?></b></p>
                    <?php

                    // Moda
                    if (max(array_count_values($notas_intento)) == 1) { // Si cada nota del array solamente se repite una vez
                        ?>
                        <p><b>Moda</b></p>
                        <p>Todas las notas son distintas. No existe un valor para la moda.</p>
                        <?php
                    } else {
                        $moda = array_search(max(array_count_values($notas_intento)), array_count_values($notas_intento)); //  Busca el mayor valor dentro del array del numero de veces que se repite cada nota y devuelve su indice (que será la nota más repetida)
                        ?>
                        <p><b>Moda</b></p>
                        <p>La moda es <b><?php echo $moda ?></b></p>
                        <?php
                    }

                    //Varianza
                    $sumatoria = 0;
                    for ($i=0; $i < count($notas_intento); $i++) {
                        $sumatoria += pow(($notas_intento[$i] - $media), 2);
                    }
                    $varianza = round($sumatoria / count($notas_intento), 2); ?>
                    <p><b>Varianza</b></p>
                        <p>La varianza es <b><?php echo $varianza ?></b></p>
                    <?php

                    //Desviación típica
                    $desv_tipica = round(sqrt($varianza), 2); ?>
                    <p><b>Desviación típica</b></p>
                        <p>La desviación típica es <b><?php echo $desv_tipica ?></b></p>
                    
                    <?php

                    // Pregunta más veces bien respondida
                    $sql = "SELECT ID_EXAMEN, COUNT(CORRECTA) AS VECES_OK FROM RESULTADOS WHERE ID_USUARIOS=? AND CORRECTA=1 GROUP BY ID_EXAMEN ORDER BY VECES_OK DESC";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute(array($resultado_usuario["ID"]));
                    $resultado_vecesbien = $sth -> fetch(PDO::FETCH_ASSOC);

                    // Pregunta más veces mal respondida
                    $sql = "SELECT ID_EXAMEN, COUNT(CORRECTA) AS VECES_NOK FROM RESULTADOS WHERE ID_USUARIOS=? AND CORRECTA=0 GROUP BY ID_EXAMEN ORDER BY VECES_NOK DESC";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute(array($resultado_usuario["ID"]));
                    $resultado_vecesmal = $sth -> fetch(PDO::FETCH_ASSOC);
                    ?>
                    <p><b>Pregunta con más aciertos</b></p>
                    <?php
                    if (!$resultado_vecesbien) { // Si aún no hay ninguna pregunta contestada correctamente
                        ?>
                        <p>Aún no hay preguntas con más aciertos.</p>
                    <?php
                    } else {
                        ?>
                        <p>La pregunta con más aciertos para este usuario ha sido la número <b><?php echo $resultado_vecesbien["ID_EXAMEN"] ?></b></p>
                    <?php
                    } ?>
                    <p><b>Pregunta con más fallos</b></p>
                    <p>La pregunta con más fallos para este usuario ha sido la número <b><?php echo $resultado_vecesmal["ID_EXAMEN"] ?></b></p>
                    <br />
                    
                    <h3><b>Estadísticas totales:</b></h3>
                    <?php

                    // Número total de intentos por cada usuario, almacenados en la tabla RESULTADOS
                    $sql = "SELECT ID_USUARIOS, MAX(INTENTO) AS MAX_INTENTO FROM RESULTADOS GROUP BY ID_USUARIOS";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute();
                    $resultado_intentosporusuario = $sth -> fetchAll();
                    $total_intentos = 0;
                    foreach ($resultado_intentosporusuario as list($usr, $maxintento)) {
                        $total_intentos += $maxintento; // Total de intentos
                    }

                    // Nota de cada intento de cada usuario
                    $sql = "SELECT ID_USUARIOS, INTENTO, COUNT(CORRECTA) AS NOTA FROM RESULTADOS WHERE CORRECTA=1 GROUP BY ID_USUARIOS, INTENTO";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute();
                    $resultado_notasporusuario = $sth -> fetchAll();

                    //Nota media
                    $suma_notastotal = 0;
                    for ($i=0; $i < $total_intentos; $i++) {
                        if (!$resultado_notasporusuario || $resultado_notasporusuario==NULL) { // Si aún no hay ninguna pregunta contestada correctamente
                            $resultado_notasporusuario = array();
                            $i = $total_intentos; // Para que salga del bucle
                        } else {
                            $suma_notastotal += $resultado_notasporusuario[$i]["NOTA"];
                        }
                    }
                    $media_total = round($suma_notastotal / $total_intentos, 2); ?>
                    <p><b>Media</b></p>
                    <p>La nota media es un <b><?php echo $media_total ?></b></p>
                    <?php

                    // Moda
                    //Paso las notas del array $resultado_notasporusuario a un array que contenga solamente las notas
                    if ($resultado_notasporusuario==NULL) { // Si aún no hay ninguna pregunta contestada correctamente
                        ?>
                        <p><b>Moda</b></p>
                        <p>El valor de la moda es <b>0</b></p>
                    <?php
                    }
                    else {
                        for ($i=0; $i < count($resultado_notasporusuario); $i++) {
                            $array_notastotales[$i] = $resultado_notasporusuario[$i]["NOTA"];
                        }
                        if (max(array_count_values($array_notastotales)) == 1) { // Si cada nota del array solamente se repite una vez
                            ?>
                            <p><b>Moda</b></p>
                            <p>Todas las notas son distintas. No existe un valor para la moda.</p>
                            <?php
                        } else {
                            $moda = array_search(max(array_count_values($array_notastotales)), array_count_values($array_notastotales)); //  Busca el mayor valor dentro del array del numero de veces que se repite cada nota y devuelve su indice (que será la nota más repetida)
                            ?>
                            <p><b>Moda</b></p>
                            <p>La moda es <b><?php echo $moda ?></b></p>
                            <?php
                        }
                    }

                    //Varianza
                    $sumatoria_total = 0;
                    for($i=0; $i < $total_intentos; $i++) {
                        if ($resultado_notasporusuario==NULL) { // Si aún no hay ninguna pregunta contestada correctamente
                            $i = $total_intentos; // Para que salga del bucle
                        }
                        else {
                            $sumatoria_total += pow($resultado_notasporusuario[$i]["NOTA"] - $media_total, 2);
                        }
                    }
                    $varianza_total = round($sumatoria_total / $total_intentos, 2);
                    ?>
                    <p><b>Varianza</b></p>
                        <p>La varianza es <b><?php echo $varianza_total ?></b></p>
                    <?php

                    //Desviación típica
                    $desv_tipicatotal = round(sqrt($varianza_total), 2);
                    ?>
                    <p><b>Desviación típica</b></p>
                    <p>La desviación típica es <b><?php echo $desv_tipicatotal ?></b></p>
                    <?php

                    // Pregunta más veces bien respondida
                    $resultado_vecesbientotal = array();
                    $sql = "SELECT ID_EXAMEN, COUNT(CORRECTA) AS VECES_OK FROM RESULTADOS WHERE CORRECTA=1 GROUP BY ID_EXAMEN ORDER BY VECES_OK DESC";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute();
                    $resultado_vecesbientotal = $sth -> fetch(PDO::FETCH_ASSOC);

                    // Pregunta más veces mal respondida
                    $sql = "SELECT ID_EXAMEN, COUNT(CORRECTA) AS VECES_NOK FROM RESULTADOS WHERE CORRECTA=0 GROUP BY ID_EXAMEN ORDER BY VECES_NOK DESC";
                    $sth = $dbh -> prepare($sql);
                    $sth -> execute();
                    $resultado_vecesmaltotal = $sth -> fetch(PDO::FETCH_ASSOC);
                    ?>
                    <p><b>Pregunta con más aciertos</b></p>
                    <?php
                    if (!$resultado_vecesbientotal) { // Si aún no hay ninguna pregunta contestada correctamente
                        ?>
                        <p>Aún no hay preguntas con más aciertos.</p>
                    <?php
                    } else {
                        ?>
                        <p>La pregunta con más aciertos ha sido la número <b><?php echo $resultado_vecesbientotal["ID_EXAMEN"] ?></b></p>
                    <?php
                    }
                    ?>
                    <p><b>Pregunta con más fallos</b></p>
                    <p>La pregunta con más fallos ha sido la número <b><?php echo $resultado_vecesmaltotal["ID_EXAMEN"] ?></b></p>
                    <br />
                    <p>Pulse sobre el botón "Información del alumno" para consultar informes de otro usuario o sobre el botón "Cerrar sesión" si así lo desea.</p>
                    <a href="info_alumno.php" class="btn btn-primary">Información del alumno</a>
                    <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
                    <?php
                }
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