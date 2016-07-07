<?php namespace Mascame\Katina\Validator;

use Mascame\Katina\AbstractValidator;

class ArrayValidator extends AbstractValidator
{

    const TYPE_ASSOCIATIVE = 'associative';
    const TYPE_INDEXED = 'indexed';

    /**
     * @param $array
     * @param null $key
     * @return bool
     * @throws \Exception
     */
    public function isValid($array, $key = null)
    {
        if (! $key) $key = $this->key;

        $arrayFields = $this->outputValidator->getArrayFields();

        switch ($arrayFields[$key]["type"])
        {
            // 'key' => 'value'
            case self::TYPE_ASSOCIATIVE:
                return $this->validateArray($this->value, $arrayFields[$key]["fields"]);

            // 0 => 'value'
            case self::TYPE_INDEXED:
                $expectedValueType = $arrayFields[$key]["value"];

                foreach ($array as $entry) {
                    if (! $this->outputValidator->isValid($entry, $expectedValueType)) {
                        return false;
                    }
                }
                
                return true;

            default:
                throw new \Exception('Invalid array validator type');
        }    
    }

    /**
     * @param $array
     * @param $arrayConfig
     * @return bool
     */
    private function validateArray($array, $arrayConfig)
    {
        /**
         * $type required|optional
         */
        foreach ($arrayConfig as $type => $fields)
        {
            foreach ($fields as $field => $expectedType)
            {
                if (! isset($array[$field]) && $type == "optional") {
                    continue;
                }

                // If is array we will pass the key
                $key = ($expectedType == 'array') ? $field : null;

                $value = $array[$field];

                if (! $this->outputValidator->isValid($value, $expectedType, $key)) {
                    return false;
                }
            }
        }

        return true;
    }
    
}
