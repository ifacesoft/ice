<?php
return [
    'Ice\\Core\\Model' => [
        'blog_pk' => 'blog_pk',
        'blog_name' => 'blog_name',
        'blog_translit' => 'blog_translit',
        'user__fk' => 'user__fk',
        'blog_active' => 'blog_active',
        'blog_created' => 'blog_created',
    ],
    'Ice\\Core\\Validator' => [
        'blog_pk' => [],
        'blog_name' => [
            0 => 'Ice:Not_Null',
        ],
        'blog_translit' => [
            0 => 'Ice:Not_Null',
        ],
        'user__fk' => [],
        'blog_active' => [
            0 => 'Ice:Not_Null',
        ],
        'blog_created' => [
            0 => 'Ice:Not_Null',
        ],
    ],
    'Ice\\Core\\Form' => [
        'blog_pk' => 'Number',
        'blog_name' => 'Text',
        'blog_translit' => 'Text',
        'user__fk' => 'Number',
        'blog_active' => 'Checkbox',
        'blog_created' => 'Date',
    ],
    'Ice\\Core\\Data' => [
        'blog_pk' => 'text',
        'blog_name' => 'text',
        'blog_translit' => 'text',
        'user__fk' => 'text',
        'blog_active' => 'text',
        'blog_created' => 'text',
    ],
];