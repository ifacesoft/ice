<?php
return array(
    'account_type_pk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => false,
            'default' => null,
        ),
    'account_type_delegate_name' =>
        array(
            'type' => 'varchar(32)',
            'nullable' => false,
            'default' => null,
        ),
    'account_type_is_active' =>
        array(
            'type' => 'tinyint(4)',
            'nullable' => false,
            'default' => '1',
        ),
);