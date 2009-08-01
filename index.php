<?php
/**
 * Home page loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_INDEX', TRUE);
$location = 'Home';
$z = 'index';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');
require_once($cfg['system_dir'].'/core/index/index.inc.php');

?>