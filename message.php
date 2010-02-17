<?php
/**
 * Messages loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_MESSAGE', TRUE);
define('COT_CORE', TRUE);
$location = 'Messages';
$z = 'message';

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

require_once sed_langfile('message', 'core');

require_once sed_incfile('main', 'message');

?>