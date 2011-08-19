<?php
/**
 * Page module main
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

define('COT_CODE', true);

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

require_once $cfg['modules_dir'] . '/page/page.php';

?>