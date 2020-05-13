<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model_Account;
use Ice\Exception\Security_Token_NotFound;
use Ice\Helper\Date;
use Ice\Model\Token;
use Ice\Widget\Account_Form;

class Security_Password_Login_RestorePasswordConfirm_Submit extends Security
{
    /**
     * @param array $input
     * @return array
     * @throws \Ice\Core\Exception
     * @throws \Ice\Exception\Config_Error
     * @throws \Ice\Exception\Error
     * @throws \Ice\Exception\FileNotFound
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
                ->getSelectQuery('*')
                ->getModel();

            // todo: получать токен без учета его истечения. срок истечения проверять отдельно + уже использован (used_at)

            if (!$token) {
                $logger->info('Ключ подтверждения не найден или истек срок его действия', Logger::DANGER, true);
                throw new Security_Token_NotFound('Токен не найден');
            }

            //TODO а не стоит ли здесь тоже брать из токена модел класс?
            $accountModelClass = $accountForm->getAccountModelClass();
            $accountModelClassName = $accountModelClass::getClassName();

            /** @var Model_Account $account */
            $account = $accountModelClass::createQueryBuilder()
                ->inner(Token::class, '/pk', 'Token.id=' . $accountModelClassName . '.token_id AND Token.token="' . $token->get('token') . '"')
                ->getSelectQuery(['/pk', 'password', 'user__fk'])
                ->getModel();

            if (!$account) {
                $tokenAccountAttachedPk = isset($token->get('token_data')['account']) ? reset($token->get('token_data')['account']) : 0;
                $existUserAccount = $accountModelClass::createQueryBuilder()
                    ->pk($tokenAccountAttachedPk)
                    ->getSelectQuery(['/pk'])
                    ->getModel();
                
                if ($existUserAccount) {
                    throw new Security_Token_NotFound('Token for restore this account not found');
                }

                $accountForm->getLogger()->exception('Account login with token not fount', __FILE__, __LINE__);
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