<?php
/**
 * Administration panel loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_ADMIN', TRUE);
$location = 'Administration';
$z = 'admin';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

require_once $cfg['modules_dir'] . '/admin/functions.php';
require_once $cfg['modules_dir'] . '/admin/resources.php';
require_once sed_langfile('admin', 'module');

require_once $cfg['modules_dir'] . '/admin/main.inc.php';

?>