<?php

namespace Ifacesoft\Ice\Framework\Infrastructure\Core;

use Exception;
use Ifacesoft\Ice\Core\Domain\Core\Module;
use Ifacesoft\Ice\Core\Domain\Data\Dto;
use Ifacesoft\Ice\Core\Infrastructure\Core\Application;
use Ifacesoft\Ice\Core\Infrastructure\Core\Application\Action as ApplicationActon;

abstract class Action extends ApplicationActon
{
    protected static function config()
    {
        return array_merge_recursive(
            [
                'services' => [
                    'application' => [
                        'class' => Application::class
                    ],
                ]
            ],
            parent::config()
        );
    }

    /**
     * @param array $data
     * @return Dto
     * @throws Exception
     */
    protected function createParams(array $data)
    {
        /** @var Application $application */
        $application = $this->getService('application');

        $iceModule = $application->getClassModule(self::class);

        require_once $iceModule->getDir(Module::PATH_SOURCE) . 'bootstrap.php';

        return parent::createParams($data);
    }
}
