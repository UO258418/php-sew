<!DOCTYPE html>

<html lang="es-ES">
    <head>
        <title>Ejercicio 4</title>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="CalculadoraCientifica.css"/>
    </head>
    <body>
        <h1>Calculadora Científica</h1>
        <p>Una calculadora cientifica</p>

        <main>
            <div class="section">
                <?php

                require "CalculadoraBase.php";

                session_start();

                if(!isset($_SESSION['pantalla'])) {
                    $_SESSION['pantalla'] = "0";
                }

                if(!isset($_SESSION['memoria'])) {
                    $_SESSION['memoria'] = 0;
                }

                class Calculadora extends CalculadoraBase {

                    private $reemplazos;
                
                    function __construct() {
                        $this->reemplazos = array(
                            "factorial" => array(
                                "searchValue" => "/(\d+)!/",
                                "replaceValue" => function($n) {
                                    $number = intval(str_replace('!', '', $n));
                                    return $this->calcularFactorial($number);
                                }
                            ),
                            "PI" => array(
                                "searchValue" => "/\x{03a0}/u",
                                "replaceValue" => function($n) {
                                    return M_PI;
                                } 
                            ),
                            "log" => array(
                                "searchValue" => "/log\(.+\)/",
                                "replaceValue" => function($n) {
                                    $number = $this->evalBetween($n, "log");
                                    return log10($number);
                                }
                            ),
                            "ln" => array(
                                "searchValue" => "/ln\(.+\)/",
                                "replaceValue" => function($n) {
                                    $number = $this->evalBetween($n, "ln");
                                    return log($number);
                                }
                            ),
                            "sr" => array(
                                "searchValue" => "/\x{221A}\(.+\)/u",
                                "replaceValue" => function($n) {
                                    $number = $this->evalBetween($n, "\u{221A}");
                                    return sqrt($number);
                                }
                            ),
                            "sin" => array(
                                "searchValue" => "/sin\(.+\)/",
                                "replaceValue" => function($n) {
                                    $number = $this->evalBetween($n, "sin");
                                    return sin($number);
                                }
                            ),
                            "cos" => array(
                                "searchValue" => "/cos\(.+\)/",
                                "replaceValue" => function($n) {
                                    $number = $this->evalBetween($n, "cos");
                                    return cos($number);
                                }
                            ),
                            "tan" => array(
                                "searchValue" => "/tan\(.+\)/",
                                "replaceValue" => function($n) {
                                    $number = $this->evalBetween($n, "tan");
                                    return tan($number);
                                }
                            ),
                            "pow" => array(
                                "searchValue" => "/\d+(\.\d+)?\^\d+(\.\d+)?/",
                                "replaceValue" => function($n) {
                                    $numbers = preg_split("/\^/", $n);
                                    if(array_key_exists(0, $numbers) && array_key_exists(1, $numbers))
                                        return pow($numbers[0], $numbers[1]);
                                }
                            )
                        );
                    }
                
                    function borrarUltimo() {
                        if (strlen($_SESSION['pantalla']) > 0) {
                            $_SESSION['pantalla'] = substr_replace($_SESSION['pantalla'] , "", -1);
                        }
                
                        if (strlen($_SESSION['pantalla']) == 0) {
                            $_SESSION['pantalla'] = "0";
                        }
                    }
                
                    function parentesis($parentesis) {
                        $this->meterEnPantalla($parentesis);
                    }
                
                    function cambiarSigno() {
                        if ($this->lastCharacter() == '-') {
                            $this->borrarUltimo();
                        } else {
                            $this->resta();
                        }
                    }
                
                    function factorial() {
                        $this->meterEnPantalla('!');
                    }
                
                    function PI() {
                        $this->meterEnPantalla("\u{03a0}");
                    }
                
                    function log() {
                        $this->meterEnPantalla("log(");
                    }
                
                    function squareRoot() {
                        $this->meterEnPantalla("\u{221A}(");
                    }
                
                    function sin() {
                        $this->meterEnPantalla("sin(");
                
                    }
                
                    function cos() {
                        $this->meterEnPantalla("cos(");
                    }
                
                    function tan() {
                        $this->meterEnPantalla("tan(");
                    }
                
                    function pow2() {
                        $this->meterEnPantalla("^2");
                    }
                
                    function pow() {
                        $this->meterEnPantalla("^");
                    }
                
                    function ln() {
                        $this->meterEnPantalla("ln(");
                    }
                
                    function tenToThePow() {
                        $this->meterEnPantalla("10^");
                    }
                
                    function ans() {
                        $this->meterEnPantalla($_SESSION['memoria']);
                    }
                
                    function random() {
                        $this->meterEnPantalla(rand());
                    }
                
                    function igual() {
                        $_SESSION['pantalla'] = $this->calcular($_SESSION['pantalla']);
                    }
                
                    // Util
                    function calcularFactorial($n) {
                        $total = 1;
                        for ($i = 1; $i <= $n; $i++) {
                            $total = $total * $i;
                        }
                        return $total;
                    }
                
                    function lastCharacter() {
                        return substr($_SESSION['pantalla'], -1);
                    }
                
                    function evalBetween($str, $prefix) {
                        $number = str_replace($prefix . "(", "", $str);
                        $number = substr_replace($number , "", -1);
                        try {
                            return eval("return $number ;");
                        } catch(ParseError $e) {

                        }
                    }
                
                    // Calcular
                    private function calcular($retornoSiHayError) {
                        // reemplazos
                
                        foreach ($this->reemplazos as $reemplazo) {
                            $matches = array();
                            preg_match_all($reemplazo["searchValue"], $_SESSION["pantalla"], $matches);
                            foreach ($matches[0] as $match) {
                                $_SESSION['pantalla'] = str_replace(
                                    $match, $reemplazo["replaceValue"]($match), $_SESSION["pantalla"]);
                            }
                        }
                        
                        try {
                            $result = eval("return " . $_SESSION['pantalla'] . ";");
                            $_SESSION['memoria'] = $result;
                            return $result;
                        } catch (ParseError $e) {
                            return $retornoSiHayError;
                        }
                    }
                
                }

                $calculadora = new Calculadora();

                if (count($_POST) > 0) 
                {   
                    // operadores
                    if(isset($_POST["/"])) $calculadora->division();
                    if(isset($_POST["*"])) $calculadora->multiplicacion();
                    if(isset($_POST["-"])) $calculadora->resta();
                    if(isset($_POST["+"])) $calculadora->suma();
                    if(isset($_POST["="])) $calculadora->igual();
                    if(isset($_POST["sqrt"])) $calculadora->squareRoot();
                    if(isset($_POST["log"])) $calculadora->log();
                    if(isset($_POST["ln"])) $calculadora->ln();

                    // operaciones complejas
                    if(isset($_POST["pow2"])) $calculadora->pow2();
                    if(isset($_POST["pow"])) $calculadora->pow();
                    if(isset($_POST["sin"])) $calculadora->sin();
                    if(isset($_POST["cos"])) $calculadora->cos();
                    if(isset($_POST["tan"])) $calculadora->tan();
                    if(isset($_POST["ten_to_the_pow"])) $calculadora->tenToThePow();
                    if(isset($_POST["factorial"])) $calculadora->factorial();

                    // simbolos
                    if(isset($_POST["punto"])) $calculadora->punto();
                    if(isset($_POST["C"])) $calculadora->borrar();
                    if(isset($_POST["rnd"])) $calculadora->random();
                    if(isset($_POST["("])) $calculadora->parentesis("(");
                    if(isset($_POST[")"])) $calculadora->parentesis(")");
                    if(isset($_POST["borrar_ultimo"])) $calculadora->borrarUltimo();
                    if(isset($_POST["ans"])) $calculadora->ans();
                    if(isset($_POST["CE"])) $calculadora->borrar();
                    if(isset($_POST["pi"])) $calculadora->pi();
                    if(isset($_POST["punto"])) $calculadora->punto();
                    if(isset($_POST["cambiar_signo"])) $calculadora->cambiarSigno();


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
                    <input type="submit" value="x²" name="pow2"/>
                    <input type="submit" value="xʸ" name="pow"/>
                    <input type="submit" value="sin" name="sin"/>
                    <input type="submit" value="cos" name="cos"/>
                    <input type="submit" value="tan" name="tan"/>

                    <!-- Row 2 -->
                    <input type="submit" value="√" name="sqrt"/>
                    <input type="submit" value="10ˣ" name="ten_to_the_pow"/>
                    <input type="submit" value="log" name="log"/>
                    <input type="submit" value="ln" name="ln"/>
                    <input type="submit" value="rnd" name="rnd"/>
                    
                    <!-- Row 3 -->
                    <input type="submit" value="ans" name="ans"/>
                    <input type="submit" value="CE" name="CE"/>
                    <input type="submit" value="C" name="C"/>
                    <input type="submit" value="&#9003;" name="borrar_ultimo"/>
                    <input type="submit" value="&#247;" name="/"/>

                    <!-- Row 4 -->
                    <input type="submit" value="&#120587;" name="pi"/>
                    <input type="submit" value="7" class="digit" name="7"/>
                    <input type="submit" value="8" class="digit" name="8"/>
                    <input type="submit" value="9" class="digit" name="9"/>
                    <input type="submit" value="&#10005;" name="*"/>

                    <!-- Row 5 -->
                    <input type="submit" value="n!" name="factorial"/>
                    <input type="submit" value="4" class="digit" name="4"/>
                    <input type="submit" value="5" class="digit" name="5"/>
                    <input type="submit" value="6" class="digit" name="6"/>
                    <input type="submit" value="-" name="-"/>

                    <!-- Row 6 -->
                    <input type="submit" value="&#177;" name="cambiar_signo"/>
                    <input type="submit" value="1" class="digit" name="1"/>
                    <input type="submit" value="2" class="digit" name="2"/>
                    <input type="submit" value="3" class="digit" name="3"/>
                    <input type="submit" value="+" name="+"/>

                    <!-- Row 7 -->
                    <input type="submit" value="(" name="("/>
                    <input type="submit" value=")" name=")"/>
                    <input type="submit" value="0" class="digit" name="0"/>
                    <input type="submit" value="," class="digit" name="punto"/>
                    <input type="submit" value="=" name="="/>
                </form>
            </div>
        </main>
    </body>
</html>