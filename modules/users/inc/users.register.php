<?php

/**
 * User Registration Script
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('auth');

$v = cot_import('v','G','ALP');
$y = cot_import('y','G','INT');
$token = cot_import('token', 'G', 'ALP');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('users', 'a');

if (Cot::$cfg['users']['disablereg'] && !Cot::$usr['isadmin'])
{
	cot_die_message(117, TRUE);
}

/* === Hook === */
foreach (cot_getextplugins('users.register.first') as $pl)
{
	include $pl;
}
/* ===== */

cot_block(Cot::$usr['id'] == 0 || Cot::$usr['isadmin']);

$ruser = array(
    'user_name' => '',
    'user_email' => '',
    'user_country' => '',
    'user_timezone' => '',
    'user_gender' => '',

);

if ($a == 'add') {
	cot_shield_protect();

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.register.add.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$ruser['user_name'] = cot_import('rusername','P','TXT', 100, TRUE);
	$ruser['user_email'] = cot_import('ruseremail','P','TXT',64, TRUE);
	$rpassword1 = (string) cot_import('rpassword1','P','NOC',32);
	$rpassword2 = (string) cot_import('rpassword2','P','NOC',32);
	$ruser['user_country'] = cot_import('rcountry','P','TXT');
	$ruser['user_timezone'] = cot_import('rusertimezone','P','TXT');
	$ruser['user_timezone'] = (!$ruser['user_timezone']) ? Cot::$cfg['defaulttimezone'] : $ruser['user_timezone'];
	$ruser['user_gender'] = cot_import('rusergender','P','TXT');
	$ruser['user_email'] = mb_strtolower($ruser['user_email']);

	// Extra fields
	if (!empty(Cot::$extrafields[Cot::$db->users])) {
		foreach (Cot::$extrafields[Cot::$db->users] as $exfld) {
			$ruser['user_' . $exfld['field_name']] = cot_import_extrafields('ruser' . $exfld['field_name'], $exfld, 'P',
				'', 'user_');
		}
	}
	$ruser['user_birthdate'] = cot_import_date('ruserbirthdate', false);
	if (!is_null($ruser['user_birthdate']) && $ruser['user_birthdate'] > Cot::$sys['now']) {
		cot_error('pro_invalidbirthdate', 'ruserbirthdate');
	}

	$user_exists = (bool)Cot::$db->query("SELECT user_id FROM ".Cot::$db->users." WHERE user_name = ? LIMIT 1",
        array($ruser['user_name']))->fetch();
	$email_exists = (bool)Cot::$db->query("SELECT user_id FROM ".Cot::$db->users." WHERE user_email = ? LIMIT 1",
        array($ruser['user_email']))->fetch();

	if (preg_match('/&#\d+;/', $ruser['user_name']) || preg_match('/[<>#\'"\/]/', $ruser['user_name'])) {
        cot_error('aut_invalidloginchars', 'rusername');
    }
	if (mb_strlen($ruser['user_name']) < 2) {
        cot_error('aut_usernametooshort', 'rusername');
    }
	if (mb_strlen($rpassword1) < 4) {
        cot_error('aut_passwordtooshort', 'rpassword1');
    }
	if (!cot_check_email($ruser['user_email']))	{
        cot_error('aut_emailtooshort', 'ruseremail');
    }
	if ($user_exists) {
        cot_error('aut_usernamealreadyindb', 'rusername');
    }
	if ($email_exists && !Cot::$cfg['useremailduplicate']) {
        cot_error('aut_emailalreadyindb', 'ruseremail');
    }
	if ($rpassword1 != $rpassword2) {
        cot_error('aut_passwordmismatch', 'rpassword2');
    }

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.register.add.validate') as $pl) {
		include $pl;
	}
	/* ===== */

	if (!cot_error_found()) {
		$ruser['user_password'] = $rpassword1;
		$userid = cot_add_user($ruser);

		$authorize = false;
        if (Cot::$db->countRows(Cot::$db->users) == 1) {
            $authorize = true;
        } elseif (
            (Cot::$cfg['users']['regnoactivation'] || Cot::$cfg['users']['inactive_login'])
            && Cot::$cfg['users']['register_auto_login']
        ) {
            $authorize = true;
        }

		/* === Hook for the plugins === */
		foreach (cot_getextplugins('users.register.add.done') as $pl)  {
			include $pl;
		}
		/* ===== */

		if($authorize) cot_user_authorize($userid);

		if (Cot::$cfg['users']['regnoactivation'] || Cot::$db->countRows(Cot::$db->users) == 1)
		{
			cot_redirect(cot_url('message', 'msg=106', '', true));
		}
		elseif (Cot::$cfg['users']['regrequireadmin'])
		{
			cot_redirect(cot_url('message', 'msg=118', '', true));
		}
		else
		{
			cot_redirect(cot_url('message', 'msg=105', '', true));
		}
	}
	else
	{
		cot_redirect(cot_url('users', 'm=register', '', true));
	}

} elseif ($a == 'validate' && mb_strlen($v) == 32) {
	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.register.validate.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	cot_shield_protect();
	$sql = Cot::$db->query("SELECT * FROM ".Cot::$db->users." WHERE user_lostpass='$v' AND (user_maingrp=2 OR user_maingrp='-1') LIMIT 1");

	if ($row = $sql->fetch())
	{
		if ($row['user_maingrp'] == COT_GROUP_INACTIVE)
		{
			if ($y == 1)
			{
				$sql = Cot::$db->update(Cot::$db->users, array('user_maingrp' => COT_GROUP_MEMBERS),
                    "user_id='".$row['user_id']."' AND user_lostpass='$v'");
				$sql = Cot::$db->update(Cot::$db->groups_users, array('gru_groupid' => COT_GROUP_MEMBERS),
                    "gru_groupid=2 AND gru_userid='".$row['user_id']."'");

                $row['user_maingrp'] = COT_GROUP_MEMBERS;

				/* === Hook for the plugins === */
				foreach (cot_getextplugins('users.register.validate.done') as $pl)
				{
					include $pl;
				}
				/* ===== */

				cot_auth_clear($row['user_id']);
                if(Cot::$usr['id'] == 0 && Cot::$cfg['users']['register_auto_login']) cot_user_authorize($row);
                cot_redirect(cot_url('message', 'msg=106', '', true));
			}
			elseif ($y == 0)
			{
				foreach(Cot::$extrafields[Cot::$db->users] as $exfld)
				{
					cot_extrafield_unlinkfiles($row['user_'.$exfld['field_name']], $exfld);
				}

				$sql = Cot::$db->delete(Cot::$db->users, "user_id=".(int)$row['user_id']);
				$sql = Cot::$db->delete(Cot::$db->groups_users, "gru_userid='".$row['user_id']."'");

				/* === Hook for the plugins === */
				foreach (cot_getextplugins('users.register.validate.rejected') as $pl)
				{
					include $pl;
				}
				/* ===== */

				cot_redirect(cot_url('message', 'msg=109', '', true));
			}
		}
		elseif ($row['user_maingrp'] == -1)
		{
			$sql = Cot::$db->update(Cot::$db->users, array('user_maingrp' => $row['user_sid']),
                "user_id='".$row['user_id']."' AND user_lostpass='$v'");
			cot_redirect(cot_url('message', 'msg=106', '', true));
		}
	}
	else
	{
        Cot::$env['status'] = '403 Forbidden';
		cot_shield_update(7, "Account validation");
		cot_log("Wrong validation URL", 'sec', 'user', 'error');
		cot_redirect(cot_url('message', 'msg=157', '', true));
	}
}

$mskin = cot_tplfile('users.register', 'module');

/* === Hook === */
foreach (cot_getextplugins('users.register.main') as $pl)
{
	include $pl;
}
/* ===== */

$out['subtitle'] = Cot::$L['aut_registertitle'];
if (!isset($out['head'])) $out['head'] = '';
$out['head'] .= Cot::$R['code_noindex'];
require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate($mskin);

require_once cot_incfile('forms');

$t->assign(array(
	'USERS_REGISTER_TITLE' => Cot::$L['aut_registertitle'],
	'USERS_REGISTER_SUBTITLE' => Cot::$L['aut_registersubtitle'],
	//'USERS_REGISTER_ADMINEMAIL' => $cot_adminemail, // Obsolete?
	'USERS_REGISTER_SEND' => cot_url('users', 'm=register&a=add'),
	'USERS_REGISTER_USER' => cot_inputbox('text', 'rusername', $ruser['user_name'], array('size' => 24, 'maxlength' => 100)),
	'USERS_REGISTER_EMAIL' => cot_inputbox('text', 'ruseremail', $ruser['user_email'], array('size' => 24, 'maxlength' => 64)),
	'USERS_REGISTER_PASSWORD' => cot_inputbox('password', 'rpassword1', '', array('size' => 12, 'maxlength' => 32)),
	'USERS_REGISTER_PASSWORDREPEAT' => cot_inputbox('password', 'rpassword2', '', array('size' => 12, 'maxlength' => 32)),
	'USERS_REGISTER_COUNTRY' => cot_selectbox_countries($ruser['user_country'], 'rcountry'),
	'USERS_REGISTER_TIMEZONE' => cot_selectbox_timezone($ruser['user_timezone'], 'rusertimezone'),
	'USERS_REGISTER_GENDER' => cot_selectbox_gender($ruser['user_gender'],'rusergender'),
	'USERS_REGISTER_BIRTHDATE' => cot_selectbox_date(0, 'short', 'ruserbirthdate', cot_date('Y', $sys['now']), cot_date('Y', $sys['now']) - 100, false),
));

// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->users])) {
    foreach (Cot::$extrafields[Cot::$db->users] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $val = isset($ruser['user_'.$exfld['field_name']]) ? $ruser['user_'.$exfld['field_name']] : null;
        $exfld_val = cot_build_extrafields('ruser'.$exfld['field_name'],  $exfld, $val);
        $exfld_title = cot_extrafield_title($exfld, 'user_');

        $t->assign(array(
            'USERS_REGISTER_' . $uname => $exfld_val,
            'USERS_REGISTER_' . $uname . '_TITLE' => $exfld_title,
            'USERS_REGISTER_EXTRAFLD' => $exfld_val,
            'USERS_REGISTER_EXTRAFLD_TITLE' => $exfld_title
        ));
        $t->parse('MAIN.EXTRAFLD');
    }
}

/* === Hook === */
foreach (cot_getextplugins('users.register.tags') as $pl)
{
	include $pl;
}
/* ===== */

// Error and message handling
cot_display_messages($t);

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
