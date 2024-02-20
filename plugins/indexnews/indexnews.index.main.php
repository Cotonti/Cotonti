<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.main
[END_COT_EXT]
==================== */
 
/**
 * plugin Index News for Cotonti Siena
 * 
 * @package Index News
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('page', 'module');

list($pg, $d, $durl) = cot_import_pagenav('d');

if ($durl > 0) {
    $canonicalUrlParams['d'] = $durl;
}