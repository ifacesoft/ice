<?php
/**
 * Ice core session class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\SessionHandler;

use Ice\Core;
use Ice\Core\Request;
use Ice\Core\Security;
use Ice\Core\SessionHandler;
use Ice\Helper\Date;
use Ice\Model\Session;

/**
 * Class Session
 *
 * Core session handler class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage SessionHandler
 */
class DataSource extends SessionHandler
{
    use Core;

    private $lifetime =  2592000;
    private $session = null;
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
     * @param  int $session_id The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function destroy($session_id)
    {
        Session::createQueryBuilder()->getDeleteQuery($session_id)->getQueryResult();

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
     * @param  string $session_id The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function read($session_id)
    {
        $this->session = Session::createQueryBuilder()
            ->eq(['/pk' => $session_id])
            ->limit(1)
            ->getSelectQuery(['/data', '/created_at', '/updated_at', '/lifetime', 'views'])
            ->getRow();

        if ($this->session) {
            if ($this->session['session_lifetime'] > strtotime($this->session['session_updated_at']) - strtotime($this->session['session_created_at'])) {
                return $this->session['session_data'];
            }
        }

        $this->session = [
            'session_pk' => $session_id,
            'session_data' => '',
            'session_updated_at' => Date::get(),
            'session_lifetime' => $this->lifetime,
            'ip' => Request::ip(),
            'agent' => Request::agent(),
            'user__fk' => Security::getInstance()->getUser()->getPkValue(),
            'views' => 0
        ];

        Session::createQueryBuilder()->getInsertQuery($this->session)->getQueryResult();

        return '';
    }

    /**
     * PHP >= 5.4.0<br/>
     * Write session data
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.write.php
     * @param  string $session_id The session id.
     * @param  string $session_data <p>
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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function write($session_id, $session_data)
    {
        Session::createQueryBuilder()
            ->eq(['/pk' => $session_id])
            ->getUpdateQuery([
                '/data' => $session_data,
                '/updated_at' => Date::get(),
                'views' => ++$this->session['views']
            ])
            ->getQueryResult();
    }
}
