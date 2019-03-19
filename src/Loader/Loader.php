<?php
namespace Assoa\Loader;

class Loader{
    static  $loader;
     
    public  static function setLoader($loader)
    {
        self::$loader = $loader;
    }
    public static function getLoader()
    {
        return self::$loader;
    }
}