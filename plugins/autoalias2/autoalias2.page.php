<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.add.done,page.edit.update.done
[END_COT_EXT]
==================== */

/**
 * Creates alias when adding or updating a page
 *
 * @package AutoAlias
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (empty($rpage['page_alias']))
{
	require_once cot_incfile('autoalias2', 'plug');
	$rpage['page_alias'] = autoalias2_update($rpage['page_title'], $id);
}
