<?php

namespace Ice\Query\Scope;

use Ice\Core\Query_Scope;
use Ice\Core\QueryBuilder;
use Ice\Helper\Date;

class Session extends Query_Scope
{
    public function active(QueryBuilder $queryBuilder, array $data)
    {
        $queryBuilder->gt('FROM_UNIXTIME(UNIX_TIMESTAMP(session_updated_at)+cookie_lifetime)', Date::get(null, null, null));
    }
}