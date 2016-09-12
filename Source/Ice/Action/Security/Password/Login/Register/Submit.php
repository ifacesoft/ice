<?php

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Exception\DataSource_Insert_DuplicateEntry;
use Ice\Exception\Not_Valid;
use Ice\Widget\Account_Form;

class Security_Password_Login_Register_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            /** @var Model $accountModelClass */
            $accountModelClass = $accountForm->getAccountModelClass();

            if (!$accountModelClass) {
                return $logger
                    ->exception(
                        ['Unknown accountModelClass', [], $accountForm->getResource()],
                        __FILE__,
                        __LINE__
                    );
            }

            $accountData = $accountForm->validate();

            $accountData['password'] = password_hash($accountData['password'], PASSWORD_DEFAULT);

            $accountData['modelClass'] = $accountModelClass;

            $this->signUp($accountData, $input);

            return array_merge(
                ['success' => $logger->info('Регистрация прошла успешно', Logger::SUCCESS, true)],
                parent::run($input)
            );
        } catch (Not_Valid $e) {
            return [
                'error' => $logger->info($e->getMessage(), Logger::DANGER, true)
            ];
        } catch (DataSource_Insert_DuplicateEntry $e) {
            return [
                'error' => $logger->info('Пользователь уже существует', Logger::DANGER, true)
            ];
        } catch (\Exception $e) {
            $logger->error('Регистрация не удалась', __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info('Регистрация не удалась', Logger::DANGER, true)
            ];
        }
    }
}