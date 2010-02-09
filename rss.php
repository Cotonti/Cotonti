<?php
/**
 * RSS loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author medar, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_RSS', TRUE);
$location = "RSS";
$z = 'rss';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

sed_dieifdisabled($cfg['disable_rss']);

require_once sed_langfile('rss', 'module');

require_once $cfg['modules_dir'] . '/rss/main.inc.php';

?>