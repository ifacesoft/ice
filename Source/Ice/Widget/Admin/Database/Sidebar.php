<?php

namespace Ice\Widget;

use Ebs\Security\Ebs;
use Ice\Core\Config;
use Ice\Core\Module;
use Ice\Core\Security;
use Ice\Exception\Http_Forbidden;

class Admin_Database_Sidebar extends Nav
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Nav::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => Admin_Database_Database::getClass()],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['schemeName' => ['providers' => 'router']],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws Http_Forbidden
     */
    protected function build(array $input)
    {
        if (!isset($input['schemeName'])) {
            return [];
        }

        $this->addClasses('nav-sidebar');

        $module = Module::getInstance();

        $currentDataSourceKey = $module->getDataSourceKeys()[$input['schemeName']];

        $config = Config::getInstance(Admin_Database_Database::getClass());

        if (!isset($config->gets()[$currentDataSourceKey])) {
            throw new Http_Forbidden(['Scheme {$0} not found', $currentDataSourceKey]);
        }

        $scheme = Config::create($currentDataSourceKey, $config->gets()[$currentDataSourceKey]);

        /** @var Ebs $security */
        $security = Security::getInstance();

        if (!$security->check($scheme->gets('roles', false))) {
            throw new Http_Forbidden('Access denied');
        }

        foreach ($scheme->gets('tables') as $tableName => $table) {
            $this->li(
                $tableName,
                [
                    'label' => $tableName,
                    'resource' => Module::getInstance()->getModelClass($tableName, $currentDataSourceKey),
                    'route' => 'ice_admin_database_table',
                    'params' => [
                        'schemeName' => $input['schemeName'],
                        'tableName' => $tableName
                    ],
                    'access' => ['roles' => $scheme->getConfig('tables/' . $tableName)->gets('roles', false)]
                ]
            );
        }
    }
}