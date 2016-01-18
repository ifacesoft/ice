<?php
/** @var Ice\Core\Widget $widget */

foreach ($widget->getResult(['sheet' => $sheet, 'column' => $column, 'index' => $index]) as $offset => $parts) {

    foreach ($parts as $partName => $part) {
        $part['renderClass'] = 'Ice\Render\External_PHPExcel';

        $widget->renderPart($part);
    }
}