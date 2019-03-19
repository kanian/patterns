<?php
namespace Assoa\Tests\Utils;

use PhpParser\ParserFactory;
use Assoa\Tests\Utils\APattern;
use Assoa\Parser\ProxyNodeVisitorFactory;

class ObjectMother
{
    public function __construct()
    {
        $this->aPattern = new APattern(new ProxyNodeVisitorFactory);
        $this->aClassInstance = new AClass;


    }

    /**
     * Get the value of AClassAst
     */
    public function getAClassAst()
    {
        $code = <<<'CODE'
<?php
namespace Assoa\Tests\Utils;

class AClass{

    public function __constructor(){
        
    }
    public function aMethod(){
        
    }
}
CODE;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
            return $ast;
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }
    }

}
