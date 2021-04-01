<?php

namespace Ice\Action;

use Ice\Model\Account_Login_Password;
use Ice\Core\QueryBuilder;
use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_Account_RestorePasswordForbidden;
use Ice\Exception\Security_Account_Verify;
use Ice\Model\Account_Email_Password;
use Ice\Core\Logger;
use Ice\Exception\DataSource;
use Ice\Model\User;
use Ice\Widget\Account_Form;

class Security_Password_Email_RestorePassword_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            /** @var Account_Email_Password $accountModelClass */
            $accountModelClass = $accountForm->getAccountModelClass();

            if (!$accountModelClass) {
                return $logger
                    ->exception(
                        ['Unknown accountModelClass', [], $accountForm->getResource()],
                        __FILE__,
                        __LINE__
                    );
            }

            /** @var Account_Email_Password $account */
            $account = $accountModelClass::getSelectQuery(
                ['/pk', 'token__fk', 'email_confirmed'],
                ['email' => $accountForm->get('email')]
            )->getModel();

            if (isset($account) && !$account->get('email_confirmed')) {
                $logger->info('Электронная почта аккаунта не подтверждена', Logger::DANGER, true);
                throw new Security_Account_Verify('Электронная почта аккаунта не подтверждена');
            }

            if (!$account) {
                $account = Account_Login_Password::createQueryBuilder()
                    ->inner(User::class)
                    ->eq(['login' => mb_strtolower($accountForm->get('email'))])
                    ->eq(['email_canonical' => mb_strtolower($accountForm->get('email'))], User::class, QueryBuilder::SQL_LOGICAL_OR)
                    ->group('/pk', User::class)
                    ->getSelectQuery(['/pk', 'token__fk'])
                    ->getModel();
            }

            if (!$account) {
                $logger->info('Пользователь с таким электронным адресом не найден.', Logger::DANGER, true);
                throw new Security_Account_NotFound('Пользователь с таким email не найден.');
//                return [
//                    'error' => $logger->info('Пользователь с таким электронным адресом не найден.', Logger::DANGER, true)
//                ];
            }

            $this->restorePassword($account, $input);

            return array_merge(
                ['success' => $logger->info('Ссылка для восстановления пароля отправлена вам на электронный адрес', Logger::SUCCESS, true)],
                parent::run($input)
            );
        } catch (Security_Account_Verify $e) {
            throw $e;
        } catch (Security_Account_NotFound $e) {
            throw $e;
        } catch (Security_Account_RestorePasswordForbidden $e) {
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