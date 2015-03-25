<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.first
[END_COT_EXT]
==================== */

/**
 * Overrides markup in Forums posts
 *
 * @package BBcode
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['parser'] == 'bbcode')
{
	$forums_quote_htmlspecialchars_bypass = true;
	$R['forums_code_quote'] = "[quote]{\$date}[url={\$url}]#{\$id}[/url] [b]{\$postername} :[/b]\n{\$text}\n[/quote]";
	$R['forums_code_update'] = "\n\n[b]{\$updated}[/b]\n\n";
}
