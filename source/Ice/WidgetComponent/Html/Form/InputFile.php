<?php

namespace Ice\WidgetComponent;

use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Helper\Directory;

class Html_Form_InputFile extends FormElement
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

    /**
     * @return array|false|mixed|string|null
     * @throws Config_Error
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    public function getDownloadUrl()
    {
        $downloadUrl = $this->getOption('downloadUrlCallback', '');

        if ($downloadUrl === true) {
            return $this->getValue();
        }

        if (empty($downloadUrl)) {
            $downloadUrl = null;
        }

        $downloadUrl = (array)$downloadUrl;

        if ($downloadUrl) {
            if (count($downloadUrl) === 2) {
                list($callback, $paramNames) = $downloadUrl;
            } else {
                $callback = reset($downloadUrl);
                $paramNames = [];
            }

            $params = [];

            foreach ((array)$paramNames as $paramName) {
                $params[$paramName] = $this->get($paramName);
            }

            $downloadUrl = call_user_func($callback, $params);
        }

        return $downloadUrl;
    }

    /**
     * @param Model $model
     * @return array
     * @throws Exception
     * @throws Config_Error
     * @throws Error
     * @throws FileNotFound
     */
    public function save(Model $model)
    {
        $fileData = explode(',', $this->getValue(false));

        $realPathCallback = $this->getOption('realPathCallback');
        $webPathCallback = $this->getOption('webPathCallback');

        if ($realPathCallback && $webPathCallback && count($fileData) === 4) {
            $params = ['pk' => $model->getPkValue(), 'name' => $fileData[2], 'size' => $fileData[3]];

            foreach ((array) $realPathCallback($model, $params) as $realPath) {
                Directory::get(dirname($realPath));
                file_put_contents($realPath, base64_decode($fileData[1]));
            }

            return [$this->getName() => $webPathCallback($model, $params)];
        }

        return [];
    }
}
