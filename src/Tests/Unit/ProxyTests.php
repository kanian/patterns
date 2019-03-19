<?php
namespace Assoa\Tests\Unit;

use ReflectionClass;
use Assoa\Loader\Loader;
use Assoa\Tests\Utils\AClass;
use InvalidArgumentException;
use Assoa\Patterns\Proxy\Proxy;
use PHPUnit\Framework\TestCase;
use Assoa\Tests\Utils\ObjectMother;
use Assoa\PatternFactories\ProxyFactory;
use Assoa\Parser\ProxyNodeVisitorFactory;

class ProxifyTests extends TestCase
{
    public function setUp(): void
    {
        Loader::setLoader(require "../vendor/autoload.php");
        $this->objectMother = new ObjectMother();
    }
    public function testProxyOnlyAcceptsUserDefinedClasses()
    {
        $proxiedClassName = 'stdClass';
        $this->expectException(\InvalidArgumentException::class);
        $proxy = ProxyFactory::get(new \stdClass, false);
        
    }
    public function testSubjectClassAstCorrect()
    {
        $proxyPattern = new Proxy(new ProxyNodeVisitorFactory());
        $proxyPattern->patternize($this->objectMother->aClassInstance, true);
        $subjectClassAst = $this->objectMother->getAClassAst();
        $this->assertEquals($subjectClassAst, $proxyPattern->getSubjectClassAst());
    }

    
}
