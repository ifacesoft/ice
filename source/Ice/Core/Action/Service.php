<?php

namespace Ice\Core;

use Ice\DataProvider\Cli;
use Ice\DataProvider\Redis;
use Ice\Helper\Date;
use Ice\Helper\File;
use Ice\Helper\Serializer;

abstract class Action_Service extends Action
{
    protected $iterations = 0;

    /** Run action
     *
     * @param  array $input
     * @return array
     * @throws Exception
     * @throws \Ice\Exception\Config_Error
     * @throws \Ice\Exception\Error
     * @throws \Ice\Exception\FileNotFound
     */
    public function run(array $input)
    {
        $logger = $this->getLogger();

        if ($input['force']) {
            $this->lock($input);
        }

        while ($this->isLocked($logger, $input)) {
            $logger->info('Service: run (' . $this->iterations . ')', Logger::INFO);

            $this->service($input);

            $logger->info('Service: wait (' . $this->iterations . ')', Logger::INFO);
        }

        return ['success' => $logger->info('Service successfully stopped...', Logger::SUCCESS)];
    }

    abstract public function service(array $input);

    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => ['force' => ['providers' => Cli::class, 'default' => false]],
            'output' => []
        ];
    }

    protected function isLocked(Logger $logger, array $input, $ttl = 60)
    {
        $lockFilePath = $this->getLockFilePath($input);

        $lockFile = File::loadData($lockFilePath, false);

        if ($lockFile === null && !$this->iterations) {
            $logger->info('Service started.. (Iteration: #' . $this->iterations . ')', Logger::INFO);
            return $this->lock($input);
        }

        if ($lockFile === null || ($lockFile['iterations'] !== $this->iterations && !Date::expired(filemtime($lockFilePath), $ttl))) {
            $logger->info('Service stopped.. (Lock file not found or iterations mismatch)', Logger::WARNING);

            return false;
        }

        $logger->info('Service runned.. (Iteration: #' . $this->iterations . ')', Logger::INFO);
        return $this->lock($input);
    }

    private function getLockFilePath(array $input)
    {
        /** @var Action $actionClass */
        $actionClass = get_class($this);

        $args = urlencode(Serializer::serialize($input));

        return getRunDir() . $actionClass::getClassName() . '__' . $args . '.lock';
    }

    protected function lock(array $input)
    {
        if (!$this->iterations) {
            $this->getServiceDataProvider()->flushAll();
        }

        return File::createData($this->getLockFilePath($input), ['iterations' => ++$this->iterations]);
    }

//    protected function unlock(Logger $logger, array $input) {
//        File::createData($this->getLockFilePath($input), ['iterations' => $this->iterations . '!']);
//        $logger->info('Service finished.. (Iteration: #' . $this->iterations . ')', Logger::INFO);
//
//        return true;
//    }

    public function getServiceDataProvider($index = null)
    {
        return $this->getDProvider(Redis::class, 'default', $index);
    }
}