<?php
namespace Assoa\Patterns;

use ReflectionClass;
use ArgumentCountError;
use PhpParser\ParserFactory;
use InvalidArgumentException;
use PhpParser\BuilderFactory;
use Assoa\Loader\ClassFileLoader;
use Assoa\Commands\BuilderCommand;
use Assoa\Tests\Utils\GuidGenerator;
use PhpParser\Node\Stmt\ClassMethod;
use Assoa\Parser\INodeVisitorFactory;
use PhpParser\PrettyPrinter\Standard;

abstract class Pattern implements IPattern
{
    protected $astParser;
    protected $nodeVisitor;
    protected $patternName;
    protected $patternClassShortName;
    protected $subjectClassShortName;
    protected $patternAstBuilder;
    protected $builderCommands = [];
    protected $methodAsts = [];
    protected $propertyAsts = [];
    public function __construct(INodeVisitorFactory $nodeVisitor)
    {
        $this->patternName = (new ReflectionClass(get_class($this)))->getShortName();
        $this->astParser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->nodeVisitor = $nodeVisitor;
    }

    abstract public function buildClassDeclaration();

    abstract public function buildMethodDeclaration(ClassMethod $method);

    abstract public function buildConstructorDeclaration(ClassMethod $method);

    public function patternize()
    {
        $args = func_get_args();
        if (count($args) < 1) {
            throw new ArgumentCountError("$this->patternName::patternize takes 1 parameter at least, the class name");
        }
        $this->init(...$args);
    }

    protected function init($subject)
    {
        if ($this->rejectIfNotUserClass($subject)) {
            throw new InvalidArgumentException("$this->patternName only accepts user defined classes");
        }
        $this->classReflector = new ReflectionClass($this->subjectClass);
        $this->factory = new BuilderFactory;
        $this->namespace = $this->classReflector->getNamespaceName();
        $this->subjectClassShortName = $this->classReflector->getShortName();
        if (!empty($this->namespace)) {
            $this->patternAstBuilder = $this->factory->namespace($this->namespace);
        }
        $this->setPatternClassShortName();
        $this->setBuilderCommands();
    }
    /**
     * Sets the generated pattern class short name.
     *
     * @return void
     */
    public function setPatternClassShortName()
    {
        $this->patternClassShortName = $this->subjectClassShortName . '_' . $this->patternName . '_' . (GuidGenerator::guidv4Like(openssl_random_pseudo_bytes(16)));
    }
    /**
    * Get the name of the fully qualified name of built pattern class 
    * 
    * @return string 
    */
    public function getPatternClassName()
    {
        return $this->namespace . "\\" . $this->patternClassShortName;
    }
    /**
     * Set BuilderCommand array
     *
     * @return void
     */
    protected function setBuilderCommands()
    {
        $this->builderCommands["class"] = new BuilderCommand(
            $this, function () {$this->buildClassDeclaration();}
        );
        $this->builderCommands["constructor"] = new BuilderCommand(
            $this, function ($node) {$this->buildConstructorDeclaration($node);}
        );
        $this->builderCommands["method"] = new BuilderCommand(
            $this, function ($node) {$this->buildMethodDeclaration($node);}
        );
    }
    /**
     * Get BuilderCommand array
     *
     * @return BuilderCommand[]
     */
    protected function getBuilderCommands()
    {
        return $this->builderCommands;
    }
    /**
     * Loads proxy class
     *
     * @return void
     */
    protected function ideatePattern()
    {
        $prettyPrinter = new Standard;
        $stmts = array($this->patternAstBuilder->getNode());
        $newClassCode = $prettyPrinter->prettyPrintFile($stmts) . PHP_EOL;
        eval("?>" . $newClassCode);
    }
    /**
     * Add parameter nodes to method ast
     *
     * @param ClassMethod $method
     * @param Node[] $params
     * @return void
     */
    protected function addMethodParams($method, $params)
    {

        for ($i = 0; $i < count($params); $i++) {
            $paramAstBuilder = $this->factory->param("param" . ($i + 1));
            if ($params[$i]->variadic) {
                $paramAstBuilder = $paramAstBuilder->makeVariadic();
            }
            if ($params[$i]->byRef) {
                $paramAstBuilder = $paramAstBuilder->makeByRef();
            }
            if ($params[$i]->type) {
                $paramAstBuilder = $paramAstBuilder->setTypeHint($params[$i]->type);
            }
            if (isset($params[$i]->default)) {
                $paramAstBuilder = $paramAstBuilder->setDefault($params[$i]->default);
            }
            $method = $method->addParam($paramAstBuilder);

        }
        return $method;
    }
    /**
     * Returns true if the object is not an instance of a class or of a user defined class
     *
     * @param mixed $obj
     * @return boolean
     */
    protected function rejectIfNotUserClass($obj): bool
    {
        $this->subjectClass = get_class($obj);
        if (empty($this->subjectClass)) {
            return true;
        }
        $reflector = new ReflectionClass($this->subjectClass);
        if (!$reflector->isUserDefined()) {
            return true;
        }
        return false;
    }

    /**
     * Reads the subject's class file into a string
     *
     * @return string
     */
    protected function getSubjectClassFileContents()
    {
        return ClassFileLoader::readClassFile($this->subjectClass);
    }

    /**
     * Get the name of subject class
     *
     * @return string
     */
    public function getSubjectClass()
    {
        return $this->subjectClass;
    }

    /**
     * Get the  subject class Ast
     *
     * @return
     */
    public function getSubjectClassAst()
    {
        return $this->subjectClassAst;
    }
}
