<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
[END_COT_EXT]
==================== */

/**
 * Header notifications
 *
 * @package contact
 * @version 2.1.0
 * @author Seditio.by
 * @copyright (c) 2008-2010 Seditio.by and Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (cot_auth('plug', 'contact', 'A'))
{
	require_once cot_incfile('contact', 'plug');

    $new_contact = $db->query("SELECT COUNT(*) FROM $db_contact WHERE contact_val=0")->fetchColumn();
    $notify_contact = ($new_contact > 0) ? cot_rc_link(cot_url('admin','m=other&p=contact'), cot_declension($new_contact, $Ls['contact_headercontact'])) : '';
    if (empty($out['notices']) && !empty($notify_contact))
    {
        $out['notices'] = $L['hea_valqueues'] .' ' . $notify_contact;
    }
	elseif (!empty($out['notices']) && !empty($notify_contact))
	{
		$out['notices'] .= '| ' . $notify_contact;
	}
}

?>