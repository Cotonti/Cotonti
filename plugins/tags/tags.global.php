<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Tags: supplimentary files connection
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages']
	&& (defined('COT_INDEX') || defined('COT_LIST') || defined('COT_PAGE'))
	|| $cfg['plugin']['tags']['forums'] && defined('COT_FORUMS')
	|| defined('COT_PLUG'))
{
	require_once cot_incfile('tags', 'plug');
}
