<?php
return [
    'Ice\\Core\\Model' => [
        'user_pk' => 'user_pk',
        'user_phone' => 'user_phone',
        'user_email' => 'user_email',
        'user_name' => 'user_name',
        'surname' => 'surname',
        'patronymic' => 'patronymic',
        'user_active' => 'user_active',
        'user_created' => 'user_created',
    ],
    'Ice\\Core\\Validator' => [
        'user_pk' => [
            0 => 'Ice:Not_Null',
        ],
        'user_phone' => [],
        'user_email' => [],
        'user_name' => [],
        'surname' => [],
        'patronymic' => [],
        'user_active' => [
            0 => 'Ice:Not_Null',
        ],
        'user_created' => [],
    ],
    'Ice\\Core\\Form' => [
        'user_pk' => 'Number',
        'user_phone' => 'Text',
        'user_email' => 'Text',
        'user_name' => 'Text',
        'surname' => 'Text',
        'patronymic' => 'Text',
        'user_active' => 'Checkbox',
        'user_created' => 'Date',
    ],
    'Ice\\Core\\Data' => [
        'user_pk' => 'text',
        'user_phone' => 'text',
        'user_email' => 'text',
        'user_name' => 'text',
        'surname' => 'text',
        'patronymic' => 'text',
        'user_active' => 'text',
        'user_created' => 'text',
    ],
];