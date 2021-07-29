<?php

namespace Ice\Action;

use Ice\Exception\Not_Good;
use Ice\Exception\Not_Valid;
use Ice\Exception\Not_Verify;
use Ice\Exception\Security_Account_EmailNotConfirmed;
use Ice\Exception\Security_Account_Expired;
use Ice\Exception\Security_Account_NotActive;
use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_Account_Verify;
use Ice\Exception\Security_User_NotActive;
use Ice\Widget\Account_Form;

class Security_SignIn extends Security
{
    protected static function config()
    {
        $config = parent::config();

        $config['input']['securityExceptionThrow'] = ['providers' => 'default', 'default' => 0];

        return $config;
    }

    /** Run action
     *
     * @param array $input
     * @return array
     * @throws \Ice\Core\Exception
     */
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm ? $accountForm->getLogger() : $this->getLogger();

        try {
            $account = $this->signIn($accountForm);

            return array_merge(
                parent::run($input),
                [
                    'account_class' => get_class($account),
                    'account_key' => $account->getPkValue(),
                    'user_key' => $account->get('user__fk'),
                    'success' => 'Авторизация прошла успешно',
                    'error' => ''
                ]
            );
        } catch (Not_Good $e) {
            return ['error' => $logger->error('Плохие параметры', __FILE__, __LINE__, $e)];
        } catch (Not_Valid $e) {
            return ['error' => $logger->error('Авторизационные данные не прошли валидацию: ' . $e->getMessage(), __FILE__, __LINE__, $e)];
        } catch (Not_Verify $e) {
            return ['error' => $logger->error('Запрос на авторизацию отклонен: ' . $e->getMessage(), __FILE__, __LINE__, $e)];
        } catch (\Ice\Exception\Security $e) {
            if ($input['securityExceptionThrow']) {
                throw $e;
            }

            return ['error' => $logger->error(['Авторизация не удалась: {$0}', $e->getMessage()], __FILE__, __LINE__, $e)];
        } catch (\Exception $e) {
            return ['error' => $logger->error('При авторизации что-то пошло не так', __FILE__, __LINE__, $e)];
        } catch (\Throwable $e) {
            return ['error' => $logger->error('При авторизации что-то пошло не так', __FILE__, __LINE__, $e)];
        }
    }
}
