<?php namespace Mascame\Katina\Validator;

use Mascame\Katina\AbstractValidator;

class Date extends AbstractValidator
{
    
    public function isValid($value) 
    {
        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {}

        return false;
    }
    
}
