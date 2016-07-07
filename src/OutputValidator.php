<?php namespace Mascame\Katina;

use Mascame\Katina\Validator\ArrayValidator;
use Mascame\Katina\Validator\Date;
use Mascame\Katina\Validator\ImdbId;

class OutputValidator
{
    private $requiredFields;
    private $optionalFields;
    private $arrayFields;

    private $debug = false;

    private $validators = [
        'date' => Date::class,
        'imdb' => ImdbId::class,
        'array' => ArrayValidator::class,
    ];

    public function __construct($requiredFields, $optionalFields = [], $arrayFields = [])
    {
        $this->requiredFields = $requiredFields;
        $this->optionalFields = $optionalFields;
        $this->arrayFields = $arrayFields;
    }

    /**
     * @return mixed
     */
    public function getRequiredFields()
    {
        return $this->requiredFields;
    }

    /**
     * @return array
     */
    public function getOptionalFields()
    {
        return $this->optionalFields;
    }

    /**
     * @return array
     */
    public function getArrayFields()
    {
        return $this->arrayFields;
    }

    public function withValidators(array $validators = []) {
        $this->validators = array_merge($this->validators, $validators);

        return $this;
    }

    /**
     * Checks if a value has the expectedType or not
     *
     * @param $value
     * @param $expectedType
     * @param null $key
     * @return bool|mixed
     */
    public function isValid(&$value, $expectedType, $key = null)
    {
        switch ($expectedType)
        {
            /**
             * Custom validators
             */
            case array_key_exists($expectedType, $this->validators):
                return $this->useValidator($expectedType, $key, $value);
            case 'bool':
            case 'boolean':
                return is_bool($value);
            case 'int':
            case 'integer':
                return is_integer($value);
            case 'float':
                return is_float($value);
            case 'numeric':
                return is_numeric($value);
            case 'url':
                return (strpos($value, "http") !== false);
            case 'string':
                if(! is_string($value)) {
                    return false;
                }

                $value = trim(strip_tags($value));

                return ! empty($value);
            default:
                return (gettype($value) == $expectedType);
        }
    }

    /**
     * @param $validatorName
     * @param $key
     * @param $value
     * @return mixed
     * @throws ValidatorException
     */
    protected function useValidator($validatorName, $key, $value) {
        /**
         * @var $validator ValidatorInterface
         */
        $validator = $this->validators[$validatorName];
        $validator = (new $validator($this, $key, $value));

        $isValid = $validator->isValid($value);

        if (! $isValid && $this->debug()) {
            throw new ValidatorException(ValidatorException::INVALID_INTEGRITY, $this->debugDump($key, $value));
        }

        return $isValid;
    }

    /**
     * Verifies that the object has the required fields
     *
     * @param array $data
     *
     * @return bool
     * @throws ValidatorException if integrity check failed
     */
    public function checkIntegrity($data)
    {
        foreach ($this->requiredFields as $field => $type)
        {
            if (! isset($data[$field]) || empty($data[$field]) || ! $this->isValid($data[$field], $type, $field)) {
                throw new ValidatorException(ValidatorException::INVALID_INTEGRITY, $this->debugDump($field, $data));
            }
        }

        foreach ($this->optionalFields as $field => $expectedType)
        {
            if (! isset($data[$field])) {
                continue;
            }

            if (! $this->isValid($data[$field], $expectedType, $field)) {
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
            return "$title: " . var_dump($debugItem);
        }

        return null;
    }

}
