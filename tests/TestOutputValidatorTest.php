<?php

use \Mascame\Katina\Validator;

class TestOutputValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Will avoid try catching. Note: Some fails are on purpose
     */
    const DEBUG = false;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    /**
     * @throws \Mascame\Katina\ValidatorException
     */
    public function testFullCheck()
    {
        $data = [
            'coconut' => 'is awesome!',
            'arrayFieldOptional' => [
                'value1',
                'value2',
                'value3',
            ],
            'arrayField' => [
                'coco' => 'amazing coconut',
                'arrayInside' => [
                    12,
                    32,
                    44
                ],
                'arrayInside2' => [
                    'foo' => 'bar',
                    'bar' => 1,
                    'arrayInsideOfInside' => [
                        '2016-10-10'
                    ]
                ]
            ],
            'arrayIndexed' => [
                true,
                true,
                false
            ],
            'myday' => '2016-12-10',
            'imdb' => 'tt4897822',
            'site' => 'http://coconuten.com',
            'floated' => 1.7,
            'numeric' => '183232',
        ];

        $validator = new \Mascame\Katina\OutputValidator(
            [
                'coconut' => 'string',
                'myday' => 'date',
                'imdb' => 'imdb',
                'site' => 'url',
                'floated' => 'float',
                'numeric' => 'numeric',
                'arrayFieldOptional' => 'array',
                'arrayField' => 'array',
                'arrayIndexed' => 'array',
            ],
            [],
            [
                'arrayFieldOptional' => [
                    'type' => Validator\ArrayValidator::TYPE_INDEXED,
                    'value' => 'string'
                ],
                'arrayField' => [
                    'type' => Validator\ArrayValidator::TYPE_ASSOCIATIVE,
                    'fields' => [
                        'required' => [
                            'coco' => 'string',
                            'arrayInside' => 'array',
                            'arrayInside2' => 'array',
                        ],
                        'optional' => [
                            'optional' => 'string',
                        ]
                    ],
                ],
                'arrayInside' => [
                    'type' => Validator\ArrayValidator::TYPE_INDEXED,
                    'value' => 'int'
                ],
                'arrayInside2' => [
                    'type' => Validator\ArrayValidator::TYPE_ASSOCIATIVE,
                    'fields' => [
                        'required' => [
                            'foo' => 'string',
                            'bar' => 'int',
                            'arrayInsideOfInside' => 'array',
                        ],
                        'optional' => [
                            'optional' => 'string',
                        ]
                    ],
                ],
                'arrayInsideOfInside' => [
                    'type' => Validator\ArrayValidator::TYPE_INDEXED,
                    'value' => 'date'
                ],
                'arrayIndexed' => [
                    'type' => Validator\ArrayValidator::TYPE_INDEXED,
                    'value' => 'bool'
                ],
            ]
        );

        $this->assertTrue($this->checkIntegrity($validator, $data));
    }

    /**
     * @param $validator \Mascame\Katina\OutputValidator
     * @param $data
     * @return bool
     */
    protected function checkIntegrity($validator, $data) {
        if (self::DEBUG) {
            return $validator->debug()->checkIntegrity($data);
        }

        $integral = false;

        try {
            $integral = $validator->checkIntegrity($data);
        } catch (\Exception $e) {}

        return $integral;
    }

    public function testIndexedArray()
    {
        $data = [
            'foo' => [
                12, 32, 44
            ]
        ];

        $validatorShouldFail = new \Mascame\Katina\OutputValidator(
            [
                'foo' => 'array',
                'inexistent' => 'array',
            ],
            [],
            [
                'foo' => [
                    'type' => \Mascame\Katina\Validator\ArrayValidator::TYPE_INDEXED,
                    'value' => 'bool'
                ],
            ]
        );

        $this->assertFalse($this->checkIntegrity($validatorShouldFail, $data));

        $validatorShouldWork = new \Mascame\Katina\OutputValidator(
            [
                'foo' => 'array',
            ],
            [
                'inexistent' => 'array',
            ],
            [
                'foo' => [
                    'type' => \Mascame\Katina\Validator\ArrayValidator::TYPE_INDEXED,
                    'value' => 'int'
                ],
            ]
        );

        $this->assertTrue($this->checkIntegrity($validatorShouldWork, $data));
    }

    public function testAssociativeArray()
    {
        $data = [
            'foo' => [
                'times' => 12,
                'name' => 'Jules',
                'action' => true
            ]
        ];

        $validatorShouldFail = new \Mascame\Katina\OutputValidator(
            [
                'foo' => 'array',
            ],
            [],
            [
                'foo' => [
                    'type' => \Mascame\Katina\Validator\ArrayValidator::TYPE_ASSOCIATIVE,
                    'fields' => [
                        'required' => [
                            'times' => 'int',
                            'name' => 'string',
                            'action' => 'bool',
                            ':DDD' => 'int'
                        ],
                        'optional' => [
                        ]
                    ],
                ],
            ]
        );

        $this->assertFalse($this->checkIntegrity($validatorShouldFail, $data));

        $validatorShouldWork = new \Mascame\Katina\OutputValidator(
            [
                'foo' => 'array',
            ],
            [],
            [
                'foo' => [
                    'type' => \Mascame\Katina\Validator\ArrayValidator::TYPE_ASSOCIATIVE,
                    'fields' => [
                        'required' => [
                            'times' => 'int',
                            'name' => 'string',
                            'action' => 'bool',
                        ],
                        'optional' => [
                            ':DDD' => 'int'
                        ]
                    ],
                ],
            ]
        );

        $this->assertTrue($this->checkIntegrity($validatorShouldWork, $data));
    }
}