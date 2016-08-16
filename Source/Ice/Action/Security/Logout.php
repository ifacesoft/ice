<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Exception\Http_Redirect;

/**
 * Class Security_Logout
 *
 * @see     Ice\Core\Action
 * @see     Ice\Core\Action_Context;
 * @package Ice\Action;
 *
 * @author dp <email>
 */
class Security_Logout extends Security
{
    /**
     * Run action
     *
     * @param  array $input
     * @return array
     * @throws Http_Redirect
     */
    public function run(array $input)
    {
        session_destroy();

        return parent::run($input);
    }
}
