<?php
/**
 * Polls module
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

// Environment setup
define('COT_CODE', true);
define('COT_MODULE', true);
$env['ext'] = 'polls';

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

require_once $cfg['modules_dir'] . '/polls/polls.php';

?>