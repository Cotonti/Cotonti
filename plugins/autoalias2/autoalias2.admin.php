<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Creates aliases in existing pages with empty alias
 *
 * @package autoalias2
 * @version 2.1.2
 * @author Trustmaster
 * @copyright (c) Cotonti Team 2010-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('autoalias2', 'plug');

$out['subtitle'] = 'AutoAlias';

if ($a == 'create')
{
	$count = 0;
	$res = $db->query("SELECT page_id, page_title FROM $db_pages WHERE page_alias = ''");
	foreach ($res->fetchAll() as $row)
	{
		autoalias2_update($row['page_title'], $row['page_id']);
		$count++;
	}
	$res->closeCursor();
	$plugin_body .= <<<HTM
<div class="error">
	{$L['aliases_written']}: $count
</div>
HTM;
}

$create_url = cot_url('admin', 'm=other&p=autoalias2&a=create');
$plugin_body .= <<<HTM
<a href="$create_url">{$L['create_aliases']}</a>
HTM;
?>
