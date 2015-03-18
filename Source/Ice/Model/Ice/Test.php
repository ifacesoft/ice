<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Ice_Test
 *
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class Ice_Test extends Model
{
    protected static function config()
    {
        return [
		    'revision' => '03171513_um',
		    'dataSourceKey' => 'Ice\\Data\\Source\\Mongodb/default.test',
		    'scheme' => [
		        'tableName' => 'ice_test',
		        'engine' => 'MongoDB',
		        'charset' => 'utf-8',
		        'comment' => 'ice_test',
		    ],
		    'schemeHash' => 2457038805,
		    'columns' => [],
		    'indexes' => [],
		    'indexesHash' => 223132457,
		    'columnsHash' => 223132457,
		];
    }
}