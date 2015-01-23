<?php
/**
 * Ice helper console class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Resource;
use Ice\Core\Response;
use Ice\Core\Validator;

/**
 * Class Console
 *
 * Helper for cli
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
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
     * @param $string
     * @param Resource $resource
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getHeader($string, Resource $resource)
    {
        $string = $resource->get($string);

        $padding = 5;
        $length = strlen($string) + $padding * 2;
        $half = $length / 2;
        $residue = $length % 2;

        return self::getText(str_repeat(' ', $half), Console::C_BLACK, Console::BG_GRAY) .
        self::getText(str_repeat(' ', $half + $residue), Console::C_BLACK, Console::BG_GRAY) . "\n" .
        self::getText(str_repeat(' ', $padding) . $string . str_repeat(' ', $padding), Console::C_BLACK, Console::BG_GRAY) . "\n" .
        self::getText(str_repeat(' ', $half), Console::C_BLACK, Console::BG_GRAY) .
        self::getText(str_repeat(' ', $half + $residue), Console::C_BLACK, Console::BG_GRAY) . "\n";
    }

    /**
     * Return stylized simple text for console
     *
     * @param $string
     * @param null $color
     * @param null $background
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * @param $class Core
     * @param $param
     * @param $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getInteractive($class, $param, $data)
    {
        $resource = $class::getResource();

        $title = Console::C_YELLOW . $resource->get($data['title'], $data['default']) . Console::C_GRAY_B;

        Response::send($title);

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

                if (!Validator::getInstance($validatorName)->validate($line, $params)) {
                    $errors++;

                    $message = !empty($params) && isset($params['message'])
                        ? $params['message']
                        : 'param {$0} is not valid';

                    Response::send(Validator::getLogger()->info([$message, $param], Core_Logger::DANGER, true, false));
                }
            }

            if ($errors) {
                Response::send($title);
                continue;
            }

            $data['default'] = $line;
            break;
        }
        fclose($f);

        Response::send(Console::C_GREEN_B . $data['default']);
        Response::send(Resource::getLogger()->info('...value is accepted!' . "\n", Core_Logger::SUCCESS, true, false));

        return $data['default'];
    }

    public static function run($commands)
    {
        foreach ((array)$commands as $command) {
            Response::send(Console::getText($command, Console::C_GREEN_B) . "\n");
            system($command);
        }
    }
}