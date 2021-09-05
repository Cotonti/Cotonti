<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=header.main
  [END_COT_EXT]
  ==================== */

/**
 * Header notifications
 *
 * @package Contact
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

global $Ls;

if (!isset($Ls['contact_headercontact'])) include cot_langfile('contact', 'plug');

if (cot_auth('plug', 'contact', 'A')) {
	require_once cot_incfile('contact', 'plug');

	$new_contact = cot::$db->query("SELECT COUNT(*) FROM $db_contact WHERE contact_val=0")->fetchColumn();
	$notify_contact = ($new_contact > 0) ? array(cot_url('admin', 'm=other&p=contact'), cot_declension($new_contact, $Ls['contact_headercontact'])) : '';
	if (!empty($notify_contact))
	{
		$out['notices_array'][] = $notify_contact;
	}
}
