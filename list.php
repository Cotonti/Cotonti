<?php
/**
 * List loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_LIST', TRUE);
$location = 'List';
$z = 'list';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

sed_dieifdisabled($cfg['disable_page']);

require_once sed_langfile('list', 'module');

require_once $cfg['system_dir'] . '/extrafields.php';

require_once $cfg['modules_dir'] . '/list/list.inc.php';

?>