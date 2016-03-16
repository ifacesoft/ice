<?php
$sheet->setCellValue($column . $index, isset($params[$title]) ? $params[$title] : $title);
$sheet->getCell($column . $index)->getHyperlink()->setUrl((!empty($options['href']) ? $options['href'] : '') . '#' . $name);