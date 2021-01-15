<?php

namespace Ice\WidgetComponent;

class Form_Period extends Form_Date
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

    /**
     * @return string
     */
    public function getFromName() {
        return $this->getOption('fromName', $this->getName() . '_from');
    }

    /**
     * @return string
     */
    public function getToName() {
        return $this->getOption('toName', $this->getName() . '_to');
    }
}