<?php

require "./vendor/autoload.php";
use Assoa\Yo\Yo;
use Assoa\Loader\Loader;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use Assoa\PatternFactories\ProxyFactory;
use Assoa\PatternFactories\SingletonFactory;

Loader::setLoader(require ("./vendor/autoload.php"));

$singletonClass = SingletonFactory::get(new Yo, true);
$single = new $singletonClass();
$single->hein("Yedi!");
$single->what();

$proxy = ProxyFactory::get(new Yo, true);
$proxy->hein("Yedi!");
$proxy->what();
print_r($single);
