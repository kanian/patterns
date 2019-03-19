<?php
namespace Assoa\Parser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Assoa\Commands\BuilderCommand;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\ClassMethod;
use Assoa\Parser\INodeVisitorFactory;
use Assoa\Parser\ProxyNodeVisitorFactory;

class SingletonNodeVisitorFactory extends ProxyNodeVisitorFactory
{

    

};
