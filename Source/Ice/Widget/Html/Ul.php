<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\Html_Ul_Li;

class Html_Ul extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        return [];
    }

    public function li($componentName, array $options = [])
    {
        return $this->addPart(new Html_Ul_Li($componentName, $options, null, $this));
    }
}