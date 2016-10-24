<?php

namespace Ice\Core;

use Ice\DataProvider\Cli;
use Ice\Helper\Date;
use Ice\Helper\File;
use Ice\Helper\Serializer;

abstract class Action_Service extends Action
{
    protected $iterations = 0;

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

    protected function isLocked(Logger $logger, array $input)
    {
        $lockFile = File::loadData($this->getLockFilePath($input), false);

        if ($lockFile === null) {
            return $this->lock($logger, $input);
        }

        if (!Date::expired(filemtime($this->getLockFilePath($input)), 60) && $lockFile['iterations'] !== $this->iterations) {
            $logger->info('Service stop.. (Lock file not found or iterations mismatch)', Logger::WARNING);

            return false;
        }

        return $this->lock($logger, $input);
    }

    private function getLockFilePath(array $input)
    {
        /** @var Action $actionClass */
        $actionClass = get_class($this);

        $args = urlencode(Serializer::serialize($input));

        return getRunDir() . $actionClass::getClassName() . '__' . $args . '.lock';
    }

    protected function lock(Logger $logger, array $input)
    {
        File::createData($this->getLockFilePath($input), ['iterations' => ++$this->iterations]);
        $logger->info('Service runned.. (Iteration: #' . $this->iterations . ')', Logger::INFO);

        return true;
    }

    protected function unlock(Logger $logger, array $input) {
        File::createData($this->getLockFilePath($input), ['iterations' => $this->iterations . '!']);
        $logger->info('Service finished.. (Iteration: #' . $this->iterations . ')', Logger::INFO);

        return true;
    }
}