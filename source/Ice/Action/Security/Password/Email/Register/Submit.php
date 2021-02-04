<?php

namespace Ice\Action;

use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Exception\DataSource;
use Ice\Exception\Not_Valid;
use Ice\Exception\Security_Account_Register;
use Ice\Widget\Account_Form;

/**
 * Class Security_Password_Email_Register_Submit
 * @package Ice\Action
 * @deprecated use Security_SignUp
 */
class Security_Password_Email_Register_Submit extends Security
{

    protected static function config()
    {
        $config = parent::config();

        $config['input']['container'] = ['default' => [], 'providers' => ['default']];

        return $config;
    }

    /** Run action
     *
     * @param array $input
     * @return array
     * @throws Exception
     */
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            $this->signUp($input['widget'], $input['container']);

            return array_merge(
                ['success' => $logger->info('Регистрация прошла успешно', Logger::SUCCESS, true)],
                parent::run($input)
            );
        } catch (Not_Valid $e) {
            return [
                'error' => $logger->info($e->getMessage(), Logger::DANGER, true)
            ];
        } catch (Security_Account_Register $e) {
            return [
                'error' => $logger->info($e->getMessage(), Logger::DANGER, true)
            ];
        } catch (DataSource $e) {
            $logger->warning($e->getMessage(), __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info('Сервис не доступен. Администратор уведомлен о проблеме. В ближайшее время работа будет восстановлена.', Logger::WARNING, true)
            ];
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info('Регистрация не удалась.', Logger::DANGER, true)
            ];
        }
    }
}