<?php

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Exception\DataSource_Insert_DuplicateEntry;
use Ice\Exception\Not_Good;
use Ice\Exception\Not_Valid;
use Ice\Exception\Not_Verify;
use Ice\Exception\Security_Account_EmailNotConfirmed;
use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_Account_Verify;
use Ice\Exception\Security_User_NotActive;
use Ice\Widget\Account_Form;
use Ice\Core\Security as Core_Security;

class Security_SignIn extends Security
{
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

        /** @var Logger $logger */
        $logger = $accountForm ? $accountForm->getLogger() : $this->getLogger();

        try {
            $account = $this->signIn($accountForm);

            return array_merge(parent::run($input), ['account_class' => get_class($account), 'account_key' => $account->getPkValue(), 'success' => 'Авторизация прошла успешно']);
        } catch (Not_Good $e) {
            return ['error' => $logger->error('Плохие параметры', __FILE__, __LINE__, $e)];
        } catch (Not_Valid $e) {
            return ['error' => $logger->error('Авторизационные данные не прошли валидацию: ' . $e->getMessage(), __FILE__, __LINE__, $e)];
        } catch (Not_Verify $e) {
            return ['error' => $logger->error('Запрос на авторизацию отклонен: ' . $e->getMessage(), __FILE__, __LINE__, $e)];
        } catch (Security_Account_Verify $e) {
            return ['error' => 'Запрос на авторизацию отклонен: неверное имя пользователя или пароль', 'exception' => $e];
        } catch (Security_User_NotActive $e) {
            return ['error' => 'Запрос на авторизацию отклонен:' . $e->getMessage(), 'exception' => $e];
        } catch (Security_Account_EmailNotConfirmed $e) {
            return ['error' => 'Запрос на авторизацию отклонен:' . $e->getMessage(), 'exception' => $e];
        } catch (Security_Account_NotFound $e) {
            return ['error' => 'Учетная запись не найдена'];
        } catch (\Exception $e) {
            return ['error' => $logger->error('При авторизации что-то пошло не так', __FILE__, __LINE__, $e)];
        }
    }
}