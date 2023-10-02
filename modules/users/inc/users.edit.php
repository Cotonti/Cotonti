<?php
/**
 * Edit User Profile
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('auth');

$y = cot_import('y','P','TXT');
$id = (int) cot_import('id','G','INT');
$s = cot_import('s','G','ALP',13);
$w = cot_import('w','G','ALP',4);
$d = cot_import('d','G','INT');
$f = cot_import('f','G','TXT');
$g = cot_import('g','G','INT');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('users', 'a');
cot_block(Cot::$usr['isadmin']);
require_once cot_langfile('users', 'module');

/* === Hook === */
foreach (cot_getextplugins('users.edit.first') as $pl) {
	include $pl;
}
/* ===== */

cot_die(empty($id), true);

$sql = Cot::$db->query("SELECT * FROM $db_users WHERE user_id = ?", $id);
cot_die($sql->rowCount()==0, true);
$urr = $sql->fetch();

$sql1 = Cot::$db->query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid=$id and gru_groupid=".COT_GROUP_SUPERADMINS);
$sys['edited_istopadmin'] = ($sql1->rowCount()>0) ? TRUE : FALSE;
$sys['user_istopadmin'] = cot_auth('admin', 'a', 'A');
$sys['protecttopadmin'] = $sys['edited_istopadmin'] && !$sys['user_istopadmin'];

if ($sys['protecttopadmin'])
{
	cot_die_message(930, TRUE);
}

if ($a == 'update')
{
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('users.edit.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$ruserdelete = cot_import('ruserdelete','P','BOL');
	if ($ruserdelete)
	{
		Cot::$db->delete($db_users, "user_id=$id");
		Cot::$db->delete($db_groups_users, "gru_userid=$id");

		foreach($cot_extrafields[$db_users] as $exfld)
		{
			cot_extrafield_unlinkfiles($urr['user_'.$exfld['field_name']], $exfld);
		}

		if (cot_module_active('pfs') && cot_import('ruserdelpfs','P','BOL'))
		{
			require_once cot_incfile('pfs', 'module');
			cot_pfs_deleteall($id);
		}

		/* === Hook === */
		foreach (cot_getextplugins('users.edit.update.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_log("Deleted user #".$id, 'users', 'delete', 'done');
		cot_redirect(cot_url('message', "msg=109&rc=200&id=".$id, '', true));
	}

	$ruser['user_name'] = cot_import('rusername','P','TXT');
	$ruser['user_maingrp'] = cot_import('rusermaingrp','P','INT');
	$ruser['user_banexpire'] = cot_import('ruserbanexpire','P','INT');
	$ruser['user_country'] = cot_import('rusercountry','P','ALP');
	$ruser['user_text'] = cot_import('rusertext','P','HTM');
    $rtheme = cot_import('rusertheme','P','TXT');
    $rtheme = !empty($rtheme) ? explode(':', $rtheme) : [];
	$ruser['user_theme'] = $rtheme[0];
	$ruser['user_scheme'] = $rtheme[1];
	$ruser['user_email'] = cot_import('ruseremail','P','TXT');
	$ruser['user_hideemail'] = cot_import('ruserhideemail','P','INT');
	$ruser['user_lang'] = cot_import('ruserlang','P','ALP');
	$ruser['user_gender'] = cot_import('rusergender','P','TXT');

	$ruser['user_birthdate'] = cot_import_date('ruserbirthdate', false);
	if (!is_null($ruser['user_birthdate']) && $ruser['user_birthdate'] > $sys['now'])
	{
		cot_error('pro_invalidbirthdate', 'ruserbirthdate');
	}

	$ruser['user_timezone'] = cot_import('rusertimezone','P','TXT');
	$rusernewpass = (string) cot_import('rusernewpass','P','NOC', 32);

	// Extra fields
	if (!empty(Cot::$extrafields[Cot::$db->users])) {
		foreach (Cot::$extrafields[Cot::$db->users] as $exfld) {
			$ruser['user_' . $exfld['field_name']] = cot_import_extrafields('ruser' . $exfld['field_name'], $exfld, 'P',
				$urr['user_' . $exfld['field_name']], 'user_');
		}
	}

	$rusergroupsms = cot_import('rusergroupsms', 'P', 'ARR');

	if (mb_strlen($ruser['user_name']) < 2 || mb_strpos($ruser['user_name'], ',') !== false || mb_strpos($ruser['user_name'], "'") !== false)
	{
		cot_error('aut_usernametooshort', 'rusername');
	}
	if ($ruser['user_name'] != $urr['user_name'] && Cot::$db->query("SELECT COUNT(*) FROM ".Cot::$db->users." WHERE user_name = ?",
            array($ruser['user_name']))->fetchColumn() > 0)
	{
		cot_error('aut_usernamealreadyindb', 'rusername');
	}
	if (!cot_check_email($ruser['user_email']))
	{
		cot_error('aut_emailtooshort', 'ruseremail');
	}
	if ($ruser['user_email'] != $urr['user_email'] && Cot::$db->query("SELECT COUNT(*) FROM ".Cot::$db->users." WHERE user_email = ?",
            array($ruser['user_email']))->fetchColumn() > 0)
	{
		cot_error('aut_emailalreadyindb', 'ruseremail');
	}
	if (!empty($rusernewpass) && mb_strlen($rusernewpass) < 4)
	{
		cot_error('aut_passwordtooshort', 'rusernewpass');
	}

	if (!cot_error_found())
	{
		if (!empty($rusernewpass))
		{
			$ruser['user_passsalt'] = cot_unique(16);
			$ruser['user_passfunc'] = empty($cfg['hashfunc']) ? 'sha256' : $cfg['hashfunc'];
			$ruser['user_password'] = cot_hash($rusernewpass, $ruser['user_passsalt'], $ruser['user_passfunc']);
		}

		$ruser['user_name'] = ($ruser['user_name']=='') ? $urr['user_name'] : $ruser['user_name'];

		if(is_null($ruser['user_birthdate'])) 
		{
			if(isset($_POST['ruserbirthdate']))
			{
				$ruser['user_birthdate'] = 'NULL';
			} else {
				unset($ruser['user_birthdate']);	
			}
            
		} else {
			$ruser['user_birthdate'] = cot_stamp2date($ruser['user_birthdate']);
		}

		if ($ruser['user_name'] != $urr['user_name']) {
			$newname = $ruser['user_name'];
			$oldname = $urr['user_name'];
            // Todo move code below to related extensions
			if (cot_module_active('forums'))
			{
				require_once cot_incfile('forums', 'module');
				$db->update($db_forum_topics, array('ft_lastpostername' => $newname), 'ft_lastpostername = ?', array($oldname));
				$db->update($db_forum_topics, array('ft_firstpostername' => $newname), 'ft_firstpostername = ?', array($oldname));
				$db->update($db_forum_posts, array('fp_postername' => $newname), 'fp_postername = ?', array($oldname));
				$db->update($db_forum_stats, array('fs_lt_postername' => $newname), 'fs_lt_postername = ?', array($oldname));
                if (!empty(Cot::$cache)) {
                    Cot::$cache->db->remove('cot_sections_act', 'system');
                }
			}
			if (cot_module_active('page'))
			{
				require_once cot_incfile('page', 'module');
				$db->update($db_pages, array('page_author' => $newname), 'page_author = ?', array($oldname));
			}
			if (cot_plugin_active('comments'))
			{
				require_once cot_incfile('comments', 'plug');
				$db->update($db_com, array('com_author' => $newname), 'com_author = ?', array($oldname));
			}
			if (cot_module_active('pm'))
			{
				require_once cot_incfile('pm', 'module');
				$db->update($db_pm, array('pm_fromuser' => $newname), 'pm_fromuser = ?', array($oldname));
			}
			if (cot_plugin_active('whosonline'))
			{
				$db->update($db_online, array('online_name' => $newname), 'online_name = ?', array($oldname));
			}
		}

		$ruser['user_auth'] = '';

		$sql = $db->update($db_users, $ruser, 'user_id='.$id);
		cot_extrafield_movefiles();

		$ruser['user_maingrp'] = ($ruser['user_maingrp'] < COT_GROUP_MEMBERS && $id==1) ? COT_GROUP_SUPERADMINS : $ruser['user_maingrp'];

		if (!$rusergroupsms[$ruser['user_maingrp']])
		{
			$rusergroupsms[$ruser['user_maingrp']] = 1;
		}
		$db->update($db_users, array('user_maingrp' => $ruser['user_maingrp']), 'user_id='.$id);

		foreach($cot_groups as $k => $i)
		{
			if (isset($rusergroupsms[$k]))
			{
				if ($db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid=$id AND gru_groupid=$k")->rowCount() == 0
					&& !($id == 1 && in_array($k, array(COT_GROUP_BANNED, COT_GROUP_INACTIVE))))
				{
					$db->insert($db_groups_users, array('gru_userid' => (int)$id, 'gru_groupid' => (int)$k));
				}
			}
			else
			{
				$db->delete($db_groups_users, "gru_userid=$id AND gru_groupid=$k");
			}
		}

		if ($ruser['user_maingrp'] == COT_GROUP_MEMBERS && $urr['user_maingrp'] == COT_GROUP_INACTIVE) {
			$rsubject = Cot::$L['useed_accountactivated'];
			$rbody = Cot::$L['Hi'] . " " . $urr['user_name'].",\n\n";
			$rbody .= Cot::$L['useed_email'];
			$rbody .= "\n\n" . Cot::$L['aut_contactadmin'];
			cot_mail($urr['user_email'], $rsubject, $rbody);
		}

		/* === Hook === */
		foreach (cot_getextplugins('users.edit.update.done') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_auth_clear($id);
		cot_log("Edited user #".$id, 'users', 'edit', 'done');
		cot_message('User_data_updated');
		cot_redirect(cot_url('users', "m=edit&id=".$id, '', true));
	}
	else
	{
		cot_redirect(cot_url('users', "m=edit&id=$id", '', true));
	}
}

$sql = $db->query("SELECT * FROM $db_users WHERE user_id=$id LIMIT 1");
$urr = $sql->fetch();

$title_params = array(
	'EDIT' => $L['Edit'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = cot_title('{EDIT} - {NAME}', $title_params);
if(!isset($out['head'])) $out['head'] = '';
$out['head'] .= Cot::$R['code_noindex'];

$mskin = cot_tplfile(array('users', 'edit', $usr['maingrp']), 'module');

/* === Hook === */
foreach (cot_getextplugins('users.edit.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate($mskin);

require_once cot_incfile('forms');

$protected = $sys['protecttopadmin'] ? array('disabled' => 'disabled') : array();

$editor_class = $cfg['users']['usertextimg'] ? 'minieditor' : '';

$delete_pfs = cot_module_active('pfs') ? cot_checkbox(false, 'ruserdelpfs', $L['PFS']) : '';

$t->assign(array(
	'USERS_EDIT_TITLE' => cot_breadcrumbs(array(array(cot_url('users'), $L['Users']), array(cot_url('users', 'm=details&id='.$urr['user_id'].'&u='.$urr['user_name']), $urr['user_name']), array(cot_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit'])), $cfg['homebreadcrumb']),
	'USERS_EDIT_DETAILSLINK' => cot_url('users', 'm=details&id='.$urr['user_id']),
	'USERS_EDIT_EDITLINK' => cot_url('users', 'm=edit&id='.$urr['user_id']),
	'USERS_EDIT_SUBTITLE' => $L['useed_subtitle'],
	'USERS_EDIT_SEND' => cot_url('users', 'm=edit&a=update&'.cot_xg().'&id='.$urr['user_id']),
	'USERS_EDIT_ID' => $urr['user_id'],
	'USERS_EDIT_NAME' => cot_inputbox('text', 'rusername', $urr['user_name'], array('size' => 32, 'maxlength' => 100) + $protected),
	//'USERS_EDIT_ACTIVE' => $user_form_active,   // It seems it is obsolete code
	//'USERS_EDIT_BANNED' => $user_form_banned,   // It seems it is obsolete code
	'USERS_EDIT_THEME' => cot_selectbox_theme($urr['user_theme'], $urr['user_scheme'], 'rusertheme'),
	'USERS_EDIT_LANG' => cot_selectbox_lang($urr['user_lang'], 'ruserlang'),
	'USERS_EDIT_NEWPASS' => cot_inputbox('password', 'rusernewpass', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off') + $protected),
	'USERS_EDIT_MAINGRP' => cot_build_group($urr['user_maingrp']),
	'USERS_EDIT_GROUPS' => cot_build_groupsms($urr['user_id'], $usr['isadmin'], $urr['user_maingrp']),
	'USERS_EDIT_COUNTRY' => cot_selectbox_countries($urr['user_country'], 'rusercountry'),
	'USERS_EDIT_EMAIL' => cot_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)),
	'USERS_EDIT_HIDEEMAIL' => cot_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array($L['Yes'], $L['No'])),
	'USERS_EDIT_TEXT' => cot_textarea('rusertext', $urr['user_text'], 4, 56, array('class' => $editor_class)),
	'USERS_EDIT_GENDER' => cot_selectbox_gender($urr['user_gender'], 'rusergender'),
	'USERS_EDIT_BIRTHDATE' => cot_selectbox_date(cot_date2stamp($urr['user_birthdate']), 'short', 'ruserbirthdate', cot_date('Y', $sys['now']), cot_date('Y', $sys['now']) - 100, false),
	'USERS_EDIT_TIMEZONE' => cot_selectbox_timezone($urr['user_timezone'], 'rusertimezone'),
	'USERS_EDIT_REGDATE' => cot_date('datetime_medium', $urr['user_regdate']),
	'USERS_EDIT_REGDATE_STAMP' => $urr['user_regdate'],
	'USERS_EDIT_LASTLOG' => cot_date('datetime_medium', $urr['user_lastlog']),
	'USERS_EDIT_LASTLOG_STAMP' => $urr['user_lastlog'],
	'USERS_EDIT_LOGCOUNT' => $urr['user_logcount'],
	'USERS_EDIT_LASTIP' => cot_build_ipsearch($urr['user_lastip']),
	'USERS_EDIT_DELETE' => (Cot::$sys['user_istopadmin']) ? cot_radiobox(0, 'ruserdelete', array(1, 0), array(Cot::$L['Yes'],
            Cot::$L['No'])) . $delete_pfs : Cot::$L['na'],
));

// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->users])) {
    foreach (Cot::$extrafields[Cot::$db->users] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $exfld_val = cot_build_extrafields('ruser' . $exfld['field_name'], $exfld, $urr['user_' . $exfld['field_name']]);
        $exfld_title = cot_extrafield_title($exfld, 'user_');

        $t->assign(array(
            'USERS_EDIT_' . $uname => $exfld_val,
            'USERS_EDIT_' . $uname . '_TITLE' => $exfld_title,
            'USERS_EDIT_EXTRAFLD' => $exfld_val,
            'USERS_EDIT_EXTRAFLD_TITLE' => $exfld_title
        ));
        $t->parse('MAIN.EXTRAFLD');
    }
}

// Error and message reporting
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('users.edit.tags') as $pl)
{
	include $pl;
}
/* ===== */


$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';
