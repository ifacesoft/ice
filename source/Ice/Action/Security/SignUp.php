<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Exception\DataSource_DuplicateEntry;
use Ice\Exception\Not_Good;
use Ice\Exception\Not_Valid;
use Ice\Exception\Not_Verify;
use Ice\Widget\Account_Form;

class Security_SignUp extends Security
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

        /** @var Logger $logger */
        $logger = $accountForm ? $accountForm->getLogger() : $this->getLogger();

        try {
            $this->signUp($accountForm);

            return array_merge(parent::run($input), ['success' => 'Регистрация прошла успешно', 'error' => '']);
        } catch (Not_Good $e) {
            return ['error' => $logger->error('Плохие параметры', __FILE__, __LINE__, $e)];
        } catch (Not_Valid $e) {
            return ['error' => $logger->error(['Регистрационные данные не прошли валидацию: {$0}', $e->getMessage()], __FILE__, __LINE__, $e)];
        } catch (Not_Verify $e) {
            return ['error' => $logger->error(['Запрос на регистрацию отклонен: ', $e->getMessage()], __FILE__, __LINE__, $e)];
        } catch (DataSource_DuplicateEntry $e) {
            return ['error' => $logger->error('Пользователь или учетная запись уже существует', __FILE__, __LINE__, $e)];
        } catch (\Ice\Exception\Security $e) {
            if ($input['securityExceptionThrow']) {
                throw $e;
            }

            return ['error' => $logger->error(['Регистрация не удалась: {$0}', $e->getMessage()], __FILE__, __LINE__, $e)];
        } catch (\Exception | \Throwable $e) {
            return ['error' => $logger->error('При регистрации что-то пошло не так', __FILE__, __LINE__, $e)];
        }
    }
}
