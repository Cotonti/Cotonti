<?php
/**
 * Administration panel loader
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

define('COT_CODE', TRUE);
define('COT_ADMIN', TRUE);
define('COT_CORE', TRUE);

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';

$env['location'] = 'administration';
$env['ext'] = 'admin';

require_once $cfg['system_dir'] . '/cotemplate.php';
require_once $cfg['system_dir'] . '/common.php';

require_once cot_incfile('admin', 'module');

include cot_incfile('admin', 'module', 'main');
