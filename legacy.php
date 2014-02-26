<?php
/**
 * Возвращает массив
 *
 * @param array $input Двумерный массив.
 * @param string $columnNames Название колонки.
 * @param string $indexName Имя индекса
 * @return array Колонка $column исходного массива
 */
function array_column($input, $columnNames, $indexName = null)
{
    if (!$columnNames) {
        return $input;
    }
    if (!is_array($input) || empty($input)) {
        return array();
    }
    $result = array();
    $count = count($columnNames);
    foreach ($input as $row) {
        $current = array();
        foreach ((array)$columnNames as $column) {
            $value = isset($row[$column]) ? $row[$column] : null;
            if ($count > 1) {
                $current[$column] = $value;
            } else {
                $current = $value;
            }
        }
        if ($indexName && isset($row[$indexName])) {
            $result[$row[$indexName]] = $current;
        } else {
            $result[] = $current;
        }
    }
    return $result;
}