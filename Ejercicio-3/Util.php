<?php
class Stack {

    private $items = array();

    function __construct() {
        
    }

    function push($item) {
        array_unshift($this->items, $item);
    }

    function pop() {
        return count($this->items) == 0 ? "Underflow" : array_shift($this->items);
    }

    function size() {
        return count($this->items);
    }

    function getElementAt($i) {
        // Aqui seria mejor hacer una copia porque no queremos que se modifiquen elementos de la pila
        // Solo para visualizacion
        if(array_key_exists($i, $this->items)) {
            return $this->items[$i];
        } else {
            return "";
        }
    }

    function empty() {
        return empty($this->items);
    }
    
    function clear() {
        $this->items = array();
    }

}
?>