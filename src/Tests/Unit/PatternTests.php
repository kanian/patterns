<?php
namespace Assoa\Tests\Unit;

use Assoa\Patterns\Pattern;
use PHPUnit\Framework\TestCase;
use Assoa\Tests\Utils\ObjectMother;


class PatternTests extends TestCase{

    public function setUp(): void{
        $this->objectMother = new ObjectMother();
    }
    public function testPatternNameCorrect(){
        $aPatternClassName = 'Assoa\Tests\Utils\APattern';
        $this->assertEquals(get_class($this->objectMother->aPattern),$aPatternClassName);
    }
}