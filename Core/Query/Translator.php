<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.01.14
 * Time: 17:51
 */

namespace ice\core;

use ice\Ice;

abstract class Query_Translator
{
    abstract protected function select(Query $query);

    public function translate(Query $query)
    {
        $statementType = $query->getStatementType();

        return $this->$statementType($query);
    }

    /**
     * @param $className
     * @return Query_Translator
     */
    public static function get($className)
    {
        /** @var Data_Provider $dataProvider */
        $dataProvider = Data_Provider::getInstance(Ice::getConfig()->getParam('queryTranslatorDataProviderKey'));

        $queryTranslator = $dataProvider->get($className);

        if ($queryTranslator) {
            return $queryTranslator;
        }

        $queryTranslator = new $className();

        $dataProvider->set($className, $queryTranslator);

        return $queryTranslator;
    }
} 