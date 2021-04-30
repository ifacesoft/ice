<?php

namespace Ice\Core;

use Ice\Exception\Error;
use Ice\Exception\Security_User_NotActive;
use Ice\Model\User;
use Ice\Exception\Security_Account_AttachForbidden;
use Ice\Helper\Date;
use Ice\Helper\Type_String;
use Ice\Model\Token;
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

        /** @var DataSource $dataSource */
        $dataSource = $accountModelClass::getDataSource();

        try {
            $dataSource->beginTransaction();

            // todo: должно работать в зависимости от конфиг (см. prolongate)
            $this->registerConfirm($accountForm, $this->signUpAccount($accountForm, $this->signUpUser($accountForm, $container), $container), $container);

            $dataSource->commitTransaction();
        } catch (\Exception $e) {
            $dataSource->rollbackTransaction($e);

            throw $e;
        }

        return $this;
    }

    /**
     * @param array $values
     * @return array
     */
    abstract public function registerVerify(array $values);

    /**
     * @param Account_Form $accountForm
     * @param Model_Account $account
     * @throws Exception
     */
    private function registerConfirm(Account_Form $accountForm, Model_Account $account, $container = [])
    {
        if ($accountForm->isConfirm()) {
            $token = isset($container['token'])
                ? $container['token']
                : Token::create(['/data' => []]);

            $token->set(['/' => md5(Type_String::getRandomString()),
                '/expired' => $accountForm->getConfirmationExpired(),
                'modelClass' => get_class($account),
                '/data' => array_merge(
                    [
                        'function' => __FUNCTION__,
                        'account' => $account->get(),
                        'account_expired' => $accountForm->getExpired()
                    ],
                    $token->get('/data'))
            ]);

            $account->set([
                'token' => $token->save()
            ])->save();

            $this->sendRegisterConfirm($accountForm, $account->get(Token::class));
        }
    }

    /**
     * @param Account_Form $accountForm
     * @param Token $token
     * @return mixed
     */
    abstract protected function sendRegisterConfirm(Account_Form $accountForm, Token $token);

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
     * @param Account_Form $accountForm
     * @param null $dataSourceKey
     * @return mixed
     * @throws Exception
     */
    final public function signIn(Account_Form $accountForm, $dataSourceKey = null)
    {
        /** @var Logger $logger */
        $logger = $accountForm->getLogger();

        $user = $this->getUser();

        if (!$user) {
//            file_put_contents(\getLogDir() . $this->getPkValue() . '.user.debug.log',print_r([$accountForm, $this, $this->getUser()], true) . "\n\n\n", FILE_APPEND);
            $logger->exception('User not found', __FILE__, __LINE__);
        } elseif (!$user->isActive()) {
            $logger->info('User is not active', Logger::DANGER, true);
            throw new Security_User_NotActive('User is not active');
        }

        $this->autoProlongate($accountForm);

        try {
            $user->set(['last_login' => Date::get()])->save();
        } catch (\Exception $e) {
        } catch (\Throwable $e) {
        }

        return Security::getInstance()->login($this, $dataSourceKey);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    final public function signOut()
    {
        return Security::getInstance()->logout();
    }

    /**
     * @param null $dataSourceKey
     * @return Model|Model_Account|null
     */
    public function getUser($dataSourceKey = null)
    {
        return $this->fetchOne(User::class, '*', true, -1, $dataSourceKey);
    }

    /**
     * @param array $values
     * @return array
     */
    abstract public function loginVerify(array $values);

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
     */
    public function isExpired()
    {
        return strtotime($this->getExpiredAt()) < time();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getExpiredAt()
    {
        return $this->getUser()->get('/expired_at');
    }

    abstract public function prolongate($expired);
}