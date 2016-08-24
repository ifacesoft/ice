<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\Html_Table_Tr_Td;
use Ice\WidgetComponent\Html_Table_Tr_Th;

class Html_Table_Tr extends Widget
{
    protected static function config()
    {
        $config =  parent::config();

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

    public function td($componentName, array $options = [])
    {
        return $this->addPart(new Html_Table_Tr_Td($componentName, $options, null, $this));
    }

    public function th($componentName, array $options = [])
    {
        return $this->addPart(new Html_Table_Tr_Th($componentName, $options, null, $this));
    }
}