<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Users module main
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsDictionary;

defined('COT_CODE') or die('Wrong URL.');

// Environment
const COT_USERS = true;

$env['location'] = 'users';

require_once cot_incfile('extrafields');
require_once cot_incfile('uploads');

require_once cot_incfile('users', ExtensionsDictionary::TYPE_MODULE);

$m = !in_array($m, ['details', 'edit', 'passrecover', 'profile', 'register']) ? 'main' : $m;

include cot_incfile('users', 'module', $m);