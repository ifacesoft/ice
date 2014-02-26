<?php
namespace ice\action;

use ice\core\Action;
use ice\core\action\Ajaxable;
use ice\core\Action_Context;
use ice\core\Validator_Exception;
use ice\model\ice\Account_Type;
use ice\model\ice\Account_Type_Exception;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 14.12.13
 * Time: 16:20
 */
class Account_Login extends Action implements Ajaxable
{
    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        $errorMessage = '';

        try {
            /** @var Account_Type $accountType */
            $accountType = Account_Type::getDelegate($input['accountType']);

            if (!$accountType) {
                throw new Account_Exception('Учетная запись заданного типа "' . $input['accountType'] . '" не может быть получена');
            }

            $accountType->check($input, 'Login');

            $accountType->login($input);

        } catch (Validator_Exception $e) {
            $errorMessage = $e->getMessage();
        } catch (Account_Exception $e) {
            $errorMessage = $e->getMessage();
        } catch (Account_Type_Exception $e) {
            $errorMessage = $e->getMessage();
        }

        if (!empty($errorMessage)) {
            return array(
                'error' => array(
                    'message' => $errorMessage
                ),
                'hasError' => 1
            );
        }

        return $input;
    }
}