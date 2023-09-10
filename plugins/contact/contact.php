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

$tplfile = cot_import('tpl', 'G', 'TXT');
$mskin = cot_tplfile(array('contact', $tplfile), 'plug');
$t = new XTemplate($mskin);
$rtext = cot_import('rtext', 'P', 'TXT');

$sent = false;
$rcontact = array(
    'contact_text' => '',
    'contact_author' => '',
    'contact_email' => '',
    'contact_subject' => '',
);
if (!empty($rtext)) {
	//Import the variables
	$rcontact['contact_text'] = $rtext;
	$rcontact['contact_author'] = cot_import('ruser', 'P', 'TXT');
	$rcontact['contact_email'] = cot_import('remail', 'P', 'TXT');
	$rcontact['contact_subject'] = cot_import('rsubject', 'P', 'TXT');

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->contact])) {
        foreach (Cot::$extrafields[Cot::$db->contact] as $exfld) {
            $rcontact['contact_' . $exfld['field_name']] = cot_import_extrafields('rcontact' . $exfld['field_name'],
                $exfld, 'P', '', 'contact_');
        }
    }

	if (Cot::$usr['id'] == 0 && !empty($cot_captcha)) {
		$rverify = cot_import('rverify', 'P', 'TXT');
		if (!cot_captcha_validate($rverify)) {
			cot_error('captcha_verification_failed', 'rverify');
		}
	}


	if ($rcontact['contact_author'] == '') {
		cot_error('contact_noname', 'ruser');
	}
	if (!cot_check_email($rcontact['contact_email'])) {
		cot_error('contact_emailnotvalid', 'remail');
	}
	if (mb_strlen($rcontact['contact_text']) < Cot::$cfg['plugin']['contact']['minchars']) {
		cot_error('contact_entrytooshort', 'rtext');
	}

	if (!cot_error_found()) {
		$rcontact['contact_authorid'] = (int) Cot::$usr['id'];
		$rcontact['contact_date'] = (int) Cot::$sys['now'];
		$rcontact['contact_val'] = 0;

		if (in_array(Cot::$cfg['plugin']['contact']['save'], array('db','both'))) {
            Cot::$db->insert(Cot::$db->contact, $rcontact);
		}

		$semail = (!empty(Cot::$cfg['plugin']['contact']['email'])) ?
            Cot::$cfg['plugin']['contact']['email'] : Cot::$cfg['adminemail'];

		if (cot_check_email($semail) && in_array(Cot::$cfg['plugin']['contact']['save'], array('email','both'))) {
			$context = array(
				'sitetitle' => Cot::$cfg["maintitle"],
				'siteurl' => Cot::$cfg['mainurl'],
				'author' => $rcontact['contact_author'],
				'email' => $rcontact['contact_email'],
				'subject' => $rcontact['contact_subject'],
				'text' => $rcontact['contact_text']
			);
            $rextras = '';
            if (!empty(Cot::$extrafields[Cot::$db->contact])) {
                foreach (Cot::$extrafields[Cot::$db->contact] as $exfld) {
                    $exfld_title = cot_extrafield_title($exfld, 'contact_');
                    $ex_body = cot_build_extrafields_data('contact', $exfld, $rcontact['contact_' . $exfld['field_name']]);
                    $rextras .= "\n" .  $exfld_title . ": " . $ex_body;

                    $context['extra' . $exfld['field_name']] = $ex_body;
                    $context['extra' . $exfld['field_name'] . '_title'] =  $exfld_title;
                    $context['extra' . $exfld['field_name'] . '_value'] = $rcontact['contact_' . $exfld['field_name']];

                }
            }
			$context['extra'] = $rextras;
			$rtextm = cot_rc(
                empty(Cot::$cfg['plugin']['contact']['template']) ?
                    Cot::$R['contact_message'] : Cot::$cfg['plugin']['contact']['template'],
                $context
            );

            cot_mail(
                [
                    'to' => $semail,
                    'from' => [$rcontact['contact_email'], $rcontact['contact_author']]
                ],
                $rcontact['contact_subject'],
                $rtextm
            );
		}
		$sent = true;
		cot_message('contact_message_sent');

		cot_extrafield_movefiles();
	}
}

Cot::$out['subtitle'] = Cot::$L['contact_title'];

cot_display_messages($t);

if (!$sent) {
	$t->assign(array(
		'CONTACT_FORM_SEND' => cot_url('plug', 'e=contact&tpl='.$tplfile),
		'CONTACT_FORM_AUTHOR' => (Cot::$usr['id'] == 0) ? cot_inputbox('text', 'ruser', $rcontact['contact_author'], 'size="24" maxlength="24"') :
            cot_inputbox('text', 'ruser', Cot::$usr['name'], 'size="24" maxlength="24" readonly="readonly"'),
		'CONTACT_FORM_EMAIL' => cot_inputbox('text', 'remail', $rcontact['contact_email'], 'size="24"'),
		'CONTACT_FORM_SUBJECT' => cot_inputbox('text', 'rsubject', $rcontact['contact_subject'], 'size="24"'),
		'CONTACT_FORM_TEXT' => cot_textarea('rtext', $rcontact['contact_text'], 8, 50, 'style="width:90%"')
	));

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->contact])) {
        foreach (Cot::$extrafields[Cot::$db->contact] as $exfld) {
            $uname = strtoupper($exfld['field_name']);
            $exfld_val = cot_build_extrafields(
                'rcontact' . $exfld['field_name'],
                $exfld,
                isset($rcontact['contact_'.$exfld['field_name']]) ? $rcontact['contact_'.$exfld['field_name']] : ''
            );
            $exfld_title = cot_extrafield_title($exfld, 'contact_');

            $t->assign([
                'CONTACT_FORM_' . $uname => $exfld_val,
                'CONTACT_FORM_' . $uname . '_TITLE' => $exfld_title,
                'CONTACT_FORM_EXTRAFLD' => $exfld_val,
                'CONTACT_FORM_EXTRAFLD_TITLE' => $exfld_title
            ]);
            $t->parse('MAIN.FORM.EXTRAFLD');
        }
    }

	if (\Cot::$usr['id'] == 0 && !empty($cot_captcha)) {
		$t->assign([
			'CONTACT_FORM_VERIFY_IMG' => cot_captcha_generate(),
			'CONTACT_FORM_VERIFY_INPUT' => cot_inputbox('text', 'rverify', '', 'id="rverify" size="20"')
		]);
		$t->parse('MAIN.FORM.CAPTCHA');
	}
	$t->parse('MAIN.FORM');
}
