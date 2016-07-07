<?php namespace Mascame\Katina;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var OutputValidator
     */
    protected $outputValidator;
    
    protected $key;
    
    protected $value;
    
    public function __construct(OutputValidator $outputValidator, $key, $value)
    {
        $this->outputValidator = $outputValidator;
        $this->key = $key;
        $this->value = $value;
    }

    abstract public function isValid($value);
    
}
