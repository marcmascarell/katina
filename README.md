# Katina - The PHP Output Validator

[![Latest Version](https://img.shields.io/github/release/marcmascarell/katina.svg?style=flat-square)](https://github.com/marcmascarell/katina/releases)
[![Travis](https://img.shields.io/travis/marcmascarell/katina.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/marcmascarell/katina)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Test helper class that **validates any kind of output based on the rules passed**. Useful to test API reponses or data that changes over time.

A modernized version of [ptrofimov/matchmaker](https://github.com/ptrofimov/matchmaker) package.

## Installation

`composer require mascame/katina --dev`

## Basic usage

```php
$data = [
    'name' => 'John Doe',
    'count' => 123,
];

$validator = new \Mascame\Katina\Validator(['name' => ':string', 'count' => ':int']);

$validator->check($data); // true
```

## Advanced usage

```php
$data = [
    'name' => 'John Doe',
    'count' => 145,
    'list' => [
        'emptyList' => false,
        'nested-list' => [
            1, 2, 3
        ]
    ],
    'books' => [
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
    ]
];

$requiredFields = [
    'name' => ':string',
    'count' => ':int',
    'list' => [
        'emptyList' => ':bool',
        'nested-list' => [
            ':int'
        ]
    ],
    'books' => [
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
    ]
];

$optionalFields = [
    'something' => 'boolean'
];

$validator = new \Mascame\Katina\Validator($requiredFields, $optionalFields);

$validator->check($data); // true
```

### Adding rules

```php
$data = [
    'my-birthday' => '1980-01-01'
];

$requiredFields = ['my-birthday' => ':birthdayValidator'];

$validator = new \Mascame\Katina\Validator($requiredFields);

// You can add or override rules
\Mascame\Katina\Rules::setRules(['birthdayValidator' => function($value) {
    return ($value == '1980-01-01');
}]);

$validator->check($data); // true
```

## Matching rules

Matching rules are strings that start with ':'. You can use multiple matchers joined with space.

Matcher could be any callable (name of function or closure). You can add your own rules or replace standard ones.

* **General**

 * empty
 * nonempty
 * required
 * in(a, b, ...)
 * mixed
 * any

* **Types**

 * array
 * bool
 * boolean
 * callable
 * double
 * float
 * int
 * integer
 * long
 * numeric
 * number
 * object
 * real
 * resource
 * scalar
 * string

* **Numbers**

 * gt(n)
 * gte(n)
 * lt(n)
 * lte(n)
 * negative
 * positive
 * between(a, b)

* **Strings**

 * alnum
 * alpha
 * cntrl
 * digit
 * graph
 * lower
 * print
 * punct
 * space
 * upper
 * xdigit
 * regexp(pattern)
 * email
 * url
 * ip
 * length(n)
 * min(n)
 * max(n)
 * contains(needle)
 * starts(s)
 * ends(s)
 * json
 * date

* **Arrays**

 * count(n)
 * keys(key1, key2, ...)

* **Objects**

 * instance(class)
 * property(name, value)
 * method(name, value)

For more details you can find [here](https://github.com/marcmascarell/katina/blob/master/src/Rules.php)

## Quantifiers for keys

* ! - one key required (default)
* ? - optional key
* * - any count of keys
* {3} - strict count of keys
* {1,5} - range

For matchers (i.e. ':string') default quantifier is *

## Using Matcher as standalone

You can directly use Matcher:

```php
\Mascame\Katina\Matcher::matches(['test' => true], ['test' => ':bool']); // true
```

## Contributing

Thank you for considering contributing! You can contribute at any time forking the project and making a pull request.

## Support

If you need help or any kind of support, please send an e-mail to Marc Mascarell at marcmascarell@gmail.com.

## License

Credit to [Petr Trofimov](https://github.com/ptrofimov) for his package [ptrofimov/matchmaker](https://github.com/ptrofimov/matchmaker) which this package is heavily based on.

MIT
