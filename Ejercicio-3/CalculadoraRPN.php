<!DOCTYPE html>

<html lang="es-ES">

<head>
    <title>Ejercicio 6</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="Calculadora.css" />
</head>

<body>
    <h1>Calculadora RPN</h1>
    <p>Una calculadora RPN</p>

    <main>
        <div id="displays">
        <?php
        require "Util.php";

        session_start();

        if(!isset($_SESSION["calculadora"])) {
            $_SESSION["calculadora"] = serialize(new Calculadora(4));
        }

        class Calculadora {

            // Pantalla
            public $pantalla;

            // Pila de la memoria
            public $stack;

            public $numOfDisplays;
            public $operators;

            function __construct($numOfDisplays) {
                $this->pantalla = 0;
                $this->stack = new Stack();
                $this->numOfDisplays = $numOfDisplays;
        
                // Operators
                $this->initOperators();
            }

            function initOperators() {
                $this->operators = array(
                    "+"     => function() {return $this->binaryOperation(function ($x, $y){return $x + $y;});},
                    "-"     => function() {return $this->binaryOperation(function ($x, $y){return $x - $y;});},
                    "*"     => function() {return $this->binaryOperation(function ($x, $y){return $x * $y;});},
                    "/"     => function() {return $this->binaryOperation(function ($x, $y){return $x / $y;});},
                    "!"     => function() {return $this->unaryOperation(function ($x){return $this->calcularFactorial($x);});},
                    "log"   => function() {return $this->unaryOperation(function ($x){return log10($x);});},
                    "ln"    => function() {return $this->unaryOperation(function ($x){return log($x);});},
                    "sqrt"  => function() {return $this->unaryOperation(function ($x){return sqrt($x);});},
                    "sin"   => function() {return $this->unaryOperation(function ($x){return sin($x);});},
                    "cos"   => function() {return $this->unaryOperation(function ($x){return cos($x);});},
                    "tan"   => function() {return $this->unaryOperation(function ($x){return tan($x);});},
                    "pow2"  => function() {return $this->unaryOperation(function ($x){return pow($x, 2);});},
                    "pow"   => function() {return $this->binaryOperation(function ($x, $y){return pow($x, $y);});},
                    "10pow" => function() {return $this->unaryOperation(function ($x){return pow(10, $x);});},
                );
            }
        
            // Operation types
            function binaryOperation($operation) {
                if($this->stack->size() >= 2) {
                    $o2 = $this->stack->pop();
                    $o1 = $this->stack->pop();
                    return $operation($o1, $o2);
                } 
            }
        
            function unaryOperation($operation) {
                if($this->stack->size() >= 1) {
                    $o = $this->stack->pop();
                    return $operation($o);
                } 
            }
        
            function operate($op) {
                if(array_key_exists($op, $this->operators)) {
                    $result = $this->operators[$op]();
                    if($result != null) {
                        $this->stack->push($result);
                        $this->clearDisplay();
                    } 
                }
            }
        
            // Operations
        
            // Enter
            function enter() {
                $number = floatval($this->pantalla);
                $this->stack->push($number);
                $this->clearDisplay();
            }
        
            function digitos($digito) {
                $this->updateDisplay($digito);
            }
        
            function punto() {
                $this->updateDisplay('.');
            }
        
            function borrar() {
                $this->stack->clear();
                $this->clearDisplay();
            }

            function borrarUltimo() {
                if (strlen($this->pantalla) > 0) {
                    $this->pantalla = substr_replace($this->pantalla , "", -1);
                }
        
                if (strlen($this->pantalla) == 0) {
                    $this->pantalla = 0;
                }
            }
        
            function cambiarSigno() {
                $this->updateDisplay('-');
            }
        
            function PI() {
                $this->updateDisplay(M_PI);
            }
        
            function ans() {
                $this->updateDisplay($this->stack->getElementAt(0));
            }
        
            function random() {
                $this->updateDisplay(rand());
            }
        
            // Util
            function calcularFactorial($n) {
                $total = 1;
                for ($i = 1; $i <= $n; $i++) {
                    $total = $total * $i;
                }
                return $total;
            }
        
            // Pantalla
            function updateDisplay($value) {
                $this->pantalla = $this->pantalla == 0 ? $value : $this->pantalla . $value;
            }
        
            function clearDisplay() {
                $this->pantalla = 0;
            }

            public function __sleep() {
                return array('pantalla', 'stack', 'numOfDisplays');
            }

            public function __wakeup() {
                $this->initOperators();
            }
        
        }

        $calculadora = unserialize($_SESSION["calculadora"]);

        if (count($_POST) > 0) 
        {   
            // operadores
            if(isset($_POST["/"])) $calculadora->operate("/");
            if(isset($_POST["*"])) $calculadora->operate("*");
            if(isset($_POST["-"])) $calculadora->operate("-");
            if(isset($_POST["+"])) $calculadora->operate("+");
            if(isset($_POST["="])) $calculadora->enter();
            if(isset($_POST["sqrt"])) $calculadora->operate("sqrt");
            if(isset($_POST["log"])) $calculadora->operate("log");
            if(isset($_POST["ln"])) $calculadora->operate("ln");

            // operaciones complejas
            if(isset($_POST["pow2"])) $calculadora->operate("pow2");
            if(isset($_POST["pow"])) $calculadora->operate("pow");
            if(isset($_POST["sin"])) $calculadora->operate("sin");
            if(isset($_POST["cos"])) $calculadora->operate("cos");
            if(isset($_POST["tan"])) $calculadora->operate("tan");
            if(isset($_POST["ten_to_the_pow"])) $calculadora->operate("10pow");
            if(isset($_POST["factorial"])) $calculadora->operate("!");

            // simbolos
            if(isset($_POST["punto"])) $calculadora->punto();
            if(isset($_POST["C"])) $calculadora->borrar();
            if(isset($_POST["rnd"])) $calculadora->random();
            if(isset($_POST["borrar_ultimo"])) $calculadora->borrarUltimo();
            if(isset($_POST["ans"])) $calculadora->ans();
            if(isset($_POST["CE"])) $calculadora->clearDisplay();
            if(isset($_POST["pi"])) $calculadora->pi();
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

            // Save object new state
            $_SESSION["calculadora"] = serialize($calculadora);
        }

        for($i = $calculadora->numOfDisplays - 1; $i >=0; $i--) {
            echo "<input type='text' id='display$i' class='display' title='display$i' value='" . $calculadora->stack->getElementAt($i) . "' disabled/>";
        }

        echo "<input type='text' id='display' class='display main' title='display' value='" . $calculadora->pantalla . "' disabled/>";

        ?>
        </div>

        <form action='#' method='post' name='botones'>
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
            <input type="submit" value="0" class="digit" name="0"/>
            <input type="submit" value="," class="digit" name="punto"/>
            <input type="submit" value="=" name="="/>
    </form>

    </main>
</body>

</html>