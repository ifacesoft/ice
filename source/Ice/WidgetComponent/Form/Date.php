<?php

namespace Ice\WidgetComponent;

use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\QueryBuilder;
use Ice\Core\Request;
use Ice\Helper\Date;

class Form_Date extends FormElement_TextInput
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

    public function getDateMomentFormat()
    {
        return Date::convertPHPToFakeMomentFormat($this->getDateFormat());
//        return Date::convertPHPToMomentFormat($this->getDateFormat());
    }

    public function getDateFormat()
    {
        $dateFormat = $this->getOption('dateFormat', null);

        if (!$dateFormat) {
            $dateConfig = Module::getInstance()->getDefault('date');

            return $dateConfig->get('format', Date::FORMAT_MYSQL);
        }

        return $dateFormat;
    }

    public function getLocale()
    {
        return Request::locale();
    }

    public function save(Model $model)
    {
        $value = $this->getValue();

        if ($this->getOption('readonly', false) || $this->getOption('disabled', false)) {
            return [];
        }

        if (array_key_exists('defaultValue', $this->getOption()) && ($value === "" || is_null($value))) {
            return [$this->getName() => $this->getOption('defaultValue')];
        }

        return [$this->getName() => Date::get($value, Date::FORMAT_MYSQL, null)];
    }

    public function filter(QueryBuilder $queryBuilder)
    {
        $option = array_merge($this->getOption(), $this->getOption('filter', []));

        $value = $this->getValue();

        if ($value === null || $value === '' || (is_array($value)) && empty($value)) {
            return $queryBuilder;
        }

        if (!isset($option['comparison'])) {
            $option['comparison'] = 'like';
        }

        if (!isset($option['modelClass'])) {
            $option['modelClass'] = $queryBuilder->getModelClass();
        }

        foreach ((array)$value as $val) {
            $val = Date::get($val, Date::FORMAT_MYSQL);

            switch ($option['comparison']) {
                case '=':
                    $queryBuilder->eq([$this->getName() => $val], $option['modelClass']);
                    break;
                case 'like':
                default:
                    $queryBuilder->like($this->getName(), '%' . $val . '%', $option['modelClass']);
            }
        }

        return $queryBuilder;
    }


}