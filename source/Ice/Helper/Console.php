<?php
/**
 * Ice helper console class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Resource as Core_Resource;
use Ice\Core\Validator as Core_Validator;
use Ice\Exception\Config_Error;
use Ice\Exception\Console_Run;
use Ice\Core\Config as Core_Config;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;

/**
 * Class Console
 *
 * Helper for cli
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Console
{
    const C_BLACK = "\033[0;30m";
    const C_BLACK_B = "\033[1;30m";
    const C_RED = "\033[0;31m";
    const C_RED_B = "\033[1;31m";
    const C_GREEN = "\033[0;32m";
    const C_GREEN_B = "\033[1;32m";
    const C_YELLOW = "\033[0;33m";
    const C_YELLOW_B = "\033[1;33m";
    const C_BLUE = "\033[0;34m";
    const C_BLUE_B = "\033[1;34m";
    const C_MAGENTA = "\033[0;35m";
    const C_CYAN = "\033[0;36m";
    const C_GRAY = "\033[0;37m";
    const C_GRAY_B = "\033[1;37m";
    const C_DEF = "\033[0;39m";

    const BG_BLACK = "\033[40m";
    const BG_RED = "\033[41m";
    const BG_GREEN = "\033[42m";
    const BG_YELLOW = "\033[43m";
    const BG_BLUE = "\033[44m";
    const BG_MAGENTA = "\033[45m";
    const BG_CYAN = "\033[46m";
    const BG_GRAY = "\033[47m";
    const BG_DEF = "\033[49m";

    const RESET = "\033[0m";

    /**
     * Return stylized header text for console
     *
     * @param  $string
     * @param  Core_Resource $resource
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getHeader($string, Core_Resource $resource)
    {
        $string = $resource->get($string);

        $padding = 5;
        $length = strlen($string) + $padding * 2;
        $half = $length / 2;
        $residue = $length % 2;

        return self::getText(str_repeat(' ', $half), Console::C_BLACK, Console::BG_GRAY) .
            self::getText(str_repeat(' ', $half + $residue), Console::C_BLACK, Console::BG_GRAY) . "\n" .
            self::getText(
                str_repeat(' ', $padding) . $string . str_repeat(' ', $padding),
                Console::C_BLACK,
                Console::BG_GRAY
            ) . "\n" .
            self::getText(str_repeat(' ', $half), Console::C_BLACK, Console::BG_GRAY) .
            self::getText(str_repeat(' ', $half + $residue), Console::C_BLACK, Console::BG_GRAY) . "\n";
    }

    /**
     * Return stylized simple text for console
     *
     * @param  $string
     * @param  null $color
     * @param  null $background
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getText($string, $color = null, $background = null)
    {
        $styling = '';

        if ($color) {
            $styling .= $color;
        }

        if ($background) {
            $styling .= $background;
        }

        return $styling . $string . self::RESET;
    }

    /**
     * Return interactive output for define variable from input
     *
     * @param  $class
     * @param  $param
     * @param $desc
     * @param  $data
     * @return string
     * @throws Exception
     * @throws Config_Error
     * @throws Error
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public static function getInteractive($class, $param, $desc, $data)
    {
        /** @var Core_Resource $resource */
        $resource = Core_Resource::create($class);

        $title = $desc . "\n" .
            Console::C_YELLOW . $resource->get($data['title'], $data['default']) . Console::C_GRAY_B;

        fwrite(STDOUT, $title);

        $f = fopen('php://stdin', 'r');
        while ($line = trim(fgets($f))) {
            if (empty($line)) {
                break;
            }

            $errors = 0;
            foreach ((array)$data['validators'] as $validatorName => $params) {
                $validator = null;

                if (is_int($validatorName)) {
                    $validatorName = $params;
                    $params = null;
                }

                if (!Core_Validator::getInstance($validatorName)->validate([$param => $line], $param, (array)$params)) {
                    $errors++;

                    $message = !empty($params) && isset($params['message'])
                        ? $params['message']
                        : 'param {$0} is not valid';

                    Logger::getInstance(Core_Validator::getClass())->info([$message, $param], Core_Logger::DANGER, true, false);
                }
            }

            if ($errors) {
                fwrite(STDOUT, $title);
                continue;
            }

            $data['default'] = $line;
            break;
        }
        fclose($f);

        fwrite(STDOUT, Console::C_GREEN_B . $data['default'] . "\n");
        Logger::getInstance(Core_Resource::getClass())->info('...value is accepted!' . "\n", Core_Logger::SUCCESS, true, false);

        return $data['default'];
    }

    /**
     * @param $source
     * @param $dest
     * @param $keyPath
     * @param $user
     * @param $host
     * @param string $port
     * @param int $fromRemote
     * @return string
     * @throws Config_Error
     * @throws Console_Run
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    public static function scp($source, $dest, $keyPath, $user, $host, $port = '22', $fromRemote = 1)
    {
        if ($fromRemote) {
            $source = '\'' . $user . '@' . $host . ':"' . $source . '"\'';
            $target = '"' . $dest . '"';
        } else {
            $source = '"' . $source . '"';
            $target = '\'' . $user . '@' . $host . ':"' . $dest . '"\'';
        }

        return Console::run('scp -r -P ' . $port . ' -i ' . $keyPath . ' ' . $source . ' ' . $target);
    }

    /**
     * @param $commands
     * @param bool $toDevNull
     * @param bool $toBackground
     * @return string
     *
     * Assuming this is running on a Linux machine, I've always handled it like this:
     *
     * Assuming this is running on a Linux machine, I've always handled it like this:
     *
     * exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
     *
     * This launches the command $cmd, redirects the command output to $outputfile, and writes the process id to $pidfile.
     *
     * That lets you easily monitor what the process is doing and if it's still running.
     *
     * function isRunning($pid){
     * try{
     * $result = shell_exec(sprintf("ps %d", $pid));
     * if( count(preg_split("/\n/", $result)) > 2){
     * return true;
     * }
     * }catch(Exception $e){}
     *
     * return false;
     * }
     *
     * @throws Config_Error
     * @throws Console_Run *@throws \Ice\Exception\FileNotFound
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @todo передавать command + args (для аргументов применять escapeshellarg)
     */

    public static function run($commands, $toDevNull = false, $toBackground = false)
    {
        $commandString = 'cd ' . MODULE_DIR;

        $andS = ' && \\';
        $and = ' && ';

        foreach ((array)$commands as $command) {
            $commandString .= $andS . "\n" . $command; // && if background wait return?
        }

        if ($toBackground) {
            $commandString = '(' . $commandString . ' &)';
        }

        if ($toDevNull) {
            $commandString .= ' > /dev/null 2>&1';
        }

        $returnCode = -1;

        if ($toBackground) {
            $output = '';

            exec(str_replace($andS . "\n", $and, $commandString), $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Console_Run(
                    ['{$0} [code:  {$1}]', [$commandString, $returnCode]],

                    ['output' => $output],
                    null,
                    null,
                    null,
                    $returnCode
                );
            } else {
                Logger::getInstance(__CLASS__)->info(['{$0} [code:  {$1}]', [$commandString, $returnCode]], Logger::INFO, true);
            }

            return $output;
        }

        ob_start();

        passthru(str_replace($andS . "\n", $and, $commandString), $returnCode);

        $output = ob_get_clean();

        if ($returnCode !== 0) {
            throw new Console_Run(
                ['{$0} [code:  {$1}]', [$commandString, $returnCode]],

                ['output' => $output],
                null,
                null,
                null,
                $returnCode
            );
        } else {
            Logger::getInstance(__CLASS__)->info(['{$0} [code:  {$1}]', [$commandString, $returnCode]], Logger::INFO, true);
        }

        return $output;
    }

    /**
     * @param $commands
     * @param $keyPath
     * @param $user
     * @param $host
     * @param string $port
     * @param bool $toDevNull
     * @param bool $toBackground
     * @return string
     * @throws Config_Error
     * @throws Console_Run
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    public static function sshRun($commands, $keyPath, $user, $host, $port = '22', $toDevNull = false, $toBackground = false)
    {
        $commandString = 'cd /';

        $andS = ' && \\';

        foreach ((array)$commands as $command) {
            $commandString .= $andS . "\n" . $command;
        }

        if ($toBackground) {
            $commandString = '(' . $commandString . ' &)';
        }

        if ($toDevNull) {
            $commandString .= ' > /dev/null 2>&1';
        }

        return Console::run(Console::getCommand('ssh') . ' -p ' . $port . ' -i ' . $keyPath . ' ' . $user . '@' . $host . ' ' . escapeshellarg($commandString));
    }

    /**
     * @param $string
     * @return string
     * @throws Config_Error
     * @throws Exception
     * @throws FileNotFound
     */
    public static function getCommand($string)
    {
        foreach (Core_Config::getInstance(__CLASS__)->gets('pathes') as $path) {
            if (is_executable($path . $string)) {
                return $path . $string;
            }
        }

        return $string;
    }
}
