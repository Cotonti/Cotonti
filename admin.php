<?php
/**
 * Administration panel loader
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\ErrorHandler;

const COT_CODE = true;
const COT_ADMIN = true;
const COT_CORE = true;

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';

$env['location'] = 'administration';
$env['ext'] = 'admin';

require_once $cfg['system_dir'] . '/common.php';

// system/admin/admin.functions.php
require_once cot_incfile('admin', 'module');

try {
    include cot_incfile('admin', 'module', 'main');
} catch (Throwable $e) {
    // Handle error
    if (!ErrorHandler::getInstance()->handle($e)) {
        throw $e;
    }
}