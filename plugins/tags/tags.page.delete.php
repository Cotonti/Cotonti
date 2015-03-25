<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done,i18n.page.delete.done
[END_COT_EXT]
==================== */

/**
 * Removes tags when removing a page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W'))
{
	require_once cot_incfile('tags', 'plug');
	if (cot_get_caller() == 'i18n.page')
	{
		$tags_extra = array('tag_locale' => $i18n_locale);
	}
	else
	{
		$tags_extra = null;
	}
	cot_tag_remove_all($id, 'pages', $tags_extra);
}
