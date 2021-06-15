<?php

namespace Ice\Widget\Form\Security\Login;

use Ice\Core\Query;
use Ice\Core\Widget_Form_Security_Login;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Model\Account;
use Ice\Model\User_Role_Link;
use Ice\Render\Php;

class LoginPassword extends Widget_Form_Security_Login
{
    protected function __construct()
    {
        parent::__construct();

        $resource = LoginPassword::getResource();

        $this->text(
            'login',
            $resource->get('login'),
            [
                'placeholder' => $resource->get('login_placeholder'),
                'validators' => ['Ice:Length_Min' => 2, 'Ice:LettersNumbers']
            ]
        )->password(
            'password',
            $resource->get('password'),
            [
                'placeholder' => $resource->get('password_placeholder'),
                'validators' => ['Ice:Length_Min' => 5]
            ]
        );
    }

    /**
     * Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public function submit()
    {
        foreach (Query::getBuilder(Account::getClass())->eq(['login' => $this->getValues()['login']])->getSelectQuery(['password', 'user__fk'])->getRows() as $accountRow) {
            if (password_verify($this->validate()['password'], $accountRow['password'])) {
                $_SESSION['userPk'] = $accountRow['user__fk'];
                $_SESSION['roleNames'] = Query::getBuilder(User_Role_Link::getClass())
                    ->inner('Ice:Role', 'role_name')
                    ->eq(['user__fk', $accountRow['user__fk']])
                    ->getSelectQuery('role_name')->getColumn();
                return;
            }
        }

        Widget_Form_Security_Login::getLogger()->exception('Authorization failed: login-password incorrect', __FILE__, __LINE__);
    }

    public function render()
    {
        $formClass = get_class($this);

        $formName = 'Form_' . Object::getName($formClass);

        $filterFields = $this->getFilterFields();

        $fields = $this->getFields();
        $values = $this->getValues();

        $result = [];

        $targetFields = [];
        foreach ($filterFields as $key => &$value) {
            if (is_string($key)) {
                if (is_array($value)) {
                    list($fields[$key]['type'], $fields[$key]['template']) = $value;
                } else {
                    $fields[$key]['type'] = $value;
                    $fields[$key]['template'] = 'Ice:' . $value;
                }

                $value = $key;
            }

            $targetFields[$value] = $fields[$value];
            unset($fields[$value]);
        }

        if (empty($targetFields)) {
            $targetFields = $fields;
        }

        unset($fields);

        foreach ($targetFields as $fieldName => $field) {
            $field['fieldName'] = $fieldName;
            $field['formName'] = $formName;
            $field['value'] = isset($values[$fieldName]) ? $values[$fieldName] : '';

            $field['dataUrl'] = $this->getUrl();
            $field['dataJson'] = Json::encode($this->getParams());
            $field['dataAction'] = $this->getAction();
            $field['dataBlock'] = $this->getBlock();

            $result[] = Php::getInstance()->fetch(Widget_Form_Security_Login::getClass($formClass . '_' . $field['template']), $field);
        }

        return Php::getInstance()->fetch(
            Widget_Form_Security_Login::getClass($formClass),
            [
                'fields' => $result,
                'formName' => $formName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );
    }
}
