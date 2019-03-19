<?php
namespace Assoa\Commands;

use Assoa\Patterns\Pattern;

class BuilderCommand
{

    protected $builder;

    public function __construct(Pattern $builder, \Closure $method)
    {
        $this->builder = $builder;
        $this->method = $method;
    }
    public function execute($arg = null){
        \Closure::bind($this->method, $this->builder)($arg);
    }

    /**
     * Get the value of receiver
     */
    public function getBuilder()
    {
        return $this->builder;
    }
    /**
     * Set the value of receiver
     *
     * @return  self
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;

        return $this;
    }
}
