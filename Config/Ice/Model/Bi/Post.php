<?php
return [
    'Ice\\Core\\Model' => [
        'post_pk' => 'post_pk',
        'post_name' => 'post_name',
        'post_translit' => 'post_translit',
        'post_text' => 'post_text',
        'post_active' => 'post_active',
        'post_created' => 'post_created',
        'blog__fk' => 'blog__fk',
    ],
    'Ice\\Core\\Validator' => [
        'post_pk' => [],
        'post_name' => [],
        'post_translit' => [],
        'post_text' => [],
        'post_active' => [
            0 => 'Ice:Not_Null',
        ],
        'post_created' => [],
        'blog__fk' => [],
    ],
    'Ice\\Core\\Form' => [
        'post_pk' => 'Number',
        'post_name' => 'Text',
        'post_translit' => 'Text',
        'post_text' => 'Textarea',
        'post_active' => 'Checkbox',
        'post_created' => 'Date',
        'blog__fk' => 'Number',
    ],
    'Ice\\Core\\Data' => [
        'post_pk' => 'text',
        'post_name' => 'text',
        'post_translit' => 'text',
        'post_text' => 'text',
        'post_active' => 'text',
        'post_created' => 'text',
        'blog__fk' => 'text',
    ],
];