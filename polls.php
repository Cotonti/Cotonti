<?php
/**
 * Polls loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_POLLS', TRUE);
$location = 'Polls';
$z = 'polls';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

sed_dieifdisabled($cfg['disable_polls']);

require_once $cfg['modules_dir'] . '/polls/functions.php';
require_once sed_langfile('polls', 'module');

require_once $cfg['modules_dir'] . '/polls/polls.inc.php';

?>