<?php

namespace App\Tests\Calculator;

use App\Service\Calculator;
use App\Utils\Exception\CalculatorException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorTest extends KernelTestCase
{
    private $calculatorService;
    public function setUp() : void
    {
        self::bootKernel();
        $container = self::$container;
        $this->calculatorService = $container->get(Calculator::class);
    }

    public function testValidCalc () : void
    {
        $result = $this->calculatorService->calculate("2.4+98*9/8+987-9.8");
        $this->assertEquals("1089.85", $result["result"]);

        //Nice
        $result = $this->calculatorService->calculate("420/69");
        $this->assertEquals("6.09", $result["result"]);

        $result = $this->calculatorService->calculate("21*87+96-9*8/1.5");
        $this->assertEquals("1875", $result["result"]);
    }

    public function testInvalidCalc () : void
    {
       $result = $this->calculatorService->calculate("2145/0");
       $this->assertEquals("error", $result["status"]);

       $result = $this->calculatorService->calculate("2154/87+8a+87");
       $this->assertEquals("error", $result["status"]);
    }
}