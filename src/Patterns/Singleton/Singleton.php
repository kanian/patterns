<?php
namespace Assoa\Patterns\Singleton;

use Assoa\Patterns\IPattern;
use Assoa\Patterns\Proxy\Proxy;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;

class Singleton extends Proxy implements IPattern
{

    public function buildConstructorDeclaration(ClassMethod $method)
    {
        $this->propertyAsts[] = $this->factory->property('subjectInstance')->makePrivate()->makeStatic();
        //Build instantiation logic
        $left = new StaticPropertyFetch(new Name('self'), "subjectInstance");
        $right = new ConstFetch(new Name("null"));
        $cond = new Identical($left, $right);
        $subNodes = [];
        $assignExpression = new Expression(
            new Assign(
                new StaticPropertyFetch(new Name("self"), "subjectInstance"),
                new New_(new Name($this->subjectClassShortName), array(new Arg(new Variable("args"), false, true)))
            )
        );
        $subNodes['stmts'] = [$assignExpression];
        $ifNode = new If_($cond, $subNodes);
        //Build instance return statement
        $instanceReturnNode = new Return_(
            new StaticPropertyFetch(new Name('self'), "subjectInstance")
        );
        //start building constructor per se
        $methodAstBuilder = $this->methodDeclarationPrelude($method);
        //Add if node expression to method
        $methodAstBuilder = $methodAstBuilder->addStmt(
            $ifNode/*$ifNodeExpression*/)->addStmt(
            $instanceReturnNode/*$instanceReturnExpression*/
        );
        $this->methodAsts[] = $methodAstBuilder;
        return $methodAstBuilder;
    }

    public function buildMethodDeclaration(ClassMethod $method)
    {
        $name = $method->name;
        $methodAstBuilder = $this->methodDeclarationPrelude($method);

        //Forward calls to subject instance
        $methodCallNode = $this->factory->methodCall(
            new StaticPropertyFetch(new Name('self'), "subjectInstance"),
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
}
