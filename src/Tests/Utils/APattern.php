<?php
namespace Assoa\Tests\Utils;

use Assoa\Patterns\Pattern;
use PhpParser\Node\Stmt\ClassMethod;

class APattern extends Pattern{
    public function buildClassDeclaration(){}

    public function buildMethodDeclaration(ClassMethod $method){}

    public function buildConstructorDeclaration(ClassMethod $method){}
}