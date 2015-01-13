<?php
return [
    'Ice\\Core\\Model' => [
        'test_pk' => 'id',
        'test_name' => 'test_name',
        'name2' => 'name2',
    ],
    'Ice\\Core\\Validator' => [
        'test_pk' => [
            0 => 'Ice:Not_Null',
        ],
        'test_name' => [],
        'name2' => [
            0 => 'Ice:Not_Null',
        ],
    ],
    'Ice\\Core\\Form' => [
        'test_pk' => 'Number',
        'test_name' => 'Text',
        'name2' => 'Text',
    ],
    'Ice\\Core\\Data' => [
        'test_pk' => 'text',
        'test_name' => 'text',
        'name2' => 'text',
    ],
];