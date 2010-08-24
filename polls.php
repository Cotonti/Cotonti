<?php
/**
 * Polls loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

define('SED_CODE', true);

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'] . '/common.php';

parse_str($_SERVER['QUERY_STRING'], $params);

header('Location: '.sed_url('polls', $params), true, 301);

?>