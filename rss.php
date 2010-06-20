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

define('SED_CODE', true);

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('common');

parse_str($_SERVER['QUERY_STRING'], $params);

header('Location: '.sed_url('rss', $params), true, 301);

?>