<?php
namespace Assoa\PatternFactories;

use Assoa\Patterns\Proxy\Proxy;
use Assoa\Parser\ProxyNodeVisitorFactory;
use Assoa\PatternFactories\PatternFactory;

class ProxyFactory extends PatternFactory
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
        $proxyPattern = new Proxy(new ProxyNodeVisitorFactory());
        $proxyPattern->patternize($obj);
        $proxyName = $proxyPattern->getPatternClassName();
        return new $proxyName;
    }
}
