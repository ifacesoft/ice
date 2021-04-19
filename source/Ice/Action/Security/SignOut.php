<?php

namespace Ice\Action;

use Ice\Core\Security as Core_Security;
use Ice\Widget\Account_Form;

/**
 * Class Security_SignOut
 *
 * @see     \Ice\Core\Action
 * @see     \Ice\Core\Action_Context;
 * @package Ice\Action;
 *
 * @author dp <email>
 */
class Security_SignOut extends Security
{
//    /**
//     * Run action
//     *
//     * @param  array $input
//     * @return array
//     * @throws Http_Redirect
//     * @throws \Ice\Core\Exception
//     */
//    public function run(array $input)
//    {
//        Core_Security::getInstance()->logout();
//
//        throw new Http_Redirect('/');
//    }

    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm ? $accountForm->getLogger() : $this->getLogger();

        try {
            $security = Core_Security::getInstance();

            if (!$security->isAuth()) {
                throw new \Ice\Exception\Security('Вы не авторизованы');
            }

            $this->signOut($security->getAccount());

            return array_merge(parent::run($input), ['success' => 'Выход прошел успешно']);
        } catch (\Exception $e) {
            return ['error' => $logger->error('При выходе что-то пошло не так: ' . $e->getMessage(), __FILE__, __LINE__, $e)];
        } catch (\Throwable $e) {
            return ['error' => $logger->error('При выходе что-то пошло не так: ' . $e->getMessage(), __FILE__, __LINE__, $e)];
        }
    }
}
