<?php

namespace Ice\Widget;

class Block_Render extends Block
{
    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws \Ice\Core\Exception
     */
    protected function build(array $input)
    {
        try {
            foreach ($input as $name => $widgetClass) {
                if (!$widgetClass) {
                    continue;
                }

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
        } catch (\Exception $e) {
            $this->alert($e->getMessage(), ['classes' => 'alert-danger']); // todo: научиться отлавливать. Сейчас оно ловися выше.
        }

        return [];
    }
}