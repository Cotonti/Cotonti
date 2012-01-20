<?php
/**
 * List loader. Permanently (SE-safely) redirects to page module.
 *
 * @package Cotonti
 * @version 0.9.0
 * @deprecated List module no longer exists since Cotonti Siena
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

define('COT_CODE', true);

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'] . '/common.php';

parse_str($_SERVER['QUERY_STRING'], $params);

$env['status'] = '301 Moved Permanently';
cot_redirect(cot_url('page', $params, '', true));

?>