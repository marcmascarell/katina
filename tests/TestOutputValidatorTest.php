<?php

use \Mascame\Katina\Validator;

class TestValidatorTest extends PHPUnit_Framework_TestCase
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

    /**
     * @param $validator \Mascame\Katina\Validator
     * @param $data
     * @return bool
     */
    protected function check($validator, $data) {
        if (self::DEBUG) {
            return $validator->debug()->check($data);
        }

        $integral = false;

        try {
            $integral = $validator->check($data);
        } catch (\Exception $e) {}

        return $integral;
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
            'arrayIndexed2' => [
                [
                    'string'
                ],
                [
                    true
                ],
                [
                    new DateTime()
                ],
            ],
            'myday' => '2016-12-10',
            'imdb' => 'tt4897822',
            'site' => 'http://coconuten.com',
            'floated' => 1.7,
            'numeric' => '183232'
        ];

        $validator = new \Mascame\Katina\Validator(
            [
                'coconut' => ':string',
                'myday' => ':date',
                'imdb' => ':imdb',
                'site' => ':url',
                'floated' => ':float',
                'numeric' => ':numeric',
                'arrayFieldOptional' => [
                    ':string'
                ],
                'arrayField' => [
                    'coco' => ':string',
                    'thisDoesNotExist?' => ':int',
                    'arrayInside' => [
                        ':int'
                    ],
                    'arrayInside2' => [
                        'foo' => ':string',
                        'bar' => ':int',
                        'arrayInsideOfInside' => [
                            ':date'
                        ]
                    ]
                ],
                'arrayIndexed' => [
                    ':bool'
                ],
                'arrayIndexed2' => [
                    '*' => [
                        ':any'
                    ]
                ],
            ]
        );

        $this->assertTrue($this->check($validator, $data));
    }

    public function testIndexedArray()
    {
        $data = [
            'foo' => [
                12, 32, 44
            ]
        ];

        $validatorShouldFail = new \Mascame\Katina\Validator(
            [
                'inexistent' => ':string',
            ],
            []
        );

        $this->assertFalse($this->check($validatorShouldFail, $data));

        $validatorShouldWork = new \Mascame\Katina\Validator(
            [
                'foo' => [
                    ':int'
                ],
            ],
            [
                'inexistent' => ':string',
            ]
        );

        $this->assertTrue($this->check($validatorShouldWork, $data));
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

        $validatorShouldFail = new \Mascame\Katina\Validator(
            [
                'foo' => ':bool',
            ],
            []
        );

        $this->assertFalse($this->check($validatorShouldFail, $data));

        $validatorShouldWork = new \Mascame\Katina\Validator(
            [
                'foo' => [
                    '*' => ':any'
                ],
            ],
            []
        );

        $this->assertTrue($this->check($validatorShouldWork, $data));
    }

//    public function testIndexedWithNestedAssociativeArray()
//    {
//        $data = [
//            'foo' => [
//                [
//                    'times' => 12,
//                    'name' => 'Jules',
//                    'action' => true
//                ],
//                [
//                    'times' => 12,
//                    'name' => 'Jules',
//                    'action' => true
//                ],
//                [
//                    'times' => 12,
//                    'name' => 'Jules',
//                    'action' => true
//                ]
//            ]
//        ];
//
//        $validator = new \Mascame\Katina\Validator(
//            [
//                'foo' => 'array',
//            ],
//            []
//        );
//
//        $this->assertTrue($validator->debug()->check($data));
//    }
}