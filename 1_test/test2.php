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
        <h3>Test online PHP</h3>
        <?php

            // Iniciar sesión
            session_start();
            
            // Conexion a la base de datos
            require_once("conectar_bd.php");
            
            /*
            Valores del usuario almacenados en $_SESSION de iniciar_sesion2.php, en caso de que el usuario empiece a hacer el test despues de iniciar la sesion
                $_SESSION["id_usuario"]
                $_SESSION["usuario"]
                $_SESSION["contrasena"]
            */

            // Obtención del numero de intento del usuario
            $sql = "SELECT MAX(INTENTO) AS INTENTO FROM RESULTADOS WHERE ID_USUARIOS=(SELECT ID FROM USUARIOS WHERE USUARIO=?)";
            $sth = $dbh -> prepare($sql);
            $sth -> execute(array($_SESSION["usuario"]));
            $resultado_intento = $sth -> fetch(PDO::FETCH_ASSOC);
            //Si el usuario no ha hecho aún ningún intento, ese id_usuarios no existe aún en la tabla resultados, con lo que le asignaremos un valor 0 para evitar un error en tiempo de ejecucion
            if ($resultado_intento["INTENTO"] == NULL){
                $resultado_intento["INTENTO"] = 0;
            }

            // Preparar la instruccion de insercion de los datos en la base de datos
            $sqli = "INSERT INTO RESULTADOS(ID_USUARIOS, INTENTO, ID_EXAMEN, RESP1, RESP2, CORRECTA) VALUES(?, ?, ?, ?, ?, ?)";

            // Preparar la instruccion de corrección de las preguntas
            $sqlc = "SELECT SOL1, SOL2 FROM RESPUESTAS WHERE ID_EXAMEN=?";

            // Captura de las respuestas del formulario
            for ($i=1; $i <= 10; $i++) {
                if (!isset($_POST["resp" . $i]) || !isset($_POST["resp" . $i])) { // Si la variable o array de la pregunta $i no está definida, es que el alumno la ha dejado en blanco
                    ${"resp" . $i . "no_valida"}[0] = NULL;
                    ${"resp" . $i . "no_valida"}[1] = NULL;
                }
                elseif (is_array($_POST["resp" . $i]) && count($_POST["resp" . $i]) == 3) { // Si el array tiene 3 elementos, es que el alumno ha seleccionado 3 respuestas
                    ${"resp" . $i . "no_valida"}[0] = NULL;
                    ${"resp" . $i . "no_valida"}[1] = NULL;
                }
                elseif (is_array($_POST["resp" . $i])) { // Si la respuesta es multiple, la respuesta está almacenada en un array resp$i[]
                    if (count($_POST["resp" . $i]) == 2) { // Si el array tiene 2 elementos, es porque el alumno ha respondido las dos respuestas multiples
                        ${"resp" . $i}[0] = array_slice($_POST["resp" . $i], 0, 1);
                        ${"resp" . $i}[1] = array_slice($_POST["resp" . $i], 1, 1);
                    }
                    else { // Si el array tiene 1 elemento, es porque el alumno ha respondido una de las dos respuestas multiples
                        ${"resp" . $i}[0] = array_slice($_POST["resp" . $i], 0, 1);
                    }
                }
                else { // La respuesta está almacenada en una variable resp$i
                    ${"resp" . $i} = $_POST["resp" . $i];
                }
            }

            // Guardar las variables o arrays recogidos del formulario en la base de datos
            for ($i=1; $i <= 10; $i++) {
                if (isset(${"resp" . $i . "no_valida"})) { //La respuesta no es valida porque el alumno ha seleccionado 3 respuestas o porque no haya selecionado ninguna
                    // No hay nada que corregir, la pregunta directamente está respondida incorrectamente
                    $corregida = 0; 
                    // Introduccion en la base de datos de las contestaciones del alumno a la pregunta $i contestada incorrectamente
                    $sthi = $dbh -> prepare($sqli);
                    $sthi -> execute(array($_SESSION["id_usuario"], ($resultado_intento["INTENTO"] + 1), $i, NULL, NULL, $corregida));
                }
                elseif (is_array(${"resp" . $i})) { // Si la respuesta es multiple, la respuesta está almacenada en un array resp$i[]
                    // Obtención de las respuestas correctas de la pregunta $i
                    $sthc = $dbh -> prepare($sqlc);
                    $sthc -> execute(array($i));
                    $resultado_plantilla = $sthc -> fetch(PDO::FETCH_ASSOC);
                    // Corrección de la pregunta $i, teniendo en cuenta que una respuesta múltiple estará bien si las dos respuestas son correctas
                    if (in_array(implode(${"resp" . $i}[0]), $resultado_plantilla, true) && count(${"resp" . $i}) == 2) { // La primera de las dos respuestas múltiples es correcta
                        if (in_array(implode(${"resp" . $i}[1]), $resultado_plantilla, true)) { // Como la primera respuesta múltiple está bien, tengo que evaluar la segunda respuesta
                            $corregida = 1; // La pregunta está correctamente respondida
                            // Introduccion en la base de datos de las contestaciones del alumno a la pregunta $i contestada correctamente
                            $sthi = $dbh -> prepare($sqli);
                            $sthi -> execute(array($_SESSION["id_usuario"], ($resultado_intento["INTENTO"] + 1), $i, implode(${"resp" . $i}[0]), implode(${"resp" . $i}[1]), $corregida));
                        } else {
                            $corregida = 0; // La pregunta no está respondida correctamente
                            // Introduccion en la base de datos de las contestaciones del alumno a la pregunta $i contestada incorrectamente
                            $sthi = $dbh -> prepare($sqli);
                            $sthi -> execute(array($_SESSION["id_usuario"], ($resultado_intento["INTENTO"] + 1), $i, implode(${"resp" . $i}[0]), implode(${"resp" . $i}[1]), $corregida));
                        }
                    }
                    elseif (!in_array(implode(${"resp" . $i}[0]), $resultado_plantilla, true) && count(${"resp" . $i}) == 2) { // Si la primera respuesta multiple es incorrecta, la pregunta en su totalidad es incorrecta
                        $corregida = 0;
                        // Introduccion en la base de datos de las contestaciones del alumno a la pregunta $i contestada incorrectamente
                        $sthi = $dbh -> prepare($sqli);
                        $sthi -> execute(array($_SESSION["id_usuario"], ($resultado_intento["INTENTO"] + 1), $i, implode(${"resp" . $i}[0]), implode(${"resp" . $i}[1]), $corregida));
                    }
                    else { // El resto de los casos es que el array solamente tenga 1 elemento, con lo que la pregunta en su totalidad no está respondida correctamente
                        $corregida = 0;
                        // Introduccion en la base de datos de las contestaciones del alumno a la pregunta $i contestada incorrectamente
                        $sthi = $dbh -> prepare($sqli);
                        $sthi -> execute(array($_SESSION["id_usuario"], ($resultado_intento["INTENTO"] + 1), $i, implode(${"resp" . $i}[0]), NULL, $corregida));
                    }    
                }
                else { //Si la respuesta no es multiple, estará almacenada en una variable resp$i
                    // Obtención de las respuestas correctas de la pregunta $i
                    $sthc = $dbh -> prepare($sqlc);
                    $sthc -> execute(array($i));
                    $resultado_plantilla = $sthc -> fetch(PDO::FETCH_ASSOC);
                    // Corrección de la pregunta $i
                    if (in_array(${"resp" . $i}, $resultado_plantilla, true)) {// La pregunta está correctamente respondida
                        $corregida = 1;
                        // Introduccion en la base de datos de la contestacion del alumno a la pregunta $i contestada correctamente
                        $sthi = $dbh -> prepare($sqli);
                        $sthi -> execute(array($_SESSION["id_usuario"], ($resultado_intento["INTENTO"] + 1), $i, ${"resp" . $i}, NULL, $corregida));
                    }
                    else { // La pregunta no está respondida correctamente
                        $corregida = 0;
                        // Introduccion en la base de datos de la contestacion del alumno a la pregunta $i contestada incorrectamente
                        $sthi = $dbh -> prepare($sqli);
                        $sthi -> execute(array($_SESSION["id_usuario"], ($resultado_intento["INTENTO"] + 1), $i, ${"resp" . $i}, NULL, $corregida));
                    }
                }
            }
        if (($resultado_intento["INTENTO"] + 1) == 3) { // Si el usuario ha hecho ya los 3 tests
            ?>
            <p>El test ha sido enviado con éxito. Ha realizado su último intento.</p>
            <a href="cerrar_sesion.php" class="btn btn-success">Cerrar sesión</a>
        <?php
        }
        else { // Si al usuario todavía le quedan intentos de tests
            ?>
            <p>El test ha sido enviado con éxito.</p>
            <p>Puede cerrar la sesión o hacer un nuevo intento de examen si así lo desea.</p>
            <a href="test.php" class="btn btn-primary">Comenzar test</a>
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