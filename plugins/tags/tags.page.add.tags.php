<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.tags
Tags=page.add.tpl:{PAGEADD_FORM_TAGS},{PAGEADD_TOP_TAGS},{PAGEADD_TOP_TAGS_HINT}
[END_COT_EXT]
==================== */

/**
 * Generates tag inputs when adding a new page
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	sed_require('tags', true);
	$t->assign(array(
		'PAGEADD_TOP_TAGS' => $L['Tags'],
		'PAGEADD_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'PAGEADD_FORM_TAGS' => sed_rc('tags_input_editpage', array('tags' => ''))
	));
	$t->parse('MAIN.TAGS');
}

?>