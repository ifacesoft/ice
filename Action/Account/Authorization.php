<?php

namespace ice\action;

use ice\core\Action;
use ice\core\action\Viewable;
use ice\core\Action_Context;
use ice\core\helper\Object;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 14.12.13
 * Time: 16:14
 */
class Account_Authorization extends Action implements Viewable
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
//        if (!User::isGuest()) {
//            Helper_Header::redirect('/');
//        }

        return array('accountType' => $input['accountType']);
    }

    protected function flush(Action_Context &$context)
    {
        $context->setTemplate(Object::getName($this->getClass()) . '_' . $context->getData()['accountType']);
        parent::flush($context);
    }
}