<?php

namespace Ice\Core;

use Ice\DataProvider\Cli;
use Ice\DataProvider\Redis;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Helper\Date;
use Ice\Helper\Json;
use Ice\Helper\Php;
use Ice\Helper\Profiler;
use Ice\Helper\Type_String;
use Ice\Message\Mail;

abstract class Action_Worker extends Action
{
    const WORKER_KEY = 'worker';
    const TASK_KEY = 'task';

    /**
     * @param array $input
     * @return array|void
     * @throws Exception
     * @throws \Exception
     */
    final public function run(array $input)
    {
        $provider = $this->getProvider();

        $workerKey = $input['workerKey']
            ? $input['workerKey']
            : $this->getWorkerKey($input['async']);

        if ($input['hash']) {
            $this->task($workerKey, $input['hash']);

            if ($input['isLastTask']) {
                $this->finish($input);
            }

            return;
        }

        if ($worker = $provider->hGet($workerKey)) {
            if ($input['update']) {
                foreach (['max', 'delay', 'ttl', 'bg'] as $option) {
                    if ($worker[$option] != $input[$option]) {
                        $worker[$option] = $input[$option];
                    }
                }

                $provider->hSet($workerKey, $worker, true);

                return;
            }

            if ($input['force']) {
                foreach ($provider->getKeys() as $key) {
                    $provider->delete($key);
                }
            } else {
                $this->getLogger()->exception(['Worker {$0}: is already running - {$1}', [get_class($this), Type_String::printR($worker)]], __FILE__, __LINE__);
            }
        }

        $worker = array_merge(
            array_intersect_key($input, self::config()['input']),
            [
                'workerKey' => $workerKey,
                'start_datetime' => Date::get(),
                'started_at' => microtime(true),
                'errors' => []
            ]
        );

        return $this->dispatch($worker, array_diff_key($input, $worker));
    }

    /**
     * @return DataProvider|Redis
     * @throws Exception
     */
    protected function getProvider()
    {
        return Redis::getInstance('default', get_class($this));
    }

    /**
     * @param null $async
     * @return string
     * @throws \Exception
     */
    private function getWorkerKey($async = null)
    {
        return self::WORKER_KEY . '/' . get_class($this) . ($async ? '/' . crc32(random_int(0, 999999999)) : '');
    }

    /**
     * @param $workerKey
     * @param $hash
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    private function task($workerKey, $hash)
    {
        /** @var Redis $provider */
        $provider = $this->getProvider();

        $taskKey = $this->getTaskKey($workerKey, $hash);

        $task = [];

        try {
            $task = $provider->hGet($taskKey);

            if (!isset($task['task'])) {
                $this->getLogger()->exception(['Worker task #{$1} not found - {$2}', [get_class($this), $taskKey, Type_String::printR($task)]], __FILE__, __LINE__);
            }

            $this->job($task['task']);

            $provider->delete($taskKey);
        } catch (\Exception $e) {
            $this->getLogger()->error(['Worker task #{$1} failed - {$2}', [get_class($this), $taskKey, Type_String::printR($task)]], __FILE__, __LINE__, $e);

            $provider->delete($taskKey);
        } catch (\Throwable $e) {
            $this->getLogger()->error(['Worker task #{$1} failed - {$2}', [get_class($this), $taskKey, Type_String::printR($task)]], __FILE__, __LINE__, $e);

            $provider->delete($taskKey);
        }
    }

    /**
     * @param $workerKey
     * @param null $hash
     * @return string
     */
    private function getTaskKey($workerKey, $hash = null)
    {
        return $workerKey . '/' . self::TASK_KEY . '/' . ($hash ? $hash : '');
    }

    /**
     * @param array $task
     * @return void
     */
    abstract public function job(array $task);

    /**
     * @param array $input
     * @return mixed
     */
    private function finish(array $input)
    {
        $report = $this->flush($input);

        Logger::log('Finish done.', get_class($this));

        return $report;
    }

    abstract protected function flush(array $input);

    protected static function config()
    {
        $config = parent::config();

        $config['input']['force'] = ['providers' => ['default', Cli::class], 'default' => 0];
        $config['input']['update'] = ['providers' => ['default', Cli::class], 'default' => 0];
        $config['input']['max'] = ['providers' => ['default', Cli::class], 'default' => 1];
        $config['input']['delay'] = ['providers' => ['default', Cli::class], 'default' => 100000];
        $config['input']['ttl'] = ['providers' => ['default', Cli::class], 'default' => 600];
        $config['input']['workerKey'] = ['providers' => ['default', Cli::class]];
        $config['input']['hash'] = ['providers' => ['default', Cli::class]];
        $config['input']['bg'] = ['providers' => ['default', Cli::class], 'default' => null];
        $config['input']['limit'] = ['providers' => ['default', Cli::class], 'default' => null];
        $config['input']['report'] = ['providers' => ['default', Cli::class], 'default' => 0];
        $config['input']['async'] = ['providers' => ['default', Cli::class], 'default' => 0];
        $config['input']['isLastTask'] = ['providers' => ['default', Cli::class], 'default' => 0];
        $config['input']['forceFlush'] = ['providers' => ['default', Cli::class], 'default' => 0];
        $config['input']['showParams'] = ['providers' => ['default', Cli::class], 'default' => 1];
        $config['input']['iceEnv'] = ['providers' => ['default', Cli::class]];

        return $config;
    }

    /**
     * @param array $dispatchWorker
     * @param array $params
     * @return array|mixed|null
     * @throws Config_Error
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @throws \Exception
     */
    private function dispatch(array $dispatchWorker, array $params)
    {
        $tasks = (array)$this->getAllTasks(array_merge($params, $dispatchWorker));

        $dispatchWorker['tasks'] = count($tasks);
        $dispatchWorker['completed'] = 0;
        $dispatchWorker['leftTime'] = '';

        $workerKey = $dispatchWorker['workerKey'];

        $provider = $this->getProvider();

        $provider->hSet($workerKey, $dispatchWorker, true);

        $worker = $dispatchWorker;

        $taskCount = 0;

        $this->getLogger()->info('Worker ' . get_class($this) . ' start! ' . Type_String::printR($worker));

        $i = 0;

        $startTime = microtime(true);

        if (empty($tasks)) {
            if ($dispatchWorker['forceFlush']) {
                $this->finish(array_merge($worker, $params));
            }
        }

        foreach ($tasks as $task) {
            $i++;

            $leftTasks = $dispatchWorker['tasks'] - $i;
            $isLastTask = (int)!$leftTasks;

            $worker = $provider->hGet($workerKey);

            if ($dispatchWorker['started_at'] !== $worker['started_at']) {
                break;
            }

            if ($worker['limit'] !== null && $worker['limit'] < ++$taskCount) {
                break;
            }

            if ($bg = $worker['bg'] === null ? (int)$worker['max'] > 1 && $worker['tasks'] > 1 : (bool)$worker['bg']) {
                Php::iniSet('memory_limit', '4G');
            }

            usleep((int)$worker['delay']);

            $waitTime = 0;
            $startWeightTime = microtime(true);

            while (($activeTasks = count($provider->getKeys($this->getTaskKey($workerKey)))) >= (int)$worker['max'] && (int)$worker['max'] !== 0) {
                if (!$waitTime) {
                    Logger::log('(' . $activeTasks . '/' . $worker['max'] . ') worker wait... ', get_class($this));
                }

                $waitTime++;
                $waitPrettyTime = Profiler::getPrettyTime(microtime(true) - $startWeightTime);

                fwrite(STDOUT, '.');

                if (!($waitTime % 100)) {
                    fwrite(STDOUT, '[' . $waitPrettyTime . ']');
                }

                usleep((int)$worker['delay']);
            }

            if ($waitTime) {
                fwrite(STDOUT, "\n");
            }

            $task = array_merge($params, $task); // именно в этом порядке, ибо нехер

            $hash = crc32(Json::encode($task));

            $provider->hSet($this->getTaskKey($workerKey, $hash), ['started_at' => time(), 'task' => $task], $worker['ttl']);

            /** @var Action_Worker $class */
            $class = get_class($this);

            try {
                $avgTime = round((microtime(true) - $startTime) / $i, 3);
                $estimateTime = Profiler::getPrettyTime(microtime(true) - $worker['started_at']);
                $leftTime = Profiler::getPrettyTime(($dispatchWorker['tasks'] - $i) * $avgTime);
                $perSec = round(1 / $avgTime, 3);

                $taskLog = $dispatchWorker['showParams'] ? Type_String::printR($task, false) : '';

                $runLog = '[ ' . $i . '/' . $dispatchWorker['tasks'] . ' (' . ($dispatchWorker['tasks'] - $i) . ') - ' . ($activeTasks + 1) . '/' . $worker['max'] . ' : ' . $estimateTime . ']';

                Logger::log($runLog . ' #' . $hash . ' ' . $taskLog . ' [ timePerTask: ' . $avgTime . ' | tasksPerSec: ' . $perSec . ' | leftTime: ' . $leftTime . ' ]', get_class($this));

                $provider->hSet($workerKey, ['completed' => $i, 'leftTime' => $leftTime]);

                if ($isLastTask) {
                    Logger::log('Finishing..', get_class($this));
                }

                $taskParams = ['workerKey' => $workerKey, 'hash' => $hash, 'task' => $taskLog, 'isLastTask' => $isLastTask];

                if ($worker['iceEnv']) {
                    $taskParams['iceEnv'] = $worker['iceEnv'];
                }

                $class::call($taskParams, 0, $bg);
            } catch (\Exception $e) {
                $this->getLogger()->error(['Worker {$0}: Task #{$1} failed - {$2}', [get_class($this), $hash, Type_String::printR($task)]], __FILE__, __LINE__, $e);
            } catch (\Throwable $e) {
                $this->getLogger()->error(['Worker {$0}: Task #{$1} failed - {$2}', [get_class($this), $hash, Type_String::printR($task)]], __FILE__, __LINE__, $e);
            }
        }

        $worker['finish_datetime'] = Date::get();
        $worker['time'] = Profiler::getPrettyTime(microtime(true) - $worker['started_at']);

        $this->getLogger()->info('Worker ' . get_class($this) . ' complete! ' . Type_String::printR($worker));

        if ($worker['report']) {
            Mail::create()
                ->setRecipients('das@landev.ru')
                ->setSubject('Worker ' . get_class($this) . ' complete!')
                ->setBody(Type_String::printR($worker))
                ->send();
        }

        $provider->delete($workerKey);

        return $worker;
    }

    /**
     * @param array $input
     * @return array
     * @throws Exception
     */
    abstract public function getAllTasks(array $input);

    /**
     * @throws Exception
     */
    protected function hit()
    {
        $this->getProvider()->getKeys();
    }
}