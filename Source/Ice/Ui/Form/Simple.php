<?php

namespace Ice\Ui\Form;

use Ice\Core\Ui_Form;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\View\Render\Php;

class Simple extends Ui_Form {

    /**
     * Submit form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function submit()
    {
        // TODO: Implement submit() method.
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
            $field['value'] = $values[$fieldName];

            $field['dataUrl'] = $this->getUrl();
            $field['dataJson'] = Json::encode($this->getParams());
            $field['dataAction'] = $this->getAction();
            $field['dataBlock'] = $this->getBlock();

            if ($this->getEvent() == Ui_Form::SUBMIT_EVENT_ONCHANGE && !isset($field['onchange'])) {
                $field['onchange'] = 'Ice_Ui_Form.change($(this)); return false;';
            }

            $result[] = Php::getInstance()->fetch(Ui_Form::getClass($formClass . '_' . $field['template']), $field);
        }

        return Php::getInstance()->fetch(
            Ui_Form::getClass($formClass),
            [
                'fields' => $result,
                'formName' => $formName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );
    }
}