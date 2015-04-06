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
		    'revision' => '04061152_eh',
		    'dataSourceKey' => 'Ice\Data\Source\Mongodb/default.test',
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
		    'references' => [],
		    'referencesHash' => '',
		    'oneToMany' => [],
		    'oneToManyHash' => 223132457,
		    'manyToOne' => [],
		    'manyToOneHash' => 223132457,
		    'manyToMany' => [],
		    'manyToManyHash' => 223132457,
		];
    }
}