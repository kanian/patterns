<?php
namespace Assoa\Parser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Assoa\Commands\BuilderCommand;
use PhpParser\NodeVisitorAbstract;
use Assoa\Parser\INodeVisitorFactory;
use PhpParser\Node\Stmt\ClassMethod;

class ProxyNodeVisitorFactory implements INodeVisitorFactory
{

    public static function get(...$args)
    {
        /*if(!isset($args[0])){
        throw new InvalidArgumentException();
        }*/
        $className = $args[0];
        $commands = $args[1];
       
        $visitor = new class($className, $commands) extends NodeVisitorAbstract
        {
            protected $parsingTargetClass = false;
            protected $isFinalClass = false;


            public function __construct($className, $commands)
            {
                $this->commands = $commands;
                $this->className = $className;
            }

            public function enterNode(Node $node)
            {
                if (($node instanceof ClassMethod) && $node->isPublic() && $this->parsingTargetClass === true) {
                    // If this node is a public method node within the class that we want to mock
                    // remove any final flag, because we want to override this method in our extension
                    if ($node->isFinal() && $this->acceptFinal) {
                        $node->flags &= ~Class_::MODIFIER_FINAL;
                    }
                    if($node->name == "__construct"){
                        $this->commands['constructor']->execute($node);
                    } else {
                        $this->commands['method']->execute($node);
                    }
                } elseif (($node instanceof Class_) && ($node->name == $this->className)) {
                    $this->parsingTargetClass = true;
                }
            }

            public function leaveNode(Node $node)
            {
                if ($node instanceof Class_) {
                    // If the node is a class node
                    if ($node->name == $this->className) {
                        if ($node->isFinal() && $this->acceptFinal) {
                            $node->flags &= ~Class_::MODIFIER_FINAL;
                        }
                        $this->commands['class']->execute();
                        $this->parsingTargetClass = false;
                    } /*else {
                        $this->parsingTargetClass = false;
                    }*/

                }
            }

        };
        return $visitor;
    }

};
