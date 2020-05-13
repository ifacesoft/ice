<?php

namespace Ice\Action;

use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_Account_RestorePasswordForbidden;
use Ice\Model\Account_Login_Password;
use Ice\Core\Logger;
use Ice\Exception\DataSource;
use Ice\Widget\Account_Form;

class Security_Password_Login_RestorePassword_Submit extends Security
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

            /** @var Account_Login_Password $account */
            $account = $accountModelClass::getAccountByLogin($accountForm->get('login'));

            if (!$account) {
                $logger->info('Пользователь с таким логином не найден.', Logger::DANGER, true);
                throw new Security_Account_NotFound('Пользователь с таким логином не найден.');
//                return [
//                    'error' => $logger->info('Пользователь с таким логином не найден.', Logger::DANGER, true)
//                ];
            }

            $this->restorePassword($account, $input);

            return array_merge(
                ['success' => $logger->info('Ссылка для восстановления пароля отправлена вам на электронный адрес', Logger::SUCCESS, true)],
                parent::run($input)
            );
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