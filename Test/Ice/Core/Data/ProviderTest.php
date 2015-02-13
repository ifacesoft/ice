<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 2/3/15
 * Time: 2:48 PM
 */

namespace Ice\Core\Data;


use Ice\Core\Data_Provider;
use Ice\Core\Logger;
use PHPUnit_Framework_TestCase;

class ProviderTest extends PHPUnit_Framework_TestCase
{
    public function testProviders()
    {
        foreach (scandir(ICE_DIR . 'Source/Ice/Data/Provider', 1) as $dataProviderFile) {
            if ($dataProviderFile == '..' || $dataProviderFile == '.') {
                break;
            }

            /** @var Data_Provider $class */
            $class = Data_Provider::getClass('Ice:' . basename($dataProviderFile, '.php'));

            $dataProvider = $class::getInstance();

            if (
                $dataProviderFile == 'Router.php' ||
                $dataProviderFile == 'Mysqli.php' ||
                $dataProviderFile == 'Mongodb.php' ||
                $dataProviderFile == 'Cli.php'
            ) {
                continue;
            }

            $this->assertEquals($dataProvider->set('test', '8'), $dataProvider->get('test'));

            if (
                $dataProviderFile != 'Redis.php' &&
                $dataProviderFile != 'File.php' &&
                $dataProviderFile != 'Apc.php' &&
                $dataProviderFile != 'Cache.php' &&
                $dataProviderFile != 'Repository.php'
            ) {
                $this->assertEquals('10', $dataProvider->incr('test', 2));

                $this->assertEquals('10', $dataProvider->get('test'));

                $this->assertEquals('8', $dataProvider->decr('test', 2));

                $this->assertEquals('8', $dataProvider->get('test'));

                $dataProvider->set('test1', '10');

                if ($dataProviderFile != 'File.php') {
                    $this->assertEquals($dataProvider->get(), ['test' => 8, 'test1' => 10]);

                    $this->assertEquals($dataProvider->getKeys(), ['test', 'test1']);
                }
            }

            $dataProvider->delete('test');

            $this->assertNull($dataProvider->get('test'));

            $dataProvider->set('test2', '12');

            $dataProvider->flushAll();

            if (
                $dataProviderFile != 'Redis.php' &&
                $dataProviderFile != 'File.php' &&
                $dataProviderFile != 'Apc.php' &&
                $dataProviderFile != 'Cache.php' &&
                $dataProviderFile != 'Repository.php'
            ) {
                $this->assertEquals($dataProvider->get(), []);
            }

            $this->assertTrue($dataProvider->closeConnection());
        }
    }
}