<?php

namespace Ice\Widget;

use Ice\Core\Debuger;

class Block_Render extends Block
{
    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        foreach ($input as $name => $widgetClass) {
            $widgetClass = (array)$widgetClass;

            if (count($widgetClass) == 3) {
                list($widgetClass, $widgetParams, $instanceKey) = $widgetClass;
            } else if (count($widgetClass) == 2) {
                list($widgetClass, $widgetParams) = $widgetClass;
                $instanceKey = null;
            } else {
                $widgetClass = reset($widgetClass);
                $widgetParams = [];
                $instanceKey = null;
            }

            $this->widget($name, ['widget' => $this->getWidget([$widgetClass, $widgetParams, $instanceKey])]);
        }

        return [];
    }
}