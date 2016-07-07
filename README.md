# Katina - The PHP Output Validator

Test helper class that **validates any kind of output based on the rules passed**. Useful to test API reponses or data that changes over time.

## Basic usage

```php
$data = [
    'name' => 'John Doe',
    'count' => 123,
];

$validator = new \Mascame\OutputValidator\OutputValidator(['name' => 'string', 'count' => 'int']);

$validator->checkIntegrity($data); // true
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
    ]
];

$requiredFields = ['name' => 'string', 'count' => 'int', 'list' => 'array'];
$optionalFields = ['something' => 'boolean'];
$arrayFields = [
    'list' => [
        'type' => Validator\ArrayValidator::TYPE_ASSOCIATIVE,
        'fields' => [
            'required' => [
                'nested-list' => 'array',
                'emptyList' => 'boolean',
            ],
            'optional' => []
        ],
    ],
    'nested-list' => [
        'type' => Validator\ArrayValidator::TYPE_INDEXED,
        'value' => 'int'
    ],
];

$validator = new \Mascame\OutputValidator\OutputValidator($requiredFields, $optionalFields, $arrayFields);

$validator->checkIntegrity($data); // true
```

### Adding validators

Extend `AbsctractValidator` and implement the required `isValid` method.

Then add it to the validator like this:

```php
$data = [
    'my-birthday' => '1980-01-01'
];

$requiredFields = ['my-birthday' => 'birthdayValidator'];

$validator = new \Mascame\OutputValidator\OutputValidator($requiredFields);

$validator->withValidators([
    'birthdayValidator' => \Namespace\MyBirthday::class
]);

$validator->checkIntegrity($data); // true|false
```

##Contributing

Thank you for considering contributing! You can contribute at any time forking the project and making a pull request.

##Support

If you need help or any kind of support, please send an e-mail to Marc Mascarell at marcmascarell@gmail.com.

##License

MIT