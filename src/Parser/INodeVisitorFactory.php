<?php
namespace Assoa\Parser;

use PhpParser\NodeVisitorAbstract;

Interface  INodeVisitorFactory {
    /**
     * Builder of a NodeVisitor for a specific pattern
     *
     * @param mixed ...$args
     * @return child class of NodeVisitorAbstract
     */
    public static function get(...$args);
}