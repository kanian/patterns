<?php
namespace Assoa\Tests\Unit;

use ReflectionClass;
use Assoa\Loader\Loader;
use Assoa\Tests\Utils\AClass;
use PHPUnit\Framework\TestCase;
use Assoa\Tests\Utils\ObjectMother;
use Assoa\PatternFactories\ProxyFactory;

class ProxyFactoryTest extends TestCase{
    public function setUp(): void
    {
        Loader::setLoader(require "../vendor/autoload.php");
        $this->objectMother = new ObjectMother();
    }
    public function testProxyIsSubclassOfSubject()
    {
        $proxy = ProxyFactory::get(Aclass::class);
        $reflector = new \ReflectionClass(get_class($proxy));
        $this->assertTrue($reflector->isSubclassOf(Aclass::class));
    }
}