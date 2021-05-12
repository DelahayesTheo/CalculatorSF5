<?php

namespace App\Utils\Exception;

class CalculatorException extends \Exception {
    const FORBIDDEN_CHAR = 1;
    const MALFORMED_STRING_OPERATOR = 2;
    const UNKNOWN_OPERATOR = 3;
    const NOT_ENOUGH_NUMBERS = 4;
    const DIVISION_BY_ZERO = 5;
    const ERROR_AFTER_CALC = 6;

    private array $messageForErrors = [
        self::FORBIDDEN_CHAR => "There are forbidden char in your operation string",
        self::MALFORMED_STRING_OPERATOR => "There is a problem with your string, two operators are next to each other (aside from '-' for the minus) ",
        self::UNKNOWN_OPERATOR => "There is an unknown operator in the string, unable to process",
        self::NOT_ENOUGH_NUMBERS => "There isn't enough number to compute the calc, are you sure about your operation string ?",
        self::DIVISION_BY_ZERO => "Division by 0 is not possible, please enter a valid operation string",
        self::ERROR_AFTER_CALC => "There was an error, i don't even know how you would end of this error"
    ];

    public function __construct(int $code = 0, Throwable $previous = null)
    {
        $message = $this->messageForErrors[$code] ?? "Unrecognized exception code";
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return $this->message;
    }
}