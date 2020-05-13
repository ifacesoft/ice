<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Widget\Account_Form;
use Ice\Core\Security as Core_Security;

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

        /** @var Logger $logger */
        $logger = $accountForm ? $accountForm->getLogger() : $this->getLogger();

        try {
            $userAccount = Core_Security::getInstance()->getAccount();
            //если по каким-то причинам аккаунт уже удален или не активен, выходим просто так
            if (isset($userAccount)) {
                $this->signOut($userAccount);
            } else {
                session_unset();
            }
            return array_merge(parent::run($input), ['success' => 'Выход прошел успешно']);

        } catch (\Exception $e) {
            return ['error' => $logger->error('При выходе что-то пошло не так', __FILE__, __LINE__, $e)];
        } catch (\Throwable $e) {
            return ['error' => $logger->error('При выходе что-то пошло не так', __FILE__, __LINE__, $e)];
        }
    }
}
