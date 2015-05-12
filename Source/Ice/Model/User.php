<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class User
 *
 * @property mixed user_pk
 * @property mixed user_phone
 * @property mixed user_email
 * @property mixed user_name
 * @property mixed surname
 * @property mixed patronymic
 * @property mixed user_active
 * @property mixed user_created
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class User extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.www',
		    'scheme' => [],
		    'columns' => [],
		    'indexes' => [],
		    'references' => [],
		    'relations' => [
		        'oneToMany' => [],
		        'manyToOne' => [
		            'ice_account' => 'user__fk',
		            'ice_user_role_link' => 'user__fk',
		        ],
		        'manyToMany' => [
		            'ice_role' => 'ice_user_role_link',
		        ],
		    ],
		];
    }
}