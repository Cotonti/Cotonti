<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=standalone
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

if (!empty($cot_captcha))
{
	if (!function_exists('cot_captcha_generate'))
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
	if (!function_exists('cot_captcha_validate'))
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

$tplfile = cot_import('tpl', 'G', 'TXT');
$mskin = cot_tplfile(array('contact', $tplfile), 'plug');
$t = new XTemplate($mskin);
$rtext = cot_import('rtext', 'P', 'TXT');

if (!empty($rtext))
{
	//Import the variables
	$rcontact['contact_text'] = $rtext;
	$rcontact['contact_author'] = cot_import('ruser', 'P', 'TXT');
	$rcontact['contact_email'] = cot_import('remail', 'P', 'TXT');
	$rcontact['contact_subject'] = cot_import('rsubject', 'P', 'TXT');

	// Extra fields
    if(!empty(cot::$extrafields[cot::$db->contact])) {
        foreach (cot::$extrafields[cot::$db->contact] as $exfld) {
            $rcontact['contact_' . $exfld['field_name']] = cot_import_extrafields('rcontact' . $exfld['field_name'],
                $exfld, 'P', '', 'contact_');
        }
    }

	if (cot::$usr['id'] == 0 && !empty($cot_captcha))
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
	if (mb_strlen($rcontact['contact_text']) < cot::$cfg['plugin']['contact']['minchars'])
	{
		cot_error('contact_entrytooshort', 'rtext');
	}

	if (!cot_error_found())
	{
		$rcontact['contact_authorid'] = (int) $usr['id'];
		$rcontact['contact_date'] = (int) $sys['now'];
		$rcontact['contact_val'] = 0;

		if (in_array($cfg['plugin']['contact']['save'], array('db','both')))
		{
			$db->insert($db_contact, $rcontact);
		}

		$semail = (!empty($cfg['plugin']['contact']['email'])) ? $cfg['plugin']['contact']['email'] : $cfg['adminemail'];
		if (cot_check_email($semail) && in_array($cfg['plugin']['contact']['save'], array('email','both')))
		{
			$headers = ("From: \"" . $rcontact['contact_author'] . "\" <" . $rcontact['contact_email'] . ">\n");
			$context = array(
				'sitetitle' => $cfg["maintitle"],
				'siteurl' => $cfg['mainurl'],
				'author' => $rcontact['contact_author'],
				'email' => $rcontact['contact_email'],
				'subject' => $rcontact['contact_subject'],
				'text' => $rcontact['contact_text']
			);
            $rextras = '';
            if(!empty(cot::$extrafields[cot::$db->contact])) {
                foreach (cot::$extrafields[cot::$db->contact] as $exfld) {
                    $exfld_title = cot_extrafield_title($exfld, 'contact_');
                    $ex_body = cot_build_extrafields_data('contact', $exfld, $rcontact['contact_' . $exfld['field_name']]);
                    $rextras .= "\n" .  $exfld_title . ": " . $ex_body;

                    $context['extra' . $exfld['field_name']] = $ex_body;
                    $context['extra' . $exfld['field_name'] . '_title'] =  $exfld_title;
                    $context['extra' . $exfld['field_name'] . '_value'] = $rcontact['contact_' . $exfld['field_name']];

                }
            }
			$context['extra'] = $rextras;
			$rtextm = cot_rc(empty(cot::$cfg['plugin']['contact']['template']) ? cot::$R['contact_message'] : cot::$cfg['plugin']['contact']['template'], $context);
			cot_mail($semail, $rcontact['contact_subject'], $rtextm, $headers);
		}
		$sent = true;
		cot_message('contact_message_sent');

		cot_extrafield_movefiles();
	}
}

cot::$out['subtitle'] = cot::$L['contact_title'];

cot_display_messages($t);

if (!$sent)
{
	$t->assign(array(
		'CONTACT_FORM_SEND' => cot_url('plug', 'e=contact&tpl='.$tplfile),
		'CONTACT_FORM_AUTHOR' => (cot::$usr['id'] == 0) ? cot_inputbox('text', 'ruser', $rcontact['contact_author'], 'size="24" maxlength="24"') :
            cot_inputbox('text', 'ruser', cot::$usr['name'], 'size="24" maxlength="24" readonly="readonly"'),
		'CONTACT_FORM_EMAIL' => cot_inputbox('text', 'remail', $rcontact['contact_email'], 'size="24"'),
		'CONTACT_FORM_SUBJECT' => cot_inputbox('text', 'rsubject', $rcontact['contact_subject'], 'size="24"'),
		'CONTACT_FORM_TEXT' => cot_textarea('rtext', $rcontact['contact_text'], 8, 50, 'style="width:90%"')
	));

	// Extra fields
    if(!empty(cot::$extrafields[cot::$db->contact])) {
        foreach (cot::$extrafields[cot::$db->contact] as $exfld) {
            $uname = strtoupper($exfld['field_name']);
            $exfld_val = cot_build_extrafields('rcontact' . $exfld['field_name'], $exfld, $rcontact['contact_'.$exfld['field_name']]);
            $exfld_title = cot_extrafield_title($exfld, 'contact_');

            $t->assign(array(
                'CONTACT_FORM_' . $uname => $exfld_val,
                'CONTACT_FORM_' . $uname . '_TITLE' => $exfld_title,
                'CONTACT_FORM_EXTRAFLD' => $exfld_val,
                'CONTACT_FORM_EXTRAFLD_TITLE' => $exfld_title
            ));
            $t->parse('MAIN.FORM.EXTRAFLD');
        }
    }
	if (cot::$usr['id'] == 0 && !empty($cot_captcha))
	{

		$t->assign(array(
			'CONTACT_FORM_VERIFY_IMG' => cot_captcha_generate(),
			'CONTACT_FORM_VERIFY' => cot_inputbox('text', 'rverify', '', 'id="rverify" size="20"')
		));
		$t->parse('MAIN.FORM.CAPTCHA');
	}
	$t->parse('MAIN.FORM');
}
