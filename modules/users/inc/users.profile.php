<?php

/**
 * User Profile
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('uploads');

@clearstatcache();

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('users', 'a');
cot_block(Cot::$usr['auth_write']);
require_once cot_langfile('users', 'module');

/* === Hook === */
foreach (cot_getextplugins('users.profile.first') as $pl) {
	include $pl;
}
/* ===== */

$id = cot_import('id','G','TXT');
$a = cot_import('a','G','ALP');

$sql = Cot::$db->query("SELECT * FROM " . Cot::$db->users . " WHERE user_id='" . Cot::$usr['id']."' LIMIT 1");
cot_die($sql->rowCount()==0);
$urr = $sql->fetch();

if ($a == 'update') {
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('users.profile.update.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$ruser['user_text'] = cot_import('rusertext','P','HTM', Cot::$cfg['users']['usertextmax']);
	$ruser['user_country'] = cot_import('rusercountry','P','ALP');
    $rtheme = cot_import('rusertheme','P','TXT');
    $rtheme = !empty($rtheme) ? explode(':', $rtheme) : [];
	$ruser['user_theme'] = isset($rtheme[0]) ? $rtheme[0] : null;
	$ruser['user_scheme'] = isset($rtheme[1]) ? $rtheme[1] : null;
	$ruser['user_lang'] = cot_import('ruserlang','P','ALP');
	$ruser['user_gender'] = cot_import('rusergender','P','ALP');
	$ruser['user_timezone'] = cot_import('rusertimezone','P','TXT');
	$ruser['user_hideemail'] = cot_import('ruserhideemail','P','BOL');

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->users])) {
        foreach (Cot::$extrafields[Cot::$db->users] as $exfld) {
            $ruser['user_' . $exfld['field_name']] = cot_import_extrafields('ruser' . $exfld['field_name'], $exfld, 'P',
                $urr['user_' . $exfld['field_name']], 'user_');
        }
    }
	$ruser['user_birthdate'] = cot_import_date('ruserbirthdate', false);
	if (!is_null($ruser['user_birthdate']) && $ruser['user_birthdate'] > Cot::$sys['now']) {
		cot_error('pro_invalidbirthdate', 'ruserbirthdate');
	}

	$roldpass  = (string) cot_import('roldpass','P','NOC');
	$rnewpass1 = (string) cot_import('rnewpass1','P','NOC', 32);
	$rnewpass2 = (string) cot_import('rnewpass2','P','NOC', 32);
	$rmailpass = (string) cot_import('rmailpass','P','NOC');
	$ruseremail = cot_import('ruseremail','P','TXT');

	//$ruser['user_scheme'] = ($ruser['user_theme'] != $urr['user_theme']) ? $ruser['user_theme'] : $ruser['user_scheme'];

	if (!empty($rnewpass1) && !empty($rnewpass2) && !empty($roldpass)) {
		if ($rnewpass1 != $rnewpass2) cot_error('pro_passdiffer', 'rnewpass2');
		if (mb_strlen($rnewpass1) < 4) cot_error('pro_passtoshort', 'rnewpass1');
		if (cot_hash($roldpass, $urr['user_passsalt'], $urr['user_passfunc']) != $urr['user_password']) cot_error('pro_wrongpass', 'roldpass');

		if (!empty($ruseremail) && !empty($rmailpass) && Cot::$cfg['users']['useremailchange'] && $ruseremail != $urr['user_email']) {
			cot_error('pro_emailandpass', 'ruseremail');
		}

        if (!cot_error_found()) {
			$ruserpass = array();
			$ruserpass['user_passsalt'] = cot_unique(16);
			$ruserpass['user_passfunc'] = empty(Cot::$cfg['hashfunc']) ? 'sha256' : Cot::$cfg['hashfunc'];
			$ruserpass['user_password'] = cot_hash($rnewpass1, $ruserpass['user_passsalt'], $ruserpass['user_passfunc']);
			Cot::$db->update(Cot::$db->users, $ruserpass, "user_id=" . Cot::$usr['id']);
			unset($ruserpass);
			cot_message('Password_updated');
		}
	}

	if (
        !empty($ruseremail) &&
        (!empty($rmailpass) || Cot::$cfg['users']['user_email_noprotection']) &&
        Cot::$cfg['users']['useremailchange'] &&
        $ruseremail != $urr['user_email']
    ) {
		$sqltmp = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->users . " WHERE user_email='" .
            Cot::$db->prep($ruseremail)."'");
		$res = $sqltmp->fetchColumn();

		if (!Cot::$cfg['users']['user_email_noprotection']) {
			$rmailpass = cot_hash($rmailpass, $urr['user_passsalt'], $urr['user_passfunc']);
			if ($rmailpass != $urr['user_password']) cot_error('pro_wrongpass', 'rmailpass');
		}

		if (!cot_check_email($ruseremail)) {
            cot_error('aut_emailtooshort', 'ruseremail');
        }
		if ($res > 0) cot_error('aut_emailalreadyindb', 'ruseremail');

		if (!cot_error_found()) {
			if (!Cot::$cfg['users']['user_email_noprotection']) {
				$validationkey = md5(microtime());
                Cot::$db->update(Cot::$db->users, array(
                    'user_email' => $ruseremail,
                    'user_lostpass' => $validationkey,
                    'user_maingrp' => '0',
                    'user_sid' => $urr['user_maingrp']),
                    "user_id='" . Cot::$usr['id']."'"
                );

				$rsubject = Cot::$L['aut_mailnoticetitle'];
				$ractivate = Cot::$cfg['mainurl'] . '/' .
                    cot_url('users', 'm=register&a=validate&v='.$validationkey, '', true);
				$rbody = sprintf(Cot::$L['aut_emailchange'], Cot::$usr['name'], $ractivate);
				$rbody .= "\n\n" . Cot::$L['aut_contactadmin'];
				cot_mail($ruseremail, $rsubject, $rbody);

				if (cot_import(Cot::$sys['site_id'], 'COOKIE', 'TXT')) {
					cot_setcookie(Cot::$sys['site_id'], '', time() - 63072000, Cot::$cfg['cookiepath'],
                        Cot::$cfg['cookiedomain'], Cot::$sys['secure'], true);
				}

				if (!empty($_SESSION[Cot::$sys['site_id']])) {
					session_unset();
					session_destroy();
				}
				if (cot_plugin_active('whosonline')) {
                    Cot::$db->delete(Cot::$db->online, "online_ip='" . Cot::$usr['ip'] . "'");
				}
				cot_redirect(cot_url('message', 'msg=102', '', true));

			} else {
                Cot::$db->update(Cot::$db->users, array('user_email' => $ruseremail), "user_id='" .
                    Cot::$usr['id']."'");
			}
		}
	}

	if (!cot_error_found()) {
		if (is_null($ruser['user_birthdate'])) {
			if (isset($_POST['ruserbirthdate'])) {
				$ruser['user_birthdate'] = 'NULL';
			} else {
				unset($ruser['user_birthdate']);
			}

		} else {
			$ruser['user_birthdate'] = cot_stamp2date($ruser['user_birthdate']);
		}

		$ruser['user_auth'] = '';
        Cot::$db->update(Cot::$db->users, $ruser, "user_id='" . Cot::$usr['id']."'");
		cot_extrafield_movefiles();

		/* === Hook === */
		foreach (cot_getextplugins('users.profile.update.done') as $pl) {
			include $pl;
		}
		/* ===== */
		cot_message('Profile_updated');
		cot_redirect(cot_url('users', 'm=profile', '', true));
	}
}

$sql = Cot::$db->query('SELECT * FROM ' . Cot::$db->users . " WHERE user_id='" . Cot::$usr['id'] . "' LIMIT 1");
$urr = $sql->fetch();

Cot::$out['subtitle'] = Cot::$L['Profile'];
Cot::$out['head'] .= Cot::$R['code_noindex'];

$mskin = cot_tplfile(array('users', 'profile'), 'module');

/* === Hook === */
foreach (cot_getextplugins('users.profile.main') as $pl) {
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate($mskin);

require_once cot_incfile('forms');

$protected = !Cot::$cfg['users']['useremailchange'] ? array('disabled' => 'disabled') : array();
$profile_form_email = cot_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)
	+ $protected);

$editor_class = Cot::$cfg['users']['usertextimg'] ? 'minieditor' : '';

$t->assign(array(
	'USERS_PROFILE_TITLE' => cot_rc_link(cot_url('users', 'm=profile'), Cot::$L['pro_title']),
	'USERS_PROFILE_SUBTITLE' => Cot::$L['pro_subtitle'],
	'USERS_PROFILE_DETAILSLINK' => cot_url('users', 'm=details&id='.$urr['user_id']),
	'USERS_PROFILE_EDITLINK' => cot_url('users', 'm=edit&id='.$urr['user_id']),
	'USERS_PROFILE_FORM_SEND' => cot_url('users', "m=profile&a=update&".cot_xg()),
	'USERS_PROFILE_ID' => $urr['user_id'],
	'USERS_PROFILE_NAME' => htmlspecialchars($urr['user_name']),
	'USERS_PROFILE_MAINGRP' => cot_build_group($urr['user_maingrp']),
	'USERS_PROFILE_GROUPS' => cot_build_groupsms($urr['user_id'], FALSE, $urr['user_maingrp']),
	'USERS_PROFILE_COUNTRY' => cot_selectbox_countries($urr['user_country'], 'rusercountry'),
	'USERS_PROFILE_TEXT' => cot_textarea('rusertext', $urr['user_text'], 8, 56, array('class' => $editor_class)),
	'USERS_PROFILE_EMAIL' => $profile_form_email,
	'USERS_PROFILE_EMAILPASS' => cot_inputbox('password', 'rmailpass', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off')),
	'USERS_PROFILE_HIDEEMAIL' => cot_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array(Cot::$L['Yes'], Cot::$L['No'])),
	'USERS_PROFILE_THEME' => cot_selectbox_theme($urr['user_theme'], $urr['user_scheme'], 'rusertheme'),
	'USERS_PROFILE_LANG' => cot_selectbox_lang($urr['user_lang'], 'ruserlang'),
	'USERS_PROFILE_GENDER' => cot_selectbox_gender($urr['user_gender'] ,'rusergender'),
	'USERS_PROFILE_BIRTHDATE' => cot_selectbox_date(cot_date2stamp($urr['user_birthdate']), 'short', 'ruserbirthdate', cot_date('Y', Cot::$sys['now']), cot_date('Y', Cot::$sys['now']) - 100, false),
	'USERS_PROFILE_TIMEZONE' => cot_selectbox_timezone($urr['user_timezone'], 'rusertimezone'),
	'USERS_PROFILE_REGDATE' => cot_date('datetime_medium', $urr['user_regdate']),
	'USERS_PROFILE_REGDATE_STAMP' => $urr['user_regdate'],
	'USERS_PROFILE_LASTLOG' => cot_date('datetime_medium', $urr['user_lastlog']),
	'USERS_PROFILE_LASTLOG_STAMP' => $urr['user_lastlog'],
	'USERS_PROFILE_LOGCOUNT' => $urr['user_logcount'],
	'USERS_PROFILE_ADMINRIGHTS' => '',
	'USERS_PROFILE_OLDPASS' => cot_inputbox('password', 'roldpass', '', array('size' => 12, 'maxlength' => 32)),
	'USERS_PROFILE_NEWPASS1' => cot_inputbox('password', 'rnewpass1', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off')),
	'USERS_PROFILE_NEWPASS2' => cot_inputbox('password', 'rnewpass2', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off')),
));

// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->users])) {
    foreach (Cot::$extrafields[Cot::$db->users] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $exfld_val = cot_build_extrafields('ruser'.$exfld['field_name'], $exfld, $urr['user_'.$exfld['field_name']]);
        $exfld_title = cot_extrafield_title($exfld, 'user_');

        $t->assign(array(
            'USERS_PROFILE_' . $uname => $exfld_val,
            'USERS_PROFILE_' . $uname . '_TITLE' => $exfld_title,
            'USERS_PROFILE_EXTRAFLD' => $exfld_val,
            'USERS_PROFILE_EXTRAFLD_TITLE' => $exfld_title
        ));
        $t->parse('MAIN.EXTRAFLD');
    }
}

/* === Hook === */
foreach (cot_getextplugins('users.profile.tags') as $pl) {
	include $pl;
}
/* ===== */

// Error handling
cot_display_messages($t);

if (Cot::$cfg['users']['useremailchange']) {
	if (!Cot::$cfg['users']['user_email_noprotection']) {
		$t->parse('MAIN.USERS_PROFILE_EMAILCHANGE.USERS_PROFILE_EMAILPROTECTION');
	}
	$t->parse('MAIN.USERS_PROFILE_EMAILCHANGE');
}

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
