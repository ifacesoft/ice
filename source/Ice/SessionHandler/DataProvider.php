<?php
/**
 * Ice core session class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\SessionHandler;

use Ice\Core\DataProvider as Core_DataProvider;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Request;
use Ice\Core\SessionHandler;
use Ice\DataProvider\Redis;
use Ice\Exception\Error;

/**
 * Class Session
 *
 * Core session handler class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class DataProvider extends SessionHandler
{
//cookie_lifetime: 10800
//cookie_domain: %cookie_domain%
//gc_maxlifetime: 10800
//gc_probability: 1
//gc_divisor: 1000

    /**
     * PHP >= 5.4.0<br/>
     * Close the session
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.close.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function close()
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Destroy a session
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.destroy.php
     * @param int $session_id The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @throws Exception
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.6
     * @since   1.5
     */
    public function destroy($session_id)
    {
        $sessionConfig = Environment::getInstance()->getConfig('ini_set_session');

        $this
            ->getSessionDataProvider()
            ->hSet(
                $this->getSessionPk(),
                ['session_deleted_at' => time()],
                $sessionConfig->get('gc_maxlifetime')
            );

        return true;
    }

    /**
     * @return Redis|Core_DataProvider
     * @throws Exception
     */
    public function getSessionDataProvider()
    {
        return Core_DataProvider::getInstance(Redis::class, 'session');
    }

    /**
     * PHP >= 5.4.0<br/>
     * Cleanup old sessions
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.gc.php
     * <p>
     * Sessions that have not updated for
     * the last maxlifetime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Initialize session
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.open.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function open($save_path, $session_id)
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Read session data
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.read.php
     * @param string $session_id The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @throws Exception
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.6
     * @since   1.5
     */
    public function read($session_id)
    {
        $dataProvider = $this->getSessionDataProvider();

        $session = $dataProvider->hGet($session_id);

        // TODO: Нужно через настройки
//        $currentIp = Request::ip();
//        $currentAgent = Request::agent();

        if (
            empty($session['session_created_at']) ||
            !empty($session['session_deleted_at'])
//            || $userIp != $currentIp
//            || $userAgent != $currentAgent
        ) {
            // todo: !!!здесь нужно перегенерировать session_id

            return '';
        }

        $this->setSessionPk($session_id);

        $this->setSessionCreatedAt($session['session_created_at']);

        $sessionLifetime = Environment::getInstance()->getConfig('ini_set_session')->get('gc_maxlifetime');

        $currentTime = time();

        if (!empty($session['session_updated_at']) && $currentTime - $session['session_updated_at'] >= $sessionLifetime) {
            $dataProvider->hSet($session_id, ['session_deleted_at' => $currentTime], $sessionLifetime);
            // todo: !!!здесь нужно перегенерировать session_id

            return '';
        }

        if (empty($session['session_data'])) {
            $session['session_data'] = '';
        }

        return $this->setSessionDataHash($session['session_data']);
    }

    /**
     * PHP >= 5.4.0<br/>
     * Write session data
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.write.php
     * @param string $session_id The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @throws Exception
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.6
     * @since   1.5
     */
    public function write($session_id, $session_data)
    {
        // todo: В сессию писать accountId

        $sessionPk = $this->getSessionPk();
        $this->setSessionPk($session_id);

        $dataProvider = $this->getSessionDataProvider();

        $cookie = session_get_cookie_params();

        $currentTime = time();

        $sessionConfig = Environment::getInstance()->getConfig('ini_set_session');

        setcookie(
            session_name(),
            $this->getSessionPk(),
            $currentTime + $sessionConfig->get('cookie_lifetime'),
            $cookie['path'],
            $cookie['domain'],
            $cookie['secure'],
            $cookie['httponly']
        );

        if (isset($_COOKIE['ice_unique_user_id'])) {
            $iceUniqueUserId = $_COOKIE['ice_unique_user_id'];
        } else {
            $iceUniqueUserId = uniqid(md5(Request::agent() . Request::ip()), true);

            setcookie(
                'ice_unique_user_id',
                $iceUniqueUserId,
                $currentTime + (10 * 365 * 24 * 60 * 60),
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly']
            );
        }

        $sessionLifetime = $sessionConfig->get('gc_maxlifetime');

        $dataProvider->hSet($this->getSessionPk(), ['session_updated_at' => $currentTime], $sessionLifetime);

        if ($this->isSessionDataUpdated($session_data)) {
            $dataProvider->hSet($this->getSessionPk(), ['session_data' => $session_data], $sessionLifetime);
        }

        if (!$this->getSessionCreatedAt()) {
            $dataProvider->hSet(
                $this->getSessionPk(),
                [
                    'session__fk' => null,
                    'session_created_at' => $currentTime,
                    'session_user_ip' => Request::ip(),
                    'session_user_agent' => Request::agent(),
                    'session_read_count' => 1,
                    'ice_unique_user_id' => $iceUniqueUserId
                ],
                $sessionLifetime
            );

            return true;
        }

        if ($sessionPk != $session_id) {
            $dataProvider->hSet(
                $this->getSessionPk(),
                [
                    'session__fk' => $sessionPk,
                    'session_created_at' => $currentTime,
                    'session_user_ip' => Request::ip(),
                    'session_user_agent' => Request::agent(),
                    'session_read_count' => 1,
                    'ice_unique_user_id' => $iceUniqueUserId
                ],
                $sessionLifetime
            );
        } else {
            $dataProvider->hSet(
                $this->getSessionPk(),
                ['session_read_count' => $dataProvider->hGet($this->getSessionPk(), 'session_read_count') + 1],
                $sessionLifetime
            );
        }

        return true;
    }
}
