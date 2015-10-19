<?php

namespace Ice\Action;

use Ice\Core\Action;

class Security_EmailPassword_Register_Submit extends Security_Register
{

    /**
     * Action config
     *
     * @return array
     */
       protected static function config()
       {
           return [
               'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
               'cache' => ['ttl' => -1, 'count' => 1000],
               'actions' => [],
               'input' => [],
               'output' => []
           ];
       }

      /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        // TODO: Implement run() method.
    }

    /**
     * Register
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @param array $userData
     * @param null $dataSource
     * @return Security_Account
     * @throws \Ice\Core\Exception
     * @version 1.1
     * @since   0.1
     */
    public function register(array $userData = [], $dataSource = null)
    {
        /** @var Model $accountModelClass */
        $accountModelClass = $this->getAccountModelClass();

        if (!$accountModelClass) {
            return $this->getLogger()
                ->exception(
                    ['Unknown accountModelClass', [], $this->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        $accountData = $this->validate();

        $accountData['password'] = password_hash($accountData['password'], PASSWORD_DEFAULT);

        return $this->signUp($accountModelClass, $accountData, $userData, $dataSource);
    }
}