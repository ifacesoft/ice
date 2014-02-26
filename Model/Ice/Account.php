<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dp
 * Date: 24.02.13
 * Time: 15:35
 * To change this template use File | Settings | File Templates.
 */

namespace ice\model\ice;

use ice\core\helper\Request;
use ice\core\Model;
use ice\Exception;

class Account extends Model
{
    const AUTHORIZATION_URL = '/authorization/';
    const REGISTRATION_URL = '/registration/';

    public static function getNewAccount($accountTypeDelegateName, array $fields)
    {
        $accountType = Account_Type::getDelegate($accountTypeDelegateName);

        if (!$accountType) {
            throw new Exception('Не получен тип учетной записи по имени делегата ' . $accountTypeDelegateName);
        }

        $fields['ip'] = Request::ip();
        $fields['account_type__fk'] = $accountType->key();

        return Account::create($fields)->insert();
    }

    public function getUser()
    {
        return User::getQueryBuilder()
            ->innerJoin('User_Account_Link')
            ->eq('User_Account_Link.account__fk', $this->key())
            ->execute()
            ->getModel();
    }
}
