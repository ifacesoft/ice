<?php
return [
    'mapping' => [
        'account_pk' => 'account_pk',
        'user__fk' => 'user__fk',
        'login' => 'login',
        'password' => 'password',
        'account_active' => 'account_active',
    ],
    'Ice\\Core\\Validator' => [
        'account_pk' => [
            0 => 'Ice:Not_Null',
        ],
        'user__fk' => [],
        'login' => [],
        'password' => [],
        'account_active' => [
            0 => 'Ice:Not_Null',
        ],
    ],
    'Ice\\Core\\Form' => [
        'account_pk' => 'Number',
        'user__fk' => 'Number',
        'login' => 'Text',
        'password' => 'Text',
        'account_active' => 'Checkbox',
    ],
    'Ice\\Core\\Data' => [
        'account_pk' => 'text',
        'user__fk' => 'text',
        'login' => 'text',
        'password' => 'text',
        'account_active' => 'text',
    ],
];