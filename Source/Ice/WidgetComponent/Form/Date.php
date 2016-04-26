<?php

namespace Ice\WidgetComponent;

use Ice\Core\Module;
use Ice\Core\Request;
use Ice\Helper\Date;

class Form_Date extends FormElement_TextInput
{
    public function getDateFormat()
    {
        $dateFormat = $this->getOption('dateFormat', null);

        if (!$dateFormat) {
            $dateConfig = Module::getInstance()->getDefault('date');

            return $dateConfig->get('format', Date::FORMAT_MYSQL);
        }

        return $dateFormat;
    }
    
    public function getDateMomentFormat() {
        return Date::convertPHPToMomentFormat($this->getDateFormat());
    }

    public function getLocale()
    {
        return Request::locale();
    }
}