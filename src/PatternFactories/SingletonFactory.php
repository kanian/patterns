<?php
namespace Assoa\PatternFactories;

use Assoa\Patterns\Singleton\Singleton;
use Assoa\PatternFactories\ProxyFactory;
use Assoa\Parser\SingletonNodeVisitorFactory;


class SingletonFactory extends ProxyFactory
{
    /**
     * Factory method to create a proxy to the first argument given
     *
     * @param mixed ...$args - $args[0] object to proxy - $args[1] should we proxy final classes or methods
     * @return a proxy to $obj
     */
    public static function get(...$args)
    {
        $obj = $args[0];
        $proxyPattern = new Singleton(new SingletonNodeVisitorFactory());
        $proxyPattern->patternize($obj);
        $proxyName = $proxyPattern->getPatternClassName();
        return $proxyName;
    }
}
