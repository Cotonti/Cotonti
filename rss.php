<?php
/**
 * RSS loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author medar, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

define('COT_CODE', true);

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'] . '/common.php';

parse_str($_SERVER['QUERY_STRING'], $params);

$env['status'] = '301 Moved Permanently';
cot_redirect(cot_url('rss', $params));

?>