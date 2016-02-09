<?php

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
    private $lifetime = 2592000;

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
        Session::createQueryBuilder()
            ->eq(['/pk' => $session_id])
            ->getUpdateQuery(['/deleted_at' => Date::get()])
            ->getQueryResult();

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
            ->getSelectQuery('*')
            ->getRow();

        if ($this->session) {
            if ($this->session['session_deleted_at'] != Date::ZERO || $this->session['session_lifetime'] <= strtotime($this->session['session_updated_at']) - strtotime($this->session['session_created_at'])) {
                $this->session['session_data'] = '';
                session_regenerate_id();
            }
        } else {
            $this->session = ['session_data' => ''];
            $this->session = array_merge($this->session, $this->getConstFields());
        }

        return $this->session['session_data'];
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
        $this->session['session_data'] = $session_data;
        $this->session['session_updated_at'] = Date::get();

        if (isset($this->session['session_pk']) && $this->session['session_pk'] == $session_id) {
            $this->session['views']++;

            Session::createQueryBuilder()
                ->eq(['/pk' => $session_id])
                ->getUpdateQuery($this->session)
                ->getQueryResult();
        } else {
            if (isset($this->session['session_pk'])) {
                $this->session['session__fk'] = $this->session['session_pk'];
            }

            $this->session['session_pk'] = $session_id;

            unset($this->session['session_created_at']);
            unset($this->session['session_deleted_at']);

            $this->session = array_merge($this->session, $this->getVarFields());

            Session::createQueryBuilder()
                ->getInsertQuery($this->session)
                ->getQueryResult();
        }
    }

    function getConstFields()
    {
        return [
            'ip' => Request::ip(),
            'agent' => Request::agent(),
            'session_lifetime' => $this->lifetime
        ];
    }

    function getVarFields()
    {
        return [
            'user__fk' => Security::getInstance()->getUser()->getPkValue(),
            'views' => 1
        ];
    }
}
