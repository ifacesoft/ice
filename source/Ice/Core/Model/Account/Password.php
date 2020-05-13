<?php

namespace Ice\Core;

use Ice\Exception\Security_Account_Verify;
use Ice\Helper\Date;
use Ice\Helper\Type_String;
use Ice\Message\Mail;
use Ice\Model\Token;
use Ice\Widget\Account_Form;

/**
 * Class Account_Email_Password
 *
 * @property mixed account_email_password_pk
 * @property mixed email
 * @property mixed password
 * @property mixed user__fk
 * @property mixed account_email_password_key
 * @property mixed account_email_password_key_expired
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
abstract class Model_Account_Password extends Model_Account
{
    /**
     * @param array $values
     * @param $paramName
     * @return string
     */
    public function getSecurityHash(array $values, $paramName)
    {
        return password_hash($values[$paramName], PASSWORD_DEFAULT);
    }

    /**
     * @param Account_Form $accountForm
     * @param Token $token
     * @throws Exception
     */
    protected function sendRegisterConfirm(Account_Form $accountForm, Token $token)
    {
        // todo: закомменченный код верный
//            $url = 'http://' . Request::host() . Router::getInstance()->getUrl('ice_security_register_confirm');
        $url = Request::protocol() . Request::host() . '/auth/mail/confirm';
        $urlFull = $url . '?token=' . $token->get('/');

        Mail::create()
            ->setRecipients([$accountForm->get('email') => $accountForm->get('fio')])
            ->setSubject('Подтверждение регистрации в ЭБС Лань')
            ->setBody('<p>Для подтверждения учетной записи перейдите по ссылке <a href="' . $urlFull . '">' . $urlFull . '</a></p>')
            ->send();
    }

    public function loginVerify(array $values)
    {
        if (Environment::getInstance()->isDevelopment()) {
            return $values;
        }

        if (!password_verify($values['password'], $this->get('password'))) {
            throw new Security_Account_Verify('Account is not valid. Please, check input.');
        }

        return $values;
    }

    /**
     * @return Model_Account_Password
     * @throws Exception
     * @internal param Model_Account $account
     */
    public function sendProlongateConfirm()
    {
        $token = Token::create([
            '/' => md5(Type_String::getRandomString()),
            '/expired' => Date::get('+3 day'),
            'modelClass' => get_class($this),
            '/data' => ['account_expired' => Date::get(' +1 year'), 'function' => __FUNCTION__]
        ])->save();

        // todo: закомменченный код верный
//        $url = 'http://' . Request::host() . Router::getInstance()->getUrl('ice_security_register_confirm');
        $url = Request::protocol() . Request::host() . '/auth/mail/confirm';
        $urlFull = $url . '?token=' . $token->get('/');

        Mail::create()
            ->setRecipients([$this->get('email') => Security::getInstance()->getUser()->get('user_name')])
            ->setSubject('Подтверждение электронного адреса в ЭБС Лань')
            ->setBody('<p>Для подтверждения учетной записи перейдите по ссылке <a href="' . $urlFull . '">' . $urlFull . '</a></p>')
            ->send();

        $this->set('token__fk', $token->getPkValue());
        $this->save();

        return $this;
    }
}