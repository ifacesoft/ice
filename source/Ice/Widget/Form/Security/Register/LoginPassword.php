<?php

namespace Ice\Widget\Form\Security\Register;


use Ice\Core\Widget_Form_Security_Register;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Render\Php;

class LoginPassword extends Widget_Form_Security_Register
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
        )->password(
            'password1',
            $resource->get('password1'),
            ['placeholder' => $resource->get('password1_placeholder')]
        );
    }

    public function bind($key, $value = null)
    {
        if ($key == 'password1') {
            [
                $this->_validateScheme['password1']['Ice:Equal'] = $this->_values['password']
            ];
        }

        return parent::bind($key, $value);
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

            $result[] = Php::getInstance()->fetch(Widget_Form_Security_Register::getClass($formClass . '_' . $field['template']), $field);
        }

        return Php::getInstance()->fetch(
            Widget_Form_Security_Register::getClass($formClass),
            [
                'fields' => $result,
                'formName' => $formName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );
    }
}
