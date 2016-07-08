<?php

class MatchesTest extends \PHPUnit_Framework_TestCase
{
    public function testScalar()
    {
        $this->assertTrue(Mascame\Katina\Matcher::matches(1, ':integer'));
        $this->assertFalse(Mascame\Katina\Matcher::matches('string', ':integer'));
    }

    public function testArray()
    {
        $pattern = [
            '*' => [
                'id' => ':integer gt(0)',
                'title' => ':string contains(super)',
            ],
        ];

        $this->assertTrue(
            Mascame\Katina\Matcher::matches(
                [
                    [
                        'id' => 1,
                        'title' => 'super cool book'
                    ],
                    [
                        'id' => 2,
                        'title' => 'another super cool book'
                    ],
                ],
                $pattern
            )
        );
        $this->assertFalse(
            Mascame\Katina\Matcher::matches(
                [
                    [
                        'id' => 1,
                        'title' => 'just book'
                    ],
                ],
                $pattern
            )
        );
        $this->assertFalse(
            Mascame\Katina\Matcher::matches(
                null,
                $pattern
            )
        );
    }

    public function testExample()
    {
        $books = [
            [
                'type' => 'book',
                'title' => 'Geography book',
                'chapters' => [
                    'eu' => ['title' => 'Europe', 'interesting' => true],
                    'as' => ['title' => 'America', 'interesting' => false]
                ]
            ],
            [
                'type' => 'book',
                'title' => 'Foreign languages book',
                'chapters' => [
                    'de' => ['title' => 'Deutsch']
                ]
            ]
        ];

        $pattern = [
            '*' => [
                'type' => 'book',
                'title' => ':string contains(book)',
                'chapters' => [
                    ':string length(2) {1,3}' => [
                        'title' => ':string',
                        'interesting?' => ':bool',
                    ]
                ]
            ]
        ];

        $this->assertTrue(Mascame\Katina\Matcher::matches($books, $pattern));
    }
}
