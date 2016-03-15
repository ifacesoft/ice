<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Widget_Security;
use Ice\Exception\DataSource_Insert_DuplicateEntry;
use Ice\Exception\Not_Valid;

class Security_EmailPassword_Register_Submit extends Security
{
    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Widget_Security $securityForm */
        $securityForm = $input['widget'];

        $logger = $securityForm->getLogger();

        try {
            /** @var Model $accountModelClass */
            $accountModelClass = $securityForm->getAccountModelClass();

            if (!$accountModelClass) {
                return $logger
                    ->exception(
                        ['Unknown accountModelClass', [], $securityForm->getResource()],
                        __FILE__,
                        __LINE__
                    );
            }

            $accountData = $securityForm->validate();

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