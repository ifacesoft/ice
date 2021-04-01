<?php

namespace Ice\Action;

use Ice\Model\Account_Email_Password;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model_Account;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_Account_Verify;
use Ice\Exception\Security_Token_NotFound;
use Ice\Helper\Date;
use Ice\Model\Token;
use Ice\Widget\Account_Form;

class Security_Password_Email_RestorePasswordConfirm_Submit extends Security
{
    /**
     * @param array $input
     * @return array
     * @throws Security_Account_NotFound
     * @throws Security_Account_Verify
     * @throws Security_Token_NotFound
     * @throws Exception
     * @throws Config_Error
     * @throws Error
     * @throws FileNotFound
     */
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            $values = $accountForm->validate();

            /** @var Token $token */
            $token = Token::createQueryBuilder()
                ->eq(['/' => $accountForm->getPart('token')->get('token')])
                ->gt('/expired', Date::get())
                ->isNull('/used_at')
                ->getSelectQuery('*')
                ->getModel();

            // todo: получать токен без учета его истечения. срок истечения проверять отдельно + уже использован (used_at)
            if (!$token) {
                $logger->info('Ключ подтверждения не найден или истек срок его действия', Logger::DANGER, true);
                throw new Security_Token_NotFound('Token for restore this account not found');
//                return [
//                    'error' => $logger->info('Ключ подтверждения не найден или истек срок его действия', Logger::DANGER, true)
//                ];
            }

            //TODO токен знает какой класс аккаунта, поэтому берем из токена
            //$accountModelClass = $accountForm->getAccountModelClass();
            $accountModelClass = $token->get('modelClass');
            $accountModelClassName = $accountModelClass::getClassName();


            //TODO  чужой класс вызывается, восстанавливает пароль по логину
            /** @var Model_Account $account */
            $accountQuery = $accountModelClass::createQueryBuilder()
                ->inner(Token::class, '/pk', 'Token.id=' . $accountModelClassName . '.token_id AND Token.token="' . $token->get('token') . '"');

            $fields = ['/pk', 'password', 'user__fk'];
            if ($accountModelClass == Account_Email_Password::class) {
                $fields[] = 'email_confirmed';
            }

            $account = $accountQuery->getSelectQuery($fields)
                ->getModel();

            if (isset($account) && $account instanceof Account_Email_Password && !$account->get('email_confirmed')) {
                $logger->info('Account email is not confirmed', Logger::DANGER, true);
                throw new Security_Account_Verify('Account email is not confirmed');
            }

            if (!$account) {
                $tokenAccountAttachedPk = isset($token->get('token_data')['account']) ? reset($token->get('token_data')['account']) : 0;
                $existUserAccount = $accountModelClass::createQueryBuilder()
                    ->pk($tokenAccountAttachedPk)
                    ->getSelectQuery(['/pk'])
                    ->getModel();

                if ($existUserAccount) {
                    throw new Security_Token_NotFound('Token for restore this account not found');
                }

                $logger->info('Account email with token not found', Logger::DANGER, true);
                throw new Security_Account_NotFound('Account email with token not found');
//                return [
//                    'error' => $logger->info('Ключ подтверждения не найден или истек срок его действия', Logger::DANGER, true)
//                ];
            }

            $accountData = ['password' => $account->getSecurityHash($values, 'new_password')];

            $this->changePassword($account, $accountData, $input);

            $tokenData = $token->get('/data');

            $tokenData['used_class'] = get_class($account);
            $tokenData['used_id'] = $account->getPkValue();

            $account->set(['token__fk' => null])->save();

            $token->set([
                '/used_at' => Date::get(),
                '/data' => $tokenData
            ])->save();

            return array_merge(
                ['success' => $logger->info('Восстановление пароля прошло успешно', Logger::SUCCESS, true)],
                parent::run($input)
            );

        } catch (Security_Account_Verify $e) {
            throw $e;
        } catch (Security_Account_NotFound $e) {
            throw $e;
        } catch (Security_Token_NotFound $e) {
            throw $e;
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info($e->getMessage(), Logger::DANGER, true)
            ];
        }
    }
}