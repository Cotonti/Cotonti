<?php
/**
 * Users loader
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

define('COT_CODE', TRUE);
define('COT_CORE', TRUE);
$env['ext'] = 'users';

if (isset($_GET['m']) && $_GET['m'] == 'auth')
{
	define('COT_AUTH', TRUE);
}

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

require_once $cfg['system_dir'] . '/users/users.php';
?>