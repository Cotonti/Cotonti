<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extensions.details,admin.extensions.plug.list.loop
[END_COT_EXT]
==================== */

/**
 * Comments plugin has no standalone page
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 * @var string $code Extension code
 */

defined('COT_CODE') or die('Wrong URL');

if ($code === 'comments') {
    $t->assign(['ADMIN_EXTENSIONS_JUMPTO_URL' => '']);
}
