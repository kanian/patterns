<?php
namespace Assoa\Patterns\Proxy;

use PhpParser\Node\Arg;
use Assoa\Patterns\Pattern;
use Assoa\Patterns\IPattern;
use PhpParser\NodeTraverser;
use InvalidArgumentException;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\ClassMethod;
use Assoa\Parser\INodeVisitorFactory;
use Assoa\Parser\ProxyNodeVisitorFactory;

class Proxy extends Pattern implements IPattern
{
    protected $subjectClass;
    protected $subjectClassShortName;
    protected $subjectClassAst;
    protected $factory;
    protected $patternClassShortName;
    protected $patternAstBuilder;
    protected $patternClassAstBuilder;
    protected $methodAsts = [];

    /**
     * Constructs the proxy class as a subclass of the subject class
     *
     * @param INodeVisitorFactory  $nodeVisitor instance 
     * @throws InvalidArgumentException
     */
    public function __construct(INodeVisitorFactory $nodeVisitor)
    {
        parent::__construct($nodeVisitor);
    }

    public function patternize()
    {
        $args = func_get_args();
        parent::patternize(...$args);
        $this->proxify();
    }

    /**
     * Builds the AST of the proxy class
     *
     * @return void
     */
    protected function proxify()
    {
        $classFileContents = $this->getSubjectClassFileContents();
        $this->subjectClassAst = $this->astParser->parse($classFileContents);
        $traverser = new NodeTraverser;

        $traverser->addVisitor(
            ProxyNodeVisitorFactory::get(
                $this->subjectClassShortName, $this->getBuilderCommands()
            )
        );
        $traverser->traverse($this->subjectClassAst);
        $this->ideatePattern();
    }
    public function buildClassDeclaration()
    {
        $this->patternClassAstBuilder = $this->factory->class($this->patternClassShortName)
            ->extend($this->subjectClassShortName);
        for ($i = 0; $i < count($this->methodAsts); $i++) {
            $this->patternClassAstBuilder = $this->patternClassAstBuilder->addStmt($this->methodAsts[$i]);
        }
        for ($i = 0; $i < count($this->propertyAsts); $i++) {
            $this->patternClassAstBuilder = $this->patternClassAstBuilder->addStmt($this->propertyAsts[$i]);
        }
        if (empty($this->patternAstBuilder)) {
            $this->patternAstBuilder = $this->patternClassAstBuilder;
        } else {
            $this->patternAstBuilder = $this->patternAstBuilder->addStmt($this->patternClassAstBuilder);
        }
    }

    public function buildMethodDeclaration(ClassMethod $method)
    {
        $name = $method->name;
        $methodAstBuilder = $this->methodDeclarationPrelude($method);

        //Forward calls to subject instance
        $methodCallNode = $this->factory->methodCall(
            $this->factory->propertyFetch(
                $this->factory->var('this'),
                $this->factory->val('subjectInstance')
            ),
            $name->name,
            array(new Arg(new Variable("args"), false, true))
        );
        //Call return 
        $methodReturnNode = new Return_(
            $methodCallNode
        );
        $methodAstBuilder = $methodAstBuilder->addStmt($methodReturnNode);

        $this->methodAsts[] = $methodAstBuilder;
        return $methodAstBuilder;
    }

    public function buildConstructorDeclaration(ClassMethod $method)
    {
        $methodAstBuilder = $this->methodDeclarationPrelude($method);
        //Assign new Instance of subject
        $assignSubjectInstanceNode = new Assign(
            $this->factory->propertyFetch(
                $this->factory->var('this'),
                $this->factory->val('subjectInstance')
            ),
            $this->factory->new($this->subjectClassShortName, [$this->factory->var('args')])
        );
        //we want to unpack args when calling original constructor
        $assignSubjectInstanceNode->expr->args[0]->unpack = true;
        $assignInstanceExpression = new Expression(
            $assignSubjectInstanceNode
        );
        $methodAstBuilder = $methodAstBuilder->addStmt(
            $assignInstanceExpression);

        $this->methodAsts[] = $methodAstBuilder;
        return $methodAstBuilder;
    }
    protected function methodDeclarationPrelude(ClassMethod $method)
    {
        $name = $method->name;
        $methodAstBuilder = $this->factory->method($name);
        $methodAstBuilder = $this->addMethodParams($methodAstBuilder, $method->getParams());

        //Get the args method was called with
        $assignNode = new Assign(
            $this->factory->var('args'),
            $this->factory->funcCall('func_get_args', [])
        );
        $assignExpression = new Expression(
            $assignNode
        );
        $methodAstBuilder = $methodAstBuilder->addStmt(
            $assignExpression);
        return $methodAstBuilder;
    }
}
