<?php

namespace Ice\Core;

use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Security_Account_AttachForbidden;
use Ice\Exception\Security_Account_NotFound;
use Ice\Exception\Security_User_NotActive;
use Ice\Exception\Security_User_NotFound;
use Ice\Helper\Date;
use Ice\Model\Token;
use Ice\Model\User;
use Ice\Widget\Account_Form;

abstract class Model_Account extends Model
{
    /**
     * @param Account_Form $accountForm
     * @param array $container
     * @return Model_Account
     * @throws Exception
     */
    final public function signUp(Account_Form $accountForm, array $container = [])
    {
        /** @var Model_Account $accountModelClass */
        $accountModelClass = get_class($this);

        $dataSource = $accountModelClass::getDataSource();

        try {
            $dataSource->beginTransaction();

            $this->signUpAccount($accountForm, $this->signUpUser($accountForm, $container), $container);

            if ($this->isConfirmed($accountForm) === false && $accountForm->isConfirm()) { // именно в такой послеовательности
                $this->sendConfirmToken($accountForm, $this->getConfirmToken($accountForm));
            }

            $dataSource->commitTransaction();
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction($e);

            throw $e;
        }

        return $this;
    }

    /**
     * @param Account_Form $accountForm
     * @param User $user
     * @param array $container
     * @return Model|Model_Account
     * @throws Exception
     */
    private function signUpAccount(Account_Form $accountForm, User $user, array $container = [])
    {
        return $this->set(
            array_merge(
                $this->getCommonAccountData($accountForm, $container),
                ['user' => $user]
            )
        )->save();
    }

    /**
     * @param Account_Form $accountForm
     * @param array $container
     * @return array
     */
    private function getCommonAccountData(Account_Form $accountForm, array $container = [])
    {
        $accountData = (array)$this->getAccountData($accountForm, $container);

        return $accountData;
    }

    /**
     * @param Account_Form $accountForm
     * @return array
     */
    abstract protected function getAccountData(Account_Form $accountForm);

    /**
     * @param Account_Form $accountForm
     * @param array $container
     * @return Model|User
     * @throws Error
     * @throws Exception
     * @throws Security_Account_AttachForbidden
     */
    private function signUpUser(Account_Form $accountForm, array $container = [])
    {
        $userData = $this->getCommonUserData($accountForm, $container);

        $security = Security::getInstance();

        $user = $security->isAuth()
            ? $security->getUser()
            : User::getUniqueModel($userData, '*');

        if ($user) {
            if (!$accountForm->isAttachAccount()) {
                throw new Security_Account_AttachForbidden('Attach account forbidden', ['user' => $user]);
            }

            if ($accountForm->isUpdateUserDataOnAttachAccount()) {
                return $user->set($userData)->save();
            }

            if ($accountForm->isUpdateUserEmailOnAttachAccount()) {
                if (empty($userData['/email'])) {
                    throw new Error('Empty email for attach');
                }

                return $user->set(['/email' => $userData['/email'], '/login' => $userData['/email']])->save();
            }

            return $user;
        }

        return User::create($userData)->save();
    }

    /**
     * @param Account_Form $accountForm
     * @param array $container
     * @return array
     * @throws \Exception
     */
    private function getCommonUserData(Account_Form $accountForm, array $container = [])
    {
        $userData = (array)$this->getUserData($accountForm, $container);

        if ($accountForm->isConfirm()) {
            if ($accountForm->isConfirmRequired()) {
                $userData['/active'] = 0;
                $userData['/expired_at'] = Date::get();
            } else {
                $userData['/active'] = 1;
                $userData['/expired_at'] = $accountForm->getConfirmationExpired();
            }
        } else {
            $userData['/active'] = 1;
            $userData['/expired_at'] = $accountForm->getExpired();
        }

        return $userData;
    }

    /**
     * @param Account_Form $accountForm
     * @param array $container
     * @return array
     * @todo оставить только services
     */
    abstract protected function getUserData(Account_Form $accountForm, array $container = []);

    /**
     * @param Account_Form|null $accountForm
     * @return bool
     */
    abstract protected function isConfirmed(Account_Form $accountForm = null);

    /**
     * @param Account_Form $accountForm
     * @param Token $token
     * @return mixed
     */
    abstract protected function sendConfirmToken(Account_Form $accountForm, Token $token);

    /**
     * @param Account_Form $accountForm
     * @return Token
     */
    abstract protected function getConfirmToken(Account_Form $accountForm);

    /**
     * @param Account_Form $accountForm
     * @return array
     */
    abstract public function registerVerify(Account_Form $accountForm);

    /**
     * @param Account_Form $accountForm
     * @return mixed
     * @throws Error
     * @throws Exception
     * @throws Security_User_NotActive
     * @throws Config_Error
     * @throws FileNotFound
     * @throws Security_Account_NotFound
     */
    final public function signIn(Account_Form $accountForm)
    {
        $logger = $accountForm->getLogger();

        $user = $this->getUser();

        if (!$user) {
            throw new Security_User_NotFound('User not found', $this->get());
        }

        if (!$user->isActive()) {
            throw new Security_User_NotActive('User not active', $this->get());
        }

        $this->autoProlongate($accountForm);

        $user
            ->set(['/logined_at' => Date::get()])
            ->save();

        return Security::getInstance()->login($this, $accountForm->get());
    }

    /**
     * @return Model|Model_Account|null
     */
    public function getUser()
    {
        return $this->fetchOne(User::class, '*', true, -1);
    }

    public function isActive()
    {
        return (bool)$this->get('/active');
    }

    /**
     * @param Account_Form $accountForm
     * @throws Exception
     */
    protected function autoProlongate(Account_Form $accountForm)
    {
        if ($expired = $this->isExpired()) {
            if ($prolongate = $accountForm->getProlongate()) {
                if ($prolongate === true) {
                    $expired = $this->prolongate($accountForm->getExpired());
                } else {
                    $expired = call_user_func((string)$prolongate, $this, $accountForm->getExpired());
                }
            }

            // todo: кидать исключение, вместо  ифа его ловить
            if ($expired) {
                $accountForm->getLogger()->exception('Account is expired', __FILE__, __LINE__);
            }
        }
    }

    /**
     * Check is expired account
     *
     * @return bool
     * @throws Exception
     * @throws \Exception
     */
    public function isExpired()
    {
        return Date::expired($this->getExpiredAt());
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getExpiredAt()
    {
        return $this->get('/expired_at');
    }

    abstract public function prolongate($expired);

    /**
     * @return mixed
     * @throws Exception
     */
    final public function signOut()
    {
        return Security::getInstance()->logout();
    }

    /**
     * @param Account_Form $accountForm
     * @return array
     */
    abstract public function loginVerify(Account_Form $accountForm);
}