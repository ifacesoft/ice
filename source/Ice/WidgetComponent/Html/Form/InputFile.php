<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Helper\Directory;

class Html_Form_InputFile extends FormElement
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

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
            if (count($downloadUrl) == 2) {
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

    public function save(Model $model)
    {
        $fileData = explode(',', $this->getValue(false));

        $realPathCallback = $this->getOption('realPathCallback');
        $webPathCallback = $this->getOption('webPathCallback');

        if (count($fileData) === 4 && $realPathCallback && $webPathCallback) {
            $params = ['pk' => $model->getPkValue(), 'name' => $fileData[2], 'size' => $fileData[3]];

            $realPath = call_user_func($realPathCallback, $params);

            Directory::get(dirname($realPath));
            file_put_contents($realPath, base64_decode($fileData[1]));

            return [$this->getName() => call_user_func($webPathCallback, $params)];
        }

        return [];
    }
}