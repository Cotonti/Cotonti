<?php
/**
 * Administration panel loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_ADMIN', TRUE);
define('COT_CORE', TRUE);
$location = 'Administration';
$z = 'admin';

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'] . '/common.php';
sed_require_api('xtemplate');

sed_require('admin');

include sed_incfile('admin', 'main');

?>