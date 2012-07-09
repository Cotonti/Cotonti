<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.first
[END_COT_EXT]
==================== */

/**
 * Overrides markup in Forums posts
 *
 * @package html
 * @version 0.9.1
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['parser'] == 'html')
{
	$forums_quote_htmlspecialchars_bypass = true;
	$R['forums_code_quote'] = '<blockquote><a href="{$url}">#{$id}</a> <strong>{$postername}: </strong><br />{$text}</blockquote><p>&nbsp;</p>';
	$R['forums_code_update'] = '<p><strong>{$updated}</strong></p>';
}
?>
