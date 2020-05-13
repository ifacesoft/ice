<?php

namespace Test\Ifacesoft\Ice\Core;

use Ifacesoft\Ice\Core\Service;

class ServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @param Service|string $serviceClass
     * @param $serviceName
     * @param array $serviceParams
     * @param array $serviceConfig
     * @return Service
     * @throws \Exception
     */
    protected function create($serviceClass, $serviceName = null, $serviceParams = [], $serviceConfig = [])
    {
        $service = $serviceClass::create($serviceName, $serviceParams, $serviceConfig);

        $this->assertNotNull($service);

        $this->assertInstanceOf($serviceClass, $service);

//        if ($id = $serviceClass::getServiceId($service->get(), $service->getConfig())) {
//            $this->assertTrue(ServiceLocator::getInstance()->has($id));
//        } else {
//            $this->assertFalse(ServiceLocator::getInstance()->has($serviceClass));
//        }

        return $service;
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}