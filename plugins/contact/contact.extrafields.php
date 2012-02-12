<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
  ==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('contact', 'plug');

$extra_whitelist[$db_contact] = array(
	'name' => $db_contact,
	'caption' => $L['Plugin'].' Contact',
	'help' => $L['adm_help_contact_extrafield'],
	'tags' => array(
		'contact.tools.tpl' => '{CONTACT_XXXXX}, {CONTACT_XXXXX_TITLE}',
		'contact.tpl' => '{CONTACT_FORM_XXXXX}, {CONTACT_FORM_XXXXX_TITLE}',
	)
);

?>