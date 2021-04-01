<?php

namespace Ice\Action;

use Ice\Model\Account_Email_Password;
use Ice\Model\Account_Login_Password;
use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_Account_Verify;
use Ice\Exception\Security_ChangeEmail_Duplicate;
use Ice\Core\Logger;
use Ice\Exception\DataSource;
use Ice\Model\User;
use Ice\Widget\Account_Form;

class Security_Password_Login_ChangeEmail_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            /** @var Account_Login_Password $accountModelClass */
            $accountModelClass = $accountForm->getAccountModelClass();

            if (!$accountModelClass) {
                return $logger
                    ->exception(
                        ['Unknown accountModelClass', [], $accountForm->getResource()],
                        __FILE__,
                        __LINE__
                    );
            }

            $user = \Ice\Core\Security::getInstance()->getUser();

            $userDuplicate = User::createQueryBuilder()
                ->eq(['email_canonical' => mb_strtolower($accountForm->get('email'))])
                ->getSelectQuery(['/pk'])
                ->getModel();

            $accountDuplicateEmail = Account_Email_Password::createQueryBuilder()
                ->eq(['email' => mb_strtolower($accountForm->get('email'))])
                ->getSelectQuery(['/pk'])
                ->getModel();

            $accountDuplicateLogin = Account_Login_Password::createQueryBuilder()
                ->like('login', mb_strtolower($accountForm->get('email')))
                ->getSelectQuery(['/pk'])
                ->getModel();

            if ($userDuplicate || $accountDuplicateLogin || $accountDuplicateEmail) {
                $logger->info('Найден пользователь с таким же электронным адресом.', Logger::DANGER, true);
                throw new Security_ChangeEmail_Duplicate('Пользователь с таким email не найден.');
            }

            /** @var Account_Email_Password $account */
            $account = $accountModelClass::getSelectQuery(
                ['/pk', 'token__fk', 'email_confirmed', 'email'],
                ['user__fk' => $user->getPkValue()]
            )->getModel();

            if (isset($account) && !$account->get('email_confirmed')) {
                $logger->info('Электронная почта аккаунта не подтверждена', Logger::DANGER, true);
                throw new Security_Account_Verify('Электронная почта аккаунта не подтверждена');
            }

            $this->createChangeMailToken($account, $input);

            return array_merge(
                ['success' => $logger->info('Ссылка для восстановления пароля отправлена вам на электронный адрес', Logger::SUCCESS, true)],
                parent::run($input)
            );
        } catch (Security_Account_NotFound $e) {
            throw $e;
        } catch (DataSource $e) {
            $logger->warning('Сервис недоступен. Попробуйте позже', __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info('Сервис недоступен. Попробуйте позже', Logger::WARNING, true)
            ];
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info('Восстановление пароля не удалось', Logger::DANGER, true)
            ];
        }
    }
}