<?php
namespace Assoa\Yo;

class Yo{
    public $yo;
    public function __construct(){
        echo $this->yo;
    }
    public function hein(string $what){
        $this->yo = $what;
    }

    public function what(){
        echo $this->yo;
    }
}