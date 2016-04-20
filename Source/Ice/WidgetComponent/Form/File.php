<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;

class Form_File extends FormElement
{
    public function getShowFile()
    {
        $showFile = $this->getOption('showFile', '');

        if ($showFile === true) {
            return $this->getValue();
        }

        if ($showFile) {
            list($method, $paramNames) = $showFile;

            $params = [];

            foreach ((array)$paramNames as $paramName) {
                $params[$paramName] = $this->get($paramName);
            }

            $showFile = call_user_func($method, $params);
        }

        return $showFile;
    }

    public function save(Model $model)
    {
        $fileData = explode(',', $this->getValue(false));

        if (count($fileData) === 2) {
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
            $ext = '';
            foreach ($extensions as $k => $v) {
                if (strpos($fileData[0], $k)) {
                    $ext = $v;
                    break;
                }
            }
            if ($ext) {
                $realPathMethod = $this->getOption('real_path');
                $webPathMethod = $this->getOption('web_path');
                $path = $model->$realPathMethod($ext);
                $ifp = fopen($path, "wb");
                if ($ifp) {
                    fwrite($ifp, base64_decode($fileData[1]));
                    fclose($ifp);
                    return [$this->getName() => $model->$webPathMethod($ext)];
                }
            }
        }

        return parent::save($model);
    }
}