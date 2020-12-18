<?php

class CalculadoraBase {
                
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

    function borrar() {
        $_SESSION['pantalla'] = "0";
    }

    protected function meterEnPantalla($valor) {
        $_SESSION['pantalla'] = $_SESSION['pantalla'] == "0" ? $valor : $_SESSION['pantalla'] . $valor;
    }

}

?>