<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
  ==================== */

/**
 * Contact Plugin for Cotonti CMF
 *
 * @package Contact
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('contact', 'plug');

$extra_whitelist[$db_contact] = array(
	'name' => $db_contact,
	'caption' => $L['Plugin'].' Contact',
	'type' => 'plug',
	'code' => 'contact',
	'tags' => array(
		'contact.tools.tpl' => '{CONTACT_XXXXX}, {CONTACT_XXXXX_TITLE}',
		'contact.tpl' => '{CONTACT_FORM_XXXXX}, {CONTACT_FORM_XXXXX_TITLE}',
	)
);
