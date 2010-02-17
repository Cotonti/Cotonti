<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=new
File=comedit.new
Hooks=comments.send.new
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comedit plug
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Asmo (Edited by motor2hg), Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

require_once sed_langfile('comedit', 'plug');

if(empty($error_string) && $cfg['plugin']['comedit']['mail'])
{
	$newcomm = sed_sql_insertid($sql);

	$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp=5");

	$email_title = $L['plu_comlive'] . $cfg['main_url'];
	$email_body  = $L['User'] .' ' . $usr['name'] . ', ' . $L['plu_comlive2'];
	$email_url = str_replace('&amp;amp;', '&', $url);
	$email_url = str_replace('&amp;', '&', $email_url);
	$sep = (mb_strpos($email_url, '?') !== false) ? '&' : '?';
	$email_body .= $cfg['mainurl'] . '/' . $email_url . $sep . 'comments=1#c' . $newcomm . "\n\n";

	while($adm = sed_sql_fetcharray($sql))
	{
		sed_mail($adm['user_email'], $email_title, $email_body);
	}

}

?>