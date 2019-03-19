<?php
namespace Assoa\Loader;

use Assoa\Loader\Loader;

class ClassFileLoader{
    /**
     * Reads the subject's class file into a string
     *
     * @return string
     */
    public static function readClassFile($className){
        $classFilePath = (Loader::getLoader())->findFile($className);
        return \file_get_contents($classFilePath);
    }
}