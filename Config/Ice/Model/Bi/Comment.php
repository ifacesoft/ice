<?php
return [
    'Ice\\Core\\Model' => [
        'comment_pk' => 'comment_pk',
        'comment_title' => 'comment_title',
        'comment_text' => 'comment_text',
        'comment_active' => 'comment_active',
        'comment_created' => 'comment_created',
        'post__fk' => 'post__fk',
    ],
    'Ice\\Core\\Validator' => [
        'comment_pk' => [],
        'comment_title' => [
            0 => 'Ice:Not_Null',
        ],
        'comment_text' => [
            0 => 'Ice:Not_Null',
        ],
        'comment_active' => [
            0 => 'Ice:Not_Null',
        ],
        'comment_created' => [],
        'post__fk' => [],
    ],
    'Ice\\Core\\Form' => [
        'comment_pk' => 'Number',
        'comment_title' => 'Text',
        'comment_text' => 'Textarea',
        'comment_active' => 'Checkbox',
        'comment_created' => 'Date',
        'post__fk' => 'Number',
    ],
    'Ice\\Core\\Data' => [
        'comment_pk' => 'text',
        'comment_title' => 'text',
        'comment_text' => 'text',
        'comment_active' => 'text',
        'comment_created' => 'text',
        'post__fk' => 'text',
    ],
];