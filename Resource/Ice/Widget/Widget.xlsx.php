<?php
$result = $options['widget']->getResult(['sheet' => $sheet, 'column' => $column, 'index' => $index]);

foreach ($result as $offset => $parts) {
    foreach ($parts as $partName => $part) {
        $part['renderClass'] = 'Ice\Render\External_PHPExcel';
        $part['index'] += isset($options['indexOffset']) ? $options['indexOffset'] : 0;
        $options['widget']->renderPart($part);
    }
}