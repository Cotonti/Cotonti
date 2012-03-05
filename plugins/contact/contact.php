<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=standalone
  [END_COT_EXT]
  ==================== */

/**
 * Contact Plugin for Cotonti CMF
 *
 * @package contact
 * @version 2.1.0
 * @author Cotonti Team
 * @copyright (c) 2008-2012 Cotonti Team
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

if (isset($cot_captcha))
{
	if (!function_exists(cot_captcha_generate))
	{

		function cot_captcha_generate($func_index = 0)
		{
			global $cot_captcha;
			if (!empty($cot_captcha[$func_index]))
			{
				$captcha = $cot_captcha[$func_index] . '_generate';
				return $captcha();
			}
			return false;
		}

	}
	if (!function_exists(cot_captcha_validate))
	{

		function cot_captcha_validate($verify = 0, $func_index = 0)
		{
			global $cot_captcha;
			if (!empty($cot_captcha[$func_index]))
			{
				$captcha = $cot_captcha[$func_index] . '_validate';
				return $captcha($verify);
			}
			return false;
		}

	}
}


//Import the variables
$rcontact['contact_text'] = cot_import('rtext', 'P', 'TXT');
$rcontact['contact_author'] = cot_import('ruser', 'P', 'TXT');
$rcontact['contact_email'] = cot_import('remail', 'P', 'TXT');
$rcontact['contact_subject'] = cot_import('rsubject', 'P', 'TXT');

// Extra fields
foreach ($cot_extrafields[$db_contact] as $row)
{
	$rcontact['contact_' . $row['field_name']] = cot_import_extrafields('rcontact' . $row['field_name'], $row);
}

if (isset($_POST['rtext']))
{
	if ($usr['id'] == 0 && isset($cot_captcha))
	{
		$rverify = cot_import('rverify', 'P', 'TXT');
		if (!cot_captcha_validate($rverify))
		{
			cot_error('captcha_verification_failed', 'rverify');
		}
	}


	if ($rcontact['contact_author'] == '')
	{
		cot_error('contact_noname', 'ruser');
	}
	if (!cot_check_email($rcontact['contact_email']))
	{
		cot_error('contact_emailnotvalid', 'remail');
	}
	if (mb_strlen($rcontact['contact_text']) < $cfg['plugin']['contact']['minchars'])
	{
		cot_error('contact_entrytooshort', 'rtext');
	}

	if (!cot_error_found())
	{
		$rcontact['contact_authorid'] = (int) $usr['id'];
		$rcontact['contact_date'] = (int) $sys['now_offset'];
		$rcontact['contact_val'] = 0;
		
		if (in_array($cfg['plugin']['contact']['save'], array('db','both')))
		{
			$db->insert($db_contact, $rcontact);
		}
		
		$semail = (!empty($cfg['plugin']['contact']['email'])) ? $cfg['plugin']['contact']['email'] : $cfg['adminemail'];
		if (cot_check_email($semail) && in_array($cfg['plugin']['contact']['save'], array('email','both')))
		{
			$headers = ("From: \"" . $rcontact['contact_author'] . "\" <" . $rcontact['contact_email'] . ">\n");
			$rtextm = $cfg["maintitle"] . " - " . $cfg['mainurl'] . " \n\n" .
				$L['Sender'] . ": " . $rcontact['contact_author'] . " (" . $rcontact['contact_email'] . ") \n";
			$rtextm .= ( $rcontact['contact_subject'] != '') ? $L['Topic'] . ": " . $rcontact['contact_subject'] . "\n" : "";
			$rtextm .= $L['Message'] . ":\n" . $rcontact['contact_text'];

			foreach ($cot_extrafields[$db_contact] as $row)
			{
				$ex_title = isset($L['contact_' . $row['field_name'] . '_title']) ? $L['contact_' . $row['field_name'] . '_title'] : $row['field_description'];
				$ex_body = cot_build_extrafields_data('contact', $row, $rcontact["contact_{$row['field_name']}"]);
				$rtextm .= "\n".$ex_title.": ".$ex_body;
			}

			cot_mail($semail, $rcontact['contact_subject'], $rtextm, $headers);
		}
		$sent = true;
		cot_message('contact_message_sent');

		cot_extrafield_movefiles();
	}
}

$out['subtitle'] = $L['contact_title'];

cot_display_messages($t);

if (!$sent)
{
	$t->assign(array(
		'CONTACT_FORM_SEND' => cot_url('plug', 'e=contact'),
		'CONTACT_FORM_AUTHOR' => ($usr['id'] == 0) ? cot_inputbox('text', 'ruser', $rcontact['contact_author'], 'size="24" maxlength="24"') : cot_inputbox('text', 'ruser', $usr['name'], 'size="24" maxlength="24" readonly="readonly"'),
		'CONTACT_FORM_EMAIL' => cot_inputbox('text', 'remail', $rcontact['contact_email'], 'size="24"'),
		'CONTACT_FORM_SUBJECT' => cot_inputbox('text', 'rsubject', $rcontact['contact_subject'], 'size="24"'),
		'CONTACT_FORM_TEXT' => cot_textarea('rtext', $rcontact['contact_text'], 8, 50, 'style="width:90%"')
	));

	// Extra fields
	foreach ($cot_extrafields[$db_contact] as $i => $row)
	{
		$uname = strtoupper($row['field_name']);
		$t->assign('CONTACT_FORM_' . $uname, cot_build_extrafields('rcontact' . $row['field_name'], $row, $rcontact[$row['field_name']]));
		$t->assign('CONTACT_FORM_' . $uname . '_TITLE', isset($L['contact_' . $row['field_name'] . '_title']) ? $L['contact_' . $row['field_name'] . '_title'] : $row['field_description']);

		// extra fields universal tags
		$t->assign('CONTACT_FORM_EXTRAFLD', cot_build_extrafields('rcontact' . $row['field_name'], $row, $rcontact[$row['field_name']]));
		$t->assign('CONTACT_FORM_EXTRAFLD_TITLE', isset($L['contact_' . $row['field_name'] . '_title']) ? $L['contact_' . $row['field_name'] . '_title'] : $row['field_description']);
		$t->parse('MAIN.FORM.EXTRAFLD');
	}
	if ($usr['id'] == 0 && isset($cot_captcha))
	{

		$t->assign(array(
			'CONTACT_FORM_VERIFY_IMG' => cot_captcha_generate(),
			'CONTACT_FORM_VERIFY' => cot_inputbox('text', 'rverify', $rverify, 'id="rverify" size="20"')
		));
		$t->parse('MAIN.FORM.CAPTCHA');
	}
	$t->parse('MAIN.FORM');
}
?>