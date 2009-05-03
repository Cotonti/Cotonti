<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.add.tags
File=tags.page.add.tags
Hooks=page.add.tags
Tags=page.add.tpl:{PAGEADD_FORM_TAGS},{PAGEADD_TOP_TAGS},{PAGEADD_TOP_TAGS_HINT}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	require_once(sed_langfile('tags'));
	$t->assign(array(
	'PAGEADD_TOP_TAGS' => $L['Tags'],
	'PAGEADD_TOP_TAGS_HINT' => $L['tags_comma_separated'],
	'PAGEADD_FORM_TAGS' => '<input type="text" name="rtags" />'
	));
	$t->parse('MAIN.TAGS');
}

?>