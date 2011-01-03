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
 * @author Seditio.by
 * @copyright (c) 2008-2010 Seditio.by and Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

if (isset($cot_captcha))
{
    if (!function_exists (cot_captcha_generate))
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
$rtext = cot_import('rtext', 'P', 'TXT');
$ruser = cot_import('ruser', 'P', 'TXT');
$remail = cot_import('remail', 'P', 'TXT');
$rsubject = cot_import('rsubject', 'P', 'TXT');

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


	if ($ruser == '')
	{
		cot_error('contact_noname', 'ruser');
	}
	if (mb_strlen($remail) < 4 || !preg_match('#^[\w\p{L}][\.\w\p{L}\-]+@[\w\p{L}\.\-]+\.[\w\p{L}]+$#u', $remail))
	{
		cot_error('contact_emailnotvalid', 'remail');
	}
	if (mb_strlen($rtext) < $cfg['plugin']['contact']['minchars'])
	{
		cot_error('contact_entrytooshort', 'rtext');
	}

	if (!cot_error_found())
	{
		$db->insert($db_contact, array(
			'contact_author' => $ruser,
			'contact_authorid' => $usr['id'],
			'contact_text' => $rtext,
			'contact_date' => $sys['now_offset'],
			'contact_email' => $remail,
			'contact_subject' => $rsubject,
			'contact_val' => 0
		));

		$semail = (!empty($cfg['plugin']['contact']['email'])) ? $cfg['plugin']['contact']['email'] : $cfg['adminemail'];
		if (mb_strlen($semail) > 4 || preg_match('#^[\w\p{L}][\.\w\p{L}\-]+@[\w\p{L}\.\-]+\.[\w\p{L}]+$#u', $semail))
		{
			$headers = ("From: \"" . $ruser . "\" <" . $remail . ">\n");
			$rtextm = $cfg["maintitle"] . " - " . $cfg['mainurl'] . " \n\n" .
				$L['Sender'] . ": " . $ruser . " (" . $remail . ") \n";
			$rtextm .= ( $rsubject != '') ? $L['Topic'] . ": " . $rsubject . "\n" : "";
			$rtextm .= $L['Message'] . ":\n" . $rtext;
			cot_mail($semail, $rsubject, $rtextm, $headers);
		}
		$sent = true;
		cot_message('contact_message_sent');
	}
}

cot_display_messages($t);

if (!$sent)
{
	$t->assign(array(
		'CONTACT_FORM_SEND' => cot_url('plug', 'e=contact'),
		'CONTACT_FORM_AUTHOR' => ($usr['id'] == 0) ? cot_inputbox('text', 'ruser', $ruser, 'size="24" maxlength="24"')
				: cot_inputbox('text', 'ruser', $usr['name'], 'size="24" maxlength="24" readonly="readonly"'),
		'CONTACT_FORM_EMAIL' => cot_inputbox('text', 'remail', $remail, 'size="24"'),
		'CONTACT_FORM_SUBJECT' => cot_inputbox('text', 'rsubject', $rsubject, 'size="24"'),
		'CONTACT_FORM_TEXT' => cot_textarea('rtext', $rtext, 8, 50, 'style="width:90%"')
	));

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