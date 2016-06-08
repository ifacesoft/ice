<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 07.06.16
 * Time: 12:12
 */

namespace Ice\Validator;


use Ice\Core\Validator;
use Ice\Helper\Directory;

class File_Exists extends Validator
{

    /**
     * Validate data
     *
     * example usage:
     * ```php
     *      $params = 'VALIDATOR_PARAM';
     * ```
     * or
     * ```php
     *      $params = [
     *          'PATTERN_NAME' => [
     *              'params' => [
     *                  'PATTERN_PARAM_NAME1' => 'PATTERN_PARAM_VALUE1',
     *                  'PATTERN_PARAM_NAME2 => 'PATTERN_PARAM_VALUE2'
     *              ],
     *              'message => 'validate failed for {$0}',
     *              'exception => 'Ice:Http_Not_Found'
     *              ]
     *          ]
     *      ];
     * ```
     *
     * @param  $value
     * @param  mixed $params
     * @return boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function validate($value, $params = [])
    {
        $path = is_array($params) && isset($params['path']) ? $params['path'] : '';

        if ($path) {
            $path = Directory::get($path);
        }

        return file_exists($path . $value);
    }

    public function getMessage()
    {
        return 'File \'{$1}\' not found';
    }
}