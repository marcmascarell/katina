<?php namespace Mascame\Katina;


class Validator
{
    private $requiredFields;
    private $optionalFields;

    private $debug = false;


    public function __construct($requiredFields, $optionalFields = [])
    {
        $this->requiredFields = $requiredFields;
        $this->optionalFields = $optionalFields;
    }

    /**
     * Checks if a value has the expected type or not
     *
     * @param $value
     * @param $pattern
     * @return bool
     */
    protected function isValid(&$value, $pattern)
    {
        return Matcher::matches($value, $pattern);
    }

    /**
     * Verifies that the object has the required fields
     *
     * @param array $data
     *
     * @return bool
     * @throws ValidatorException if integrity check failed
     */
    public function check($data)
    {
        foreach ($this->requiredFields as $field => $type)
        {
            if (! isset($data[$field]) || empty($data[$field]) || ! $this->isValid($data[$field], $type)) {

                throw new ValidatorException(ValidatorException::INVALID_INTEGRITY, $this->debugDump($field, $data));
            }
        }

        foreach ($this->optionalFields as $field => $expectedType)
        {
            if (! isset($data[$field])) {
                continue;
            }

            if (! $this->isValid($data[$field], $expectedType)) {
                throw new ValidatorException(ValidatorException::INVALID_INTEGRITY, $this->debugDump($field, $data));
            }
        }

        return true;
    }


    /**
     * @return $this
     */
    public function debug() {
        $this->debug = true;

        return $this;
    }

    /**
     * @param $title
     * @param $debugItem
     * @return null|string
     */
    protected function debugDump($title, $debugItem) {
        if ($this->debug) {
            return "'$title'" . var_dump($debugItem);
        }

        return null;
    }

}
