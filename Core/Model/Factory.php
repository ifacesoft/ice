<?php
namespace ice\core\model;

use ice\core\Model;

/**
 * @author dp
 * @package Ice
 *
 */
abstract class Factory extends Defined
{
    /**
     * Получение делегата модели
     *
     * @param $delegateName
     * @return Model|null
     */
    public static function getDelegate($delegateName)
    {
        /** @var Model $modelclass */
        $modelclass = get_called_class();

        return $modelclass::getQueryBuilder()
            ->select('/delegate_name')
            ->eq('/delegate_name', $delegateName)
            ->eq('/is_active', 1)
            ->execute()
            ->getModel();
    }
}