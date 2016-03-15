<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
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

$indexnews_html = cot_page_enum($cfg['plugin']['indexnews']['category'], $cfg['plugin']['indexnews']['maxpages'],
	cot_tplfile('indexnews', 'plug'), '', '', true, true, false, '', 'd', (int)$cfg['plugin']['indexnews']['cache_ttl']);


$t->assign('INDEX_NEWS', $indexnews_html);