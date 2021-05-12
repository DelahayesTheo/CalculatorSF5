<?php

namespace App\Service;

use App\Utils\Exception\CalculatorException;


/**
 * https://en.wikipedia.org/wiki/Shunting-yard_algorithm
 * Shunting yard-y based Parser, basic operations such as (- * / +), take in account the negative numbers and decimals but not parenthesis
 * back to school, let's go
 */
class Calculator 
{
    //Operators and their precedences
    private $operators = [
        "*" => 3,
        "/" => 3,
        "+" => 2,
        "-" => 2,
    ];

    /**
     * Calculate a given string using shutting yard and a postfix calculator
     * @param string $operationString
     * @return array response
     */
    public function calculate(string $operationString) : array {
        try {
            $RPNNotationOfCalc = $this->parse($operationString);
            $result = $this->postfix($RPNNotationOfCalc);
        } catch (CalculatorException $e) {
            return [
                "status" => "error",
                "result" => $e->getMessage()
            ];
        }

        return [
            "status" => "success",
            "result" => $result
        ];

    }

    /**
     * Parse to get the infix notation (or RPN Reverse Polish Notation)
     * @return array RPN notation array
     * @throws CalculatorException
     */
    public function parse(string $calcString) : array
    {
        $queue = [];
        $stack = [];
        if(!preg_match("/^[0-9\+\.\/\*\.-]+$/", $calcString)) {
            throw new CalculatorException(CalculatorException::FORBIDDEN_CHAR);
        }
        $arrayOfChar = str_split($calcString);
        $tempMemoryForNumber = "";
        $lastSeenIsOperator = false;
        foreach($arrayOfChar as $aCharacter) {
            if((is_numeric($aCharacter) || $aCharacter === ".") || ($lastSeenIsOperator && $aCharacter === "-")) {
                $tempMemoryForNumber .= $aCharacter;
                $lastSeenIsOperator = false;
            } else {
                if($aCharacter === "-" && $lastSeenIsOperator) {
                    // is negative of negative number, we count this has part of the number
                    $tempMemoryForNumber .= $aCharacter;
                    $lastSeenIsOperator = false;
                } else if ($lastSeenIsOperator) {
                    throw new CalculatorException(CalculatorException::MALFORMED_STRING_OPERATOR);
                } else {
                    //We need to process operator
                    if(!array_key_exists($aCharacter, $this->operators)) {
                        throw new CalculatorException(CalculatorException::UNKNOWN_OPERATOR);
                    }
                    //We need to get the temp out, we have an operator
                    $queue[] = $tempMemoryForNumber;
                    $tempMemoryForNumber = "";
                    $operatorIsStacked = false;
                    //We need to place the operators in the stack
                    do {
                        $lastOperatorStacked = "";
                        $precedenceOfOperatorToStack = $this->operators[$aCharacter];
                        if(!empty($stack)) {
                            $lastOperatorStacked = $stack[count($stack) - 1];
                        }

                        if(!empty($lastOperatorStacked)) {
                            $precedenceOfLastOperator = $this->operators[$lastOperatorStacked];
                            if ($precedenceOfOperatorToStack <= $precedenceOfLastOperator) {
                                //The last operator is of bigger or same precedence than the one we are trying to push
                                //The last operator needs to go to the queue;
                                $queue[] = array_pop($stack);
                            } else {
                                $stack[] = $aCharacter;
                                $operatorIsStacked = true;
                            }
                        } else {
                            $stack[] = $aCharacter;
                            $operatorIsStacked = true;
                        }
                    } while(!$operatorIsStacked);
                    $lastSeenIsOperator = true;
                }
            }
        }
        if((!empty($tempMemoryForNumber)) || $tempMemoryForNumber === "0") {
            $queue[] = $tempMemoryForNumber;
        }
        //Now we empty the stack to the queue
        for($i = 0; $i <= count($stack); $i++) {
            $queue[] = array_pop($stack);
        }

        return $queue;
    }
    /**
     * Calculate based on the RPN
     * @return string Result of the operation stringified
     */
    private function postfix(array $RPNArray) : string
    {
        $resultStack = [];
        do {
            $onValueOrOperator = array_shift($RPNArray);
            if (is_numeric($onValueOrOperator)) {
                $resultStack[] = (float)$onValueOrOperator;
            } else {
                //We can't calculate with only one side
                if (count($resultStack) < 2) {
                    throw new CalculatorException(CalculatorException::NOT_ENOUGH_NUMBERS);
                }
                $secondPartOfOperation = array_pop($resultStack);
                $firstPartOfOperation = array_pop($resultStack);

                //we have operation from here so * / - +
                switch ($onValueOrOperator) {
                    case "*":
                        $result = $firstPartOfOperation * $secondPartOfOperation;
                        break;
                    case "/":
                        if ($secondPartOfOperation === (float)0) {
                            throw new CalculatorException(CalculatorException::DIVISION_BY_ZERO);
                        }
                        $result = $firstPartOfOperation / $secondPartOfOperation;
                        break;
                    case "-":
                        $result = $firstPartOfOperation - $secondPartOfOperation;
                        break;
                    case "+":
                    default:
                        $result = $firstPartOfOperation + $secondPartOfOperation;
                        break;
                }
                $resultStack[] = $result;
            }
        } while (!empty($RPNArray));

        if(count($resultStack) !== 1) {
            throw new CalculatorException(CalculatorException::ERROR_AFTER_CALC);
        }
        //the result is the one and only number left on the stack
        return round($resultStack[0],2);
    }
}