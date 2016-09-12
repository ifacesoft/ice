<?php namespace Ice\Model;

use Ice\Core\Config;
use Ice\Core\DataSource;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Model\Security_User;
use Ice\Core\Request;
use Ice\Core\Router;
use Ice\Core\Security;
use Ice\Exception\Security_Account_Login;
use Ice\Exception\Security_Account_Verify;
use Ice\Helper\Date;
use Ice\Helper\String;
use Ice\Message\Mail;
use Ice\Model\Account;
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
 * @property mixed account_email_password_expired
 * @property mixed account_email_password_key_expired
 *
 * @see \Ice\Core\Model
 *
 * @package Ebs\Model
 */
abstract class Account_Password extends Account
{
    /**
     * Check is expired account
     *
     * @return bool
     */
    public function isExpired()
    {
        return strtotime($this->get('/expired')) < time();
    }

    public function getUser()
    {
        /** @var Model $userModelClass */
        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');
        return $this->fetchOne($userModelClass, '*', true);
    }

    public function securityVerify(array $values)
    {
        if (!password_verify($values['password'], $this->get('password'))) {
            throw new Security_Account_Verify('Account is not valid. Please, check input.');
        }

        return $values;
    }

    public function securityHash(array $values, $paramName)
    {
        return password_hash($values[$paramName], PASSWORD_DEFAULT);
    }

    public function signIn(Account_Form $accountForm)
    {
        $logger =  $accountForm->getLogger();

        $this->securityVerify($accountForm->validate());

        if ($expired = $this->isExpired()) {
            if ($prolongate = $accountForm->getProlongate()) {
                if ($prolongate === true) {
                    $expired = $this->prolongate($accountForm->getExpired());
                } else {
                    $expired = call_user_func((string)$prolongate, $this, $accountForm->getExpired());
                }
            }

            if ($expired) {
                $logger->exception('Account is expired', __FILE__, __LINE__);
            }
        }

        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

        /** @var Security_User|Model $user */
        $user = $this->fetchOne($userModelClass, '/active', true);

        if (!$user || !$user->isActive()) {
            $logger->exception('User is not active or not found', __FILE__, __LINE__);
        }

        return Security::getInstance()->login($this);
    }

    abstract protected function getAccountData(Account_Form $accountForm);

    abstract protected function getUserData(Account_Form $accountForm);

    /**
     * @param Account_Form $accountForm
     * @return \Ice\Model\Account
     * @throws \Exception
     */
    public function signUp(Account_Form $accountForm)
    {
        /** @var Account $accountModelClass */
        $accountModelClass = __CLASS__;

        /** @var DataSource $dataSource */
        $dataSource = $accountModelClass::getDataSource();

        try {
            $dataSource->beginTransaction();

            $accountForm->validate();

            $this->set($this->getAccountData($accountForm))->save();

            $userData = $this->getUserData($accountForm);

            $token = null;

            if ($accountForm->isConfirm()) {
                $token = Token::create([
                    '/' => md5(String::getRandomString()),
                    '/expired' => $accountForm->getConfirmationExpired(),
                    'modelClass' => $accountModelClass,
                    '/data' => ['account_expired' => $accountForm->getExpired()]
                ])->save();

                $this->set(['token' => $token]);

                if ($accountForm->isConfirmRequired()) {
                    $this->set(['/expired' => Date::ZERO]);
                    $userData['/active'] = 0;
                } else {
                    $this->set(['/expired' => $accountForm->getConfirmationExpired()]);
                    $userData['/active'] = 1;
                }
            } else {
                $this->set(['/expired' => $accountForm->getExpired()]);
                $userData['/active'] = 1;
            }

            /** @var Security_User|Model $userModelClass */
            $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');

            $this->set(['user' => $userModelClass::create($userData)->save()])->save();

            if ($token) {
                $this->sendRegisterConfirm($accountForm, $token);
            }

            $dataSource->commitTransaction();
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction();

            throw $e;
        }

        return (!$accountForm->isConfirm() || ($accountForm->isConfirm() && !$accountForm->isConfirmRequired())) && $accountForm->isAutologin()
            ? $this->signIn($accountForm)
            : $this;
    }

    public function sendRegisterConfirm(Account_Form $accountForm, Token $token)
    {
        $url = 'http://' . Request::host() . Router::getInstance()->getUrl('ebs_security_register_confirm');
        $urlFull = $url . '?token=' . $token->get('/');

        Mail::create()
            ->setRecipients([$accountForm->get('email') => $accountForm->get('fio')])
            ->setSubject('Подтверждение регистрации в ЭБС')
            ->setBody('<p>Для подтверждения учетной записи перейдите по ссылке <a href="' . $urlFull . '">' . $urlFull . '</a></p>' .
                '<p>Вы можете внести ключ подтверждения ' . $token->get('/') . ' самостоятельно на странице <a href="' . $url . '">' . $url . '</a></p>')
            ->send();
    }

    public function prolongate($expired)
    {
        // TODO: Implement prolongate() method.
    }
}