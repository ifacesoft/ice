<?php
return array(
    'route_pk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => false,
            'default' => null,
        ),
    'actions' =>
        array(
            'type' => 'varchar(128)',
            'nullable' => false,
            'default' => 'Http_Status/code404',
        ),
    'params__json' =>
        array(
            'type' => 'text',
            'nullable' => false,
            'default' => null,
        ),
    'titleAction' =>
        array(
            'type' => 'varchar(128)',
            'nullable' => false,
            'default' => 'Title',
        ),
    'patterns__json' =>
        array(
            'type' => 'text',
            'nullable' => false,
            'default' => null,
        ),
    'route' =>
        array(
            'type' => 'varchar(255)',
            'nullable' => false,
            'default' => '/404/',
        ),
    'layoutAction' =>
        array(
            'type' => 'varchar(128)',
            'nullable' => false,
            'default' => 'Layout_Main',
        ),
    'layoutTemplate' =>
        array(
            'type' => 'varchar(128)',
            'nullable' => true,
            'default' => null,
        ),
);