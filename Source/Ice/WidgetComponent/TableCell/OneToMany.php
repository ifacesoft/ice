<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;

class TableCell_ManyToMany extends TableCell_Span
{
    public function getValue()
    {
        Debuger::dump($this); die();
        
        return parent::getValue();
    }


}