<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Security_Account;
use Ice\Widget\Form_Security_EmailPassword_Login as Widget_Form_Security_EmailPassword_Login;

class Form_Security_EmailPassword_Login extends Render
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => 'Ice:Php', 'layout' => ''],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'widget' => ['default' => null, 'providers' => 'request'],
                'widgets' => ['default' => [], 'providers' => ['default', 'request']],
                'form' => ['validators' => 'Ice:Not_Empty'],
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Widget_Form_Security_EmailPassword_Login $form */
        $form = $input['form'];

        $accountModelClass = $form->getAccountModelClass();

        if (!$accountModelClass) {
            return $form->getLogger()
                ->exception(
                    ['Unknown accountModelClass', [], $form->getResource()],
                    __FILE__,
                    __LINE__
                );
        }

        $values = $form->validate();

        /** @var Security_Account|Model $account */
        $account = $accountModelClass::createQueryBuilder()
            ->eq(['email' => $values['email']])
            ->limit(1)
            ->getSelectQuery(['password', '/expired', 'user__fk'])
            ->getModel();

        if (!$account && !$form->verify($account, $values)) {
            $form->getLogger()->exception(['Log in failure', [], $form->getResource()], __FILE__, __LINE__);
        }

        $form->signIn($account);

        return [
            'success' => $form->getLogger()->info('Login successfully', Logger::SUCCESS),
            'widgets' => parent::run($input)['widgets']
        ];
    }
}