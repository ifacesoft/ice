<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Exception\Error;
use Ice\Exception\Security_Token_Expired;
use Ice\Exception\Security_Token_NotFound;
use Ice\Exception\Security_Token_Used;
use Ice\Helper\Date;
use Ice\Model\Token;
use Ice\Widget\Account_Form;

class Security_Password_Email_RegisterConfirm_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            /** @var Token $token */
            $token = Token::createQueryBuilder()
                ->eq(['/' => $accountForm->getPart('token')->get('token')])
                ->getSelectQuery('*')
                ->getModel();

            if (!$token) {
                $logger->info('Token not found', Logger::DANGER, true);
                throw new Security_Token_NotFound('Токен не найден.');
//                return [
//                    'error' => $logger->info('Token not found', Logger::DANGER, true)
//                ];
            }

            if ($token->get('/used_at', false) !== false) {
                $logger->info('Token already used', Logger::DANGER, true);
                throw new Security_Token_Used('Токен уже использовался.');
//                return [
//                    'error' => $logger->info('Token already used', Logger::DANGER, true)
//                ];
            }

            if (Date::expired(strtotime($token->get('/expired')))) {
                $logger->info('Token already expired', Logger::DANGER, true);
                throw new Security_Token_Expired('Токен истек.');
//                return [
//                    'error' => $logger->info('Token already expired', Logger::DANGER, true)
//                ];
            }

            $account = $this->registerConfirm($token, $input);

            return array_merge(
                [
                    'accountKey' => $account->getPkValue(),
                    'accountClass' => get_class($account),
                    'success' => $logger->info('Электронная почта успешно подтверждена', Logger::SUCCESS, true)
                ],
                parent::run($input)
            );


        } catch (\Ice\Exception\Security $e) {
            throw $e;
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info($e->getMessage(), Logger::DANGER, true)
            ];
        }
    }
}