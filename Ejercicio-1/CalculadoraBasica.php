<!DOCTYPE html>

<html lang="es-ES">

<head>
    <title>Ejercicio 3</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="CalculadoraBasica.css" />
</head>

<body>
    <h1>CalculadoraBasica</h1>
    <p>Una calculadora basica</p>
    <main>
        <div class="section">
            <?php

            session_start();

            if(!isset($_SESSION['pantalla'])) {
                $_SESSION['pantalla'] = "0";
            }

            if(!isset($_SESSION['memoria'])) {
                $_SESSION['memoria'] = 0;
            }

            class Calculadora {
            
                function __construct() {

                }
            
                function digitos($digito) {
                    $this->meterEnPantalla($digito);
                }
            
                function punto() {
                    $this->meterEnPantalla('.');
                }
            
                function suma() {
                    $this->meterEnPantalla('+');
                }
            
                function resta() {
                    $this->meterEnPantalla('-');
                }
            
                function multiplicacion() {
                    $this->meterEnPantalla('*');
                }
            
                function division() {
                    $this->meterEnPantalla('/');
                }
            
                function mrc() {
                    $_SESSION['pantalla'] = $_SESSION['memoria'];
                }
            
                function mMenos() {
                    $_SESSION['memoria'] -= $this->calcular("0");
                }
            
                function mMas() {
                    $_SESSION['memoria'] += $this->calcular("0");
                }
            
                function borrar() {
                    $_SESSION['pantalla'] = "0";
                }
            
                function igual() {
                    $_SESSION['pantalla'] = $this->calcular($_SESSION['pantalla']);
                }
            
                private function meterEnPantalla($valor) {
                    $_SESSION['pantalla'] = $_SESSION['pantalla'] == "0" ? $valor : $_SESSION['pantalla'] . $valor;
                }
            
                private function calcular($retornoSiHayError) {
                    try {
                        return eval("return " . $_SESSION['pantalla'] . ";");
                    } catch(ParseError $e) {
                        return $retornoSiHayError;
                    }
                }
            
            }

            $calculadora = new Calculadora();

            if (count($_POST) > 0) 
            {   
                // memoria
                if(isset($_POST["mrc"])) $calculadora->mrc();
                if(isset($_POST["m-"])) $calculadora->mMenos();
                if(isset($_POST["m+"])) $calculadora->mMas();

                // operadores
                if(isset($_POST["/"])) $calculadora->division();
                if(isset($_POST["*"])) $calculadora->multiplicacion();
                if(isset($_POST["-"])) $calculadora->resta();
                if(isset($_POST["+"])) $calculadora->suma();
                if(isset($_POST["="])) $calculadora->igual();

                // simbolos
                if(isset($_POST["punto"])) $calculadora->punto();
                if(isset($_POST["C"])) $calculadora->borrar();

                // digitos
                if(isset($_POST["1"])) $calculadora->digitos("1");
                if(isset($_POST["2"])) $calculadora->digitos("2");
                if(isset($_POST["3"])) $calculadora->digitos("3");
                if(isset($_POST["4"])) $calculadora->digitos("4");
                if(isset($_POST["5"])) $calculadora->digitos("5");
                if(isset($_POST["6"])) $calculadora->digitos("6");
                if(isset($_POST["7"])) $calculadora->digitos("7");
                if(isset($_POST["8"])) $calculadora->digitos("8");
                if(isset($_POST["9"])) $calculadora->digitos("9");
                if(isset($_POST["0"])) $calculadora->digitos("0");
            }

            echo "<input type='text' id='display' title='display' value='" . $_SESSION['pantalla'] . "' disabled />";
            ?>

            <form action='#' method='post' name='botones'>
                <!-- Row 1 -->
                <input type="submit" value="mrc" class="dark-gray" name="mrc" />
                <input type="submit" value="m-" class="dark-gray" name="m-" />
                <input type="submit" value="m+" class="dark-gray" name="m+" />
                <input type="submit" value="/" class="red black" name="/" />

                <!-- Row 2 -->
                <input type="submit" value="7" class="gray" name="7" />
                <input type="submit" value="8" class="gray" name="8" />
                <input type="submit" value="9" class="gray" name="9" />
                <input type="submit" value="*" class="red black" name="*" />

                <!-- Row 3 -->
                <input type="submit" value="4" class="gray" name="4" />
                <input type="submit" value="5" class="gray" name="5" />
                <input type="submit" value="6" class="gray" name="6" />
                <input type="submit" value="-" class="red black" name="-">

                <!-- Row 4 -->
                <input type="submit" value="1" class="gray" name="1" />
                <input type="submit" value="2" class="gray" name="2" />
                <input type="submit" value="3" class="gray" name="3" />
                <input type="submit" value="+" class="red black" name="+" />

                <!-- Row 5 -->
                <input type="submit" value="0" class="gray" name="0" />
                <input type="submit" value="." class="gray" name="punto" />
                <input type="submit" value="C" class="gray" name="C" />
                <input type="submit" value="=" class="gray black" name="=" />
            </form>
        </div>
    </main>
</body>

</html>