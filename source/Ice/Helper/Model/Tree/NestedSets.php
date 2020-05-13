<?php

namespace Ice\Helper;

use Ice\Core\Model as Core_Model;
use Ice\Exception\DataSource_Deadlock;
use Ice\Core\Config as Core_Config;

class Model_Tree_NestedSets
{
    /**
     * @param Core_Model|string $modelClass
     * @param array $row
     * @param null $parentKey
     * @return Core_Model
     * @throws \Exception
     */
    public static function insert($modelClass, array $row, $parentKey = null)
    {
        $dataSource = $modelClass::getDataSource();

        $i = 0;

        while (++$i) {
            try {
                $dataSource->beginTransaction();

                $model = null;

                if ($parentKey) {
                    $node = $modelClass::getSelectQuery(['right_key', 'level'], ['/pk' => $parentKey])->getRow();

                    $row['left_key'] = $node['right_key'];
                    $row['right_key'] = $node['right_key'] + 1;
                    $row['level'] = $node['level'] + 1;

                    $dataSource->executeNativeQuery('UPDATE ' . $modelClass::getSchemeName() . '.' . $modelClass::getTableName() . ' SET `right_key`=`right_key`+2 WHERE `right_key`>=' . (int)$node['right_key']);
                    $dataSource->executeNativeQuery('UPDATE ' . $modelClass::getSchemeName() . '.' . $modelClass::getTableName() . ' SET `left_key`=`left_key`+2 WHERE `left_key`>=' . (int)$node['right_key']);
                } else {
                    $leftKey = $modelClass::createQueryBuilder()->func(['MAX' => 'left_key'], 'right_key')->getSelectQuery(null)->getValue();

                    if (!$leftKey) {
                        $leftKey = 0;
                    }

                    $row['left_key'] = $leftKey + 1;
                    $row['right_key'] = $leftKey + 2;
                    $row['level'] = 1;

                    $dataSource->executeNativeQuery('UPDATE ' . $modelClass::getSchemeName() . '.' . $modelClass::getTableName() . ' SET `right_key`=`right_key`+2 WHERE `right_key`>' . (int)$leftKey);
                    $dataSource->executeNativeQuery('UPDATE ' . $modelClass::getSchemeName() . '.' . $modelClass::getTableName() . ' SET `left_key`=`left_key`+2 WHERE `left_key`>' . (int)$leftKey);
                }

                $model = $modelClass::create($row)->save();


                $dataSource->commitTransaction();

                return $model;

            } catch (DataSource_Deadlock $e) {
                $dataSource->rollbackTransaction($e);

                if ($i >= 60) {
                    throw $e;
                }

                usleep(rand(500, 1000));
            } catch (\Exception $e) {
                $dataSource->rollbackTransaction($e);

                throw $e;
            }
        }
    }

    public static function getConfig()
    {
        return Core_Config::getInstance(__CLASS__);
    }
}