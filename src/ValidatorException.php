<?php namespace Mascame\Katina;


class ValidatorException extends \Exception {

    const INVALID_INTEGRITY = "Invalid object integrity";

    public function __construct($constant, $extraInfo, $code = 0, \Exception $previous = null) 
    {
        $message = ($extraInfo) ? $constant . ': ' . $extraInfo: $constant;

        parent::__construct($message, $code, $previous);
    }
    
}
