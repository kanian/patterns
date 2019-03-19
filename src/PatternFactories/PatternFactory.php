<?php
namespace Assoa\PatternFactories;

abstract class PatternFactory{
    public static abstract function get(...$args);
}