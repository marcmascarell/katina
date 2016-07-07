<?php namespace Mascame\Katina\Validator;

use Mascame\Katina\AbstractValidator;

class ImdbId extends AbstractValidator
{
    
    public function isValid($value) 
    {
        preg_match("/tt([0-9]+){5,9}/", $value, $matches);
        
        return (sizeof($matches) > 0);
    }
    
}
