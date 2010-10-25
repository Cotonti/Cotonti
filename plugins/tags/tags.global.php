<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Tags: supplimentary files connection
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages']
	&& (defined('COT_INDEX') || defined('COT_LIST') || defined('COT_PAGE'))
	|| $cfg['plugin']['tags']['forums'] && defined('COT_FORUMS')
	|| defined('COT_PLUG'))
{
	cot_require('tags', true);
}

?>