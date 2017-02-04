<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Creates aliases in existing pages with empty alias
 *
 * @package AutoAlias
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('autoalias2', 'plug');
require_once cot_langfile('autoalias2', 'plug');

$t = new XTemplate(cot_tplfile('autoalias2.admin', 'plug', true));

$adminsubtitle = cot::$L['AutoAlias2'];

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
	cot_message(cot_rc('aliases_written', $count));
	cot_redirect(cot_url('admin', 'm=other&p=autoalias2', '', true));
}

$t->assign('AUTOALIAS_CREATE', cot_url('admin', 'm=other&p=autoalias2&a=create'));

cot_display_messages($t);

$t->parse();
$plugin_body = $t->text('MAIN');
