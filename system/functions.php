<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/functions.php
Version=125
Updated=2008-may-26
Type=Core
Author=Neocrome
Description=Functions
[END_SED]
==================== */

/**
 * @package Seditio-N
 * @version 0.0.1
 * @copyright Partial copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

// Group constants
define('SED_GROUP_GUESTS', 1);
define('SED_GROUP_INACTIVE', 2);
define('SED_GROUP_BANNED', 3);
define('SED_GROUP_MEMBERS', 4);
define('SED_GROUP_TOPADMINS', 5);

//$cfg = array();
$out = array();
$plu = array();
$sys = array();
$usr = array();

/* ======== Pre-sets ========= */

$i = explode(' ', microtime());
$sys['starttime'] = $i[1] + $i[0];

unset ($warnings, $moremetas, $morejavascript, $error_string,  $sed_cat, $sed_smilies, $sed_acc, $sed_catacc, $sed_rights, $sed_config, $sql_config, $sed_usersonline, $sed_plugins, $sed_groups, $rsedition, $rseditiop, $rseditios, $tcount, $qcount);

$cfg['authmode'] = 3; 				// (1:cookies, 2:sessions, 3:cookies+sessions)
$cfg['xmlclient'] = FALSE; 			// For testing-purposes only, else keep it off
$cfg['enablecustomhf'] = FALSE;		// To enable header.$location.tpl and footer.$location.tpl
$cfg['pfs_dir'] = 'datas/users/';
$cfg['av_dir'] = 'datas/avatars/';
$cfg['photos_dir'] = 'datas/photos/';
$cfg['sig_dir'] = 'datas/signatures/';
$cfg['defav_dir'] = 'datas/defaultav/';
$cfg['th_dir'] = 'datas/thumbs/';
$cfg['pagination'] = ' [%s]';
$cfg['pagination_cur'] = ' <strong>&gt; %s &lt;</strong>';
$cfg['pfsmaxuploads'] = 8;
$cfg['version'] = '125';
$cfg['dbversion'] = '125a';
$cfg['sqldb'] = 'mysql';

/* ======== Names of the SQL tables ========= */

$sed_dbnames = array ('auth', 'auth_default', 'banlist', 'cache', 'com', 'core', 'config', 'forum_sections', 'forum_structure', 'forum_topics', 'forum_posts', 'groups', 'groups_users', 'logger', 'online', 'pages', 'pfs', 'pfs_folders', 'plugins', 'pm', 'polls_options', 'polls', 'polls_voters', 'rated', 'ratings', 'referers', 'smilies', 'stats', 'structure', 'trash', 'users');

foreach($sed_dbnames as $k => $i)
{
	$j = 'db_'.$i;
	$$j = 'sed_'.$i;
}

/**
 * Strips everything but alphanumeric, hyphens and underscores
 *
 * @param string $text Input
 * @return string
 */
function sed_alphaonly($text)
{
	return(preg_replace('/[^a-zA-Z0-9\-_]/', '', $text));
}

/*
 * ================================= Authorization Subsystem ==================================
 */

/**
 * Returns specific access permissions
 *
 * @param string $area Seditio area
 * @param string $option Option to access
 * @param string $mask Access mask
 * @return mixed
 */
function sed_auth($area, $option, $mask='RWA')
{
	global $sys, $usr;

	$mn['R'] = 1;
	$mn['W'] = 2;
	$mn['1'] = 4;
	$mn['2'] = 8;
	$mn['3'] = 16;
	$mn['4'] = 32;
	$mn['5'] = 64;
	$mn['A'] = 128;

	$masks = str_split($mask);
	$res = array();

	foreach($masks as $k => $ml)
	{
		if(empty($mn[$ml]))
		{
			$sys['auth_log'][] = $area.".".$option.".".$ml."=0";
			$res[] = FALSE;
		}
		elseif ($option=='any')
		{
			$cnt = 0;

			if(is_array($usr['auth'][$area]))
			{
				foreach($usr['auth'][$area] as $k => $g)
				{ $cnt += (($g & $mn[$ml]) == $mn[$ml]); }
			}
			$cnt = ($cnt==0 && $usr['auth']['admin']['a'] && $ml=='A') ? 1 : $cnt;

			$sys['auth_log'][] = ($cnt>0) ? $area.".".$option.".".$ml."=1" : $area.".".$option.".".$ml."=0";
			$res[] = ($cnt>0) ? TRUE : FALSE;
		}
		else
		{
			$sys['auth_log'][] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? $area.".".$option.".".$ml."=1" : $area.".".$option.".".$ml."=0";
			$res[] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? TRUE : FALSE;
		}
	}
	return (count($res) == 1) ? $res[0]: $res;
}

/**
 * Builds Access Control List (ACL) for a specific user
 *
 * @param int $userid User ID
 * @param int $maingrp User main group
 * @return array
 */
function sed_auth_build($userid, $maingrp=0)
{
	global $db_auth, $db_groups_users;

	$groups = array();
	$authgrid = array();
	$tmpgrid = array();

	if ($userid==0 || $maingrp==0)
	{
		$groups[] = 1;
	}
	else
	{
		$groups[] = $maingrp;
		$sql = sed_sql_query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid='$userid'");

		while ($row = sed_sql_fetcharray($sql))
		{ $groups[] = $row['gru_groupid']; }
	}

	$sql_groups = implode(',', $groups);
	$sql = sed_sql_query("SELECT auth_code, auth_option, auth_rights FROM $db_auth WHERE auth_groupid IN (".$sql_groups.") ORDER BY auth_code ASC, auth_option ASC");

	while ($row = sed_sql_fetcharray($sql))
	{ $authgrid[$row['auth_code']][$row['auth_option']] |= $row['auth_rights']; }

	return($authgrid);
}

/**
 * Clears user permissions cache
 *
 * @param mixed $id User ID or 'all'
 * @return int
 */
function sed_auth_clear($id='all')
{
	global $db_users;

	if($id=='all')
	{
		$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE 1");
	}
	else
	{
		$sql = sed_sql_query("UPDATE $db_users SET user_auth='' WHERE user_id='$id'");
	}
	return sed_sql_affectedrows();
}


/*
 * ================================= BBCode Parser API ==================================
 */

/**
 * Registers a new bbcode in database.
 * In 'callback' mode $replacement is normal PHP function body (without declaration) which
 * takes $input array of matches as parameter and must return a replacement string. These
 * variables are also imported as globals in callback function: $cfg, $sys, $usr, $L, $skin, $sed_groups
 *
 * @global $db_bbcode;
 * @param string $name BBcode name
 * @param string $mode Parsing mode, on of the following: 'str' (str_replace), 'ereg' (eregi_replace), 'pcre' (preg_replace) and 'callback' (preg_replace_callback)
 * @param string $pattern Bbcode string or entire regular expression
 * @param string $replacement Replacement string or regular substitution or callback body
 * @param bool $container Whether bbcode is container (like [bbcode]Something here[/bbcode])
 * @param int $priority BBcode priority from 0 to 255. Smaller priority bbcodes are parsed first, 128 is default medium priority.
 * @param string $plug Plugin/part name this bbcode belongs to.
 * @param bool $postrender Whether this bbcode must be applied on a pre-rendered HTML cache.
 * @return bool
 */
function sed_bbcode_add($name, $mode, $pattern, $replacement, $container = true, $priority = 128, $plug = '', $postrender = false)
{
	global $db_bbcode;
	$bbc['name'] = $name;
	$bbc['mode'] = $mode;
	$bbc['pattern'] = $pattern;
	$bbc['replacement'] = $replacement;
	$bbc['container'] = empty($container) ? 0 : 1;
	if($priority >= 0 && $priority < 256)
	{
		$bbc['priority'] = (int) $priority;
	}
	if(!empty($plug))
	{
		$bbc['plug'] = $plug;
	}
	$bbc['postrender'] = empty($postrender) ? 0 : 1;
	return sed_sql_insert($db_bbcode, $bbc, 'bbc_') == 1;
}

/**
 * Removes a bbcode from parser database.
 *
 * @global $db_bbcode
 * @param int $id BBCode ID or 0 to remove all (use carefully)
 * @param string $plug Remove all bbcodes that belong to this plug
 * @return bool
 */
function sed_bbcode_remove($id = 0, $plug = '')
{
	global $db_bbcode;
	if($id > 0)
	{
		return sed_sql_delete($db_bbcode, "bbc_id = $id") == 1;
	}
	elseif(!empty($plug))
	{
		return sed_sql_delete($db_bbcode, "bbc_plug = '" . sed_sql_prep($plug) . "'");
	}
	else
	{
		return sed_sql_delete($db_bbcode) > 0;
	}
}

/**
 * Updates bbcode data in parser database.
 *
 * @global $db_bbcode;
 * @param int $id BBCode ID
 * @param bool $enabled Enable the bbcode
 * @param string $name BBcode name
 * @param string $mode Parsing mode, on of the following: 'str' (str_replace), 'ereg' (eregi_replace), 'pcre' (preg_replace) and 'callback' (preg_replace_callback)
 * @param string $pattern Bbcode string or entire regular expression
 * @param string $replacement Replacement string or regular substitution or callback body
 * @param bool $container Whether bbcode is container (like [bbcode]Something here[/bbcode])
 * @param int $priority BBcode preority from 0 to 255. Smaller priority bbcodes are parsed first, 128 is default medium priority.
 * @param bool $postrender Whether this bbcode must be applied on a pre-rendered HTML cache.
 * @return bool
 */
function sed_bbcode_update($id, $enabled, $name, $mode, $pattern, $replacement, $container, $priority = 128, $postrender = false)
{
	global $db_bbcode;
	$bbc['enabled'] = empty($enabled) ? 0 : 1;
	if(!empty($name))
	{
		$bbc['name'] = $name;
	}
	if(!empty($mode))
	{
		$bbc['mode'] = $mode;
	}
	if(!empty($pattern))
	{
		$bbc['pattern'] = $pattern;
	}
	if(!empty($replacement))
	{
		$bbc['replacement'] = $replacement;
	}
	if($priority >= 0 && $priority < 256)
	{
		$bbc['priority'] = $priority;
	}
	$bbc['container'] = empty($container) ? 0 : 1;
	$bbc['postrender'] = empty($postrender) ? 0 : 1;
	return sed_sql_update($db_bbcode, "bbc_id = $id", $bbc, 'bbc_') == 1;
}

/**
 * Loads bbcodes from database if they havent been already loaded.
 *
 * @global $sed_bbcodes
 * @global $db_bbcode
 */
function sed_bbcode_load()
{
	global $db_bbcode, $sed_bbcodes, $sed_bbcodes_post, $sed_bbcode_containers;
	if(!is_array($sed_bbcodes))
	{
		$sed_bbcodes = array();
		$sed_bbcodes_post = array();
		$sed_bbcode_containers = ''; // required for auto-close
		$bbc_cntr = array();
		$i = 0;
		$j = 0;
		$res = sed_sql_query("SELECT * FROM $db_bbcode WHERE bbc_enabled = 1 ORDER BY bbc_priority");
		while($row = sed_sql_fetchassoc($res))
		{
			if($row['bbc_postrender'] == 1)
			{
				foreach($row as $key => $val)
				{
					$sed_bbcodes_post[$j][str_replace('bbc_', '', $key)] = $val;
				}
				$j++;
			}
			else
			{
				foreach($row as $key => $val)
				{
					$sed_bbcodes[$i][str_replace('bbc_', '', $key)] = $val;
				}
				$i++;
			}
			if($row['bbc_container'] == 1 && !isset($bbc_cntr[$row['bbc_name']]))
			{
				$sed_bbcode_containers .= $row['bbc_name'] . '|';
				$bbc_cntr[$row['bbc_name']] = 1;
			}
		}
		sed_sql_freeresult($res);
		if(!empty($sed_bbcode_containers))
		{
			$sed_bbcode_containers = mb_substr($sed_bbcode_containers, 0, -1);
		}
	}
}

/**
 * Parses bbcodes in text.
 *
 * @global $sed_bbcodes
 * @param string $text Text body
 * @param bool $post Post-rendering
 * @return string
 */
function sed_bbcode_parse($text, $post = false)
{
	global $cfg, $sed_bbcodes, $sed_bbcodes_post, $sed_bbcode_containers;
	if($cfg['parser_custom'])
	{
		return $text;
	}
	// BB auto-close
	$bbc = array();
	if(!$post && preg_match_all('#\[(/)?('.$sed_bbcode_containers.')(=[^\]]*)?\]#i', $text, $mt, PREG_SET_ORDER))
	{
		// Count all unclosed bbcode entries
		for($i = 0, $cnt = count($mt); $i < $cnt; $i++)
		{
				if($mt[$i][1] == '/')
				{
					$bb = $mt[$i][2];
					// Protect from "[/foo] [/bar][foo][bar]" trick
					if($bbc[$bb] > 0) $bbc[$bb]--;
					// else echo 'ERROR: invalid closing bbcode detected';
				}
				else
				{
					// Count opening tag in
					$bbc[$mt[$i][2]]++;
				}
		}
		// Close all unclosed tags. Produces non XHTML-compliant output
		// (doesn't take tag order and semantics into account) but fixes the layout
		if(count($bbc) > 0)
		{
			foreach($bbc as $bb => $c)
			{
				$text .= str_repeat("[/$bb]", $c);
			}
		}
	}
	// Done, ready to parse bbcodes
	$cnt = $post ? count($sed_bbcodes_post) : count($sed_bbcodes);
	for($i = 0; $i < $cnt; $i++)
	{
		$bbcode = ($post) ? $sed_bbcodes_post[$i] : $sed_bbcodes[$i];
		switch($bbcode['mode'])
		{
			case 'str':
				$text = str_ireplace($bbcode['pattern'], $bbcode['replacement'], $text);
			break;

			case 'ereg':
				$text = mb_eregi_replace($bbcode['pattern'], $bbcode['replacement'], $text);
			break;

			case 'pcre':
				$text = preg_replace('`'.$bbcode['pattern'].'`mis', $bbcode['replacement'], $text);
			break;

			case 'callback':
				$code = 'global $cfg, $sys, $usr, $L, $skin, $sed_groups;' . $bbcode['replacement'];
				$text = preg_replace_callback('`'.$bbcode['pattern'].'`mis', create_function('$input', $code), $text);
			break;
		}
	}

	return $text;
}

/**
 * JavaScript HTML obfuscator to protect some parts (like email) from bots
 *
 * @param string $text Source text
 * @return string
 */
function sed_obfuscate($text)
{
	static $calls = 0;
	$enc_string = '';
	$length = mb_strlen($text);
	for ($i=0; $i < $length; $i++) {
		$inter = ord($text[$i]) + 3;
		$enc_char =  chr($inter);
		$enc_string .= ($enc_char == '\\' ? '\\\\' : $enc_char);
	}
	// get a random string to use as a function name
	srand((float) microtime() * 10000000);
	$letters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	$rnd = $letters[array_rand($letters)] . md5(time());
	// the actual js (in one line to confuse)
	if($calls++ == 0) $var = 'var a,s,n;';
	$script = "<script language=\"JavaScript\" type=\"text/javascript\">{$var}function $rnd(s){r='';for(i=0;i<s.length;i++){n=s.charCodeAt(i);if(n>=8364){n=128;}r+=String.fromCharCode(n-3);}return r;}a='".$enc_string."';document.write($rnd(a));</script>";
	return $script;
}

/**
 * Supplimentary email obfuscator callback
 *
 * @param array $m PCRE entry
 * @return string
 */
function sed_obfuscate_eml($m)
{
	return $m[1].sed_obfuscate('<a href="mailto:'.$m[2].'">'.$m[2].'</a>');
}


/**
 * Automatically detect and parse URLs in text into HTML
 *
 * @param string $text Text body
 * @return string
 */
function sed_parse_autourls($text)
{
	$text = preg_replace('`(^|\s)(http|https|ftp)://([^\s"\'\[]+)`', '$1<a href="$2://$3">$2://$3</a>', $text);
	$text = preg_replace_callback('`(^|\s)(\w[\._\w\-]+@[\w\.\-]+\.[a-z]+)`', 'sed_obfuscate_eml', $text);
	return $text;
}

/**
 * Supplimentary br stripper callback
 *
 * @param array $m PCRE entries
 * @return string
 */
function sed_parse_pre($m)
{
	return str_replace('<br />', '', $m[0]);
}

/**
 * Parses text body
 *
 * @param string $text Source text
 * @param bool $parse_bbcodes Enable bbcode parsing
 * @param bool $parse_smilies Enable emoticons
 * @param bool $parse_newlines Replace line breaks with <br />
 * @return string
 */
function sed_parse($text, $parse_bbcodes = TRUE, $parse_smilies = TRUE, $parse_newlines = TRUE)
{
	global $cfg, $sys, $sed_smilies, $L, $usr;

	if($cfg['parser_custom'])
	{
		include_once $cfg['system_dir'].'/parser.php';
		if(function_exists('sed_custom_parse'))
		{
			return sed_custom_parse($text, $parse_bbcodes, $parse_smilies, $parse_newlines);
		}
	}

	$text = ' '.$text;
	$code = array();
	$unique_seed = $sys['unique'];
	$ii = 10000;

	$text = sed_parse_autourls($text);

	if($parse_bbcodes)
	{
		$p1 = 1;
		$p2 = 1;
		while($p1 > 0 && $p2> 0  && $ii < 10031)
		{
			$ii++;
			$p1 = mb_strpos($text, '[code]');
			$p2 = mb_strpos($text, '[/code]');
			if($p2 > $p1 && $p1 > 0)
			{
				$key = '**'.$ii.$unique_seed.'**';
				$code[$key] = mb_substr($text, $p1 + 6, ($p2 - $p1) - 6);
				$code_len = mb_strlen($code[$key]) + 13;
				$code[$key] = str_replace(
				array('{', '<', '>' , '\'', '"', "<!--", '$' ),
				array('&#123;', '&lt;', '&gt;', '&#039;', '&quot;', '"&#60;&#33;--"', '&#036;' ),$code[$key]);
				$code[$key] = '<pre>'.trim($code[$key])."</pre>";
				$text = substr_replace($text, $key, $p1, $code_len);
			}
		}

		//$text = htmlspecialchars($text);
		$text = sed_bbcode_parse($text);
	}

	// TODO replace with new smiley system
	if ($parse_smilies && is_array($sed_smilies))
	{
		reset($sed_smilies);
		while ((list($j,$dat) = each($sed_smilies)))
		{
			$ii++;
			$key = '**'.$ii.$unique_seed.'**';
			$code[$key]= "<img class=\"aux\" src=\"".$dat['smilie_image']."\" alt=\"\" />";
			$text = str_replace($dat['smilie_code'], $key, $text);
		}
	}

	if ($parse_bbcodes || $parse_smilies)
	{
		foreach($code as $x => $y)
		{ $text = str_replace($x, $y, $text); }
	}

	if ($parse_newlines)
	{
		$text = nl2br($text);
		$text = str_replace("\r", '', $text);
		// Strip extraneous breaks
		$text = preg_replace('#<(/?)(p|hr|ul|ol|li|blockquote|table|tr|td|th)(.*?)>(\s*)<br />#', '<$1$2$3>', $text);
		$text = preg_replace_callback('#<pre>(.+?)</pre>#sm', 'sed_parse_pre', $text);
	}

	return mb_substr($text, 1);
}

/**
 * Post-render parser function
 *
 * @param string $text Text body
 * @param string $area Site area to check bbcode enablement
 * @return string
 */
function sed_post_parse($text, $area = '')
{
	global $cfg;
	if(!$cfg['parser_custom'] && (empty($area) || $cfg["parsebbcode$area"]))
	{
		return sed_bbcode_parse($text, true);
	}
	return $text;
}

/**
 * Block user if he is not allowed to access the page
 *
 * @param bool $allowed Authorization result
 * @return bool
 */
function sed_block($allowed)
{
	if(!$allowed)
	{
		global $sys;
		header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=930&".$sys['url_redirect']);
		exit;
	}
	return FALSE;
}


/**
 * Block guests from viewing the page
 *
 * @return bool
 */
function sed_blockguests()
{
	global $usr, $sys;

	if ($usr['id']<1)
	{
		header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=930&".$sys['url_redirect']);
		exit;
	}
	return FALSE;
}

/* ------------------ */
// TODO eliminate this function
function sed_build_addtxt($c1, $c2)
{
	$result = "
	function addtxt(text)
	{
	document.".$c1.".".$c2.".value  += text;
	document.".$c1.".".$c2.".focus();
	}
	";
	return($result);
}

/**
 * Calculates age out of D.O.B.
 *
 * @param int $birth Date of birth as UNIX timestamp
 * @return int
 */
function sed_build_age($birth)
{
	global $sys;

	if ($birth==1)
	{ return ('?'); }

	$day1 = @date('d', $birth);
	$month1 = @date('m', $birth);
	$year1 = @date('Y', $birth);

	$day2 = @date('d', $sys['now_offset']);
	$month2 = @date('m', $sys['now_offset']);
	$year2 = @date('Y', $sys['now_offset']);

	$age = ($year2-$year1)-1;

	if ($month1<$month2 || ($month1==$month2 && $day1<=$day2))
	{ $age++; }

	if($age < 0)
	{ $age += 136; }

	return ($age);
}

/* ------------------ */
// TODO eliminate this function
function sed_build_bbcodes($c1, $c2, $title)
{
	//$result = "<a href=\"javascript:help('bbcodes','".$c1."','".$c2."')\">".$title."</a>";
	//return($result);
	return '';
}

/* ------------------ */
// TODO eliminate this function
function sed_build_bbcodes_local($limit)
{
	/*global $sed_bbcodes;

	reset ($sed_bbcodes);

	$result = '<div class="bbcodes">';

	while (list($i,$dat)=each($sed_bbcodes))
	{
		$kk = 'bbcodes_'.$dat[1];
		$result .= "<a href=\"javascript:addtxt('".$dat[0]."')\"><img src=\"images/bbcodes/".$dat[1].".gif\" alt=\"\" /></a> ";
	}

	$result .= "</div>";
	return($result);*/
	return '';
}

/**
 * Builds category path
 *
 * @param string $cat Category code
 * @param string $mask Format mask
 * @return string
 */
function sed_build_catpath($cat, $mask)
{
	global $sed_cat, $cfg;
	$pathcodes = explode('.', $sed_cat[$cat]['path']);
	foreach($pathcodes as $k => $x)
	{
		$tmp[]= sprintf($mask, $x, $sed_cat[$x]['title']);
	}
	return implode(' '.$cfg['separator'].' ', $tmp);
}

/* ------------------ */
// TODO replace with new comments plugin
function sed_build_comments($code, $url, $display)
{
	global $db_com, $db_users, $db_pages, $cfg, $usr, $L, $sys;

	list($usr['auth_read_com'], $usr['auth_write_com'], $usr['isadmin_com']) = sed_auth('comments', 'a');
	sed_block($usr['auth_read_com']);

	if ($cfg['disable_comments'] || !$usr['auth_read_com'])
	{ return (array('',''));  }

	if ($display)
	{
		$ina = sed_import('ina','G','ALP');
		$ind = sed_import('ind','G','INT');

		if ($ina=='send' && $usr['auth_write_com'])
		{
			sed_shield_protect();
			$rtext = sed_import('rtext','P','HTM');

			/* == Hook for the plugins == */
			$extp = sed_getextplugins('comments.send.first');
			if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */

			$error_string .= (mb_strlen($rtext)<2) ? $L['com_commenttooshort']."<br />" : '';
			$error_string .= (mb_strlen($rtext)>2000) ? $L['com_commenttoolong']."<br />" : '';

			if (empty($error_string))
			{
				$sql = sed_sql_query("INSERT INTO $db_com (com_code, com_author, com_authorid, com_authorip, com_text, com_date) VALUES ('".sed_sql_prep($code)."', '".sed_sql_prep($usr['name'])."', ".(int)$usr['id'].", '".$usr['ip']."', '".sed_sql_prep($rtext)."', ".(int)$sys['now_offset'].")");

				if (mb_substr($code, 0, 1) =='p')
				{
					$page_id = mb_substr($code, 1, 10);
					$sql = sed_sql_query("UPDATE $db_pages SET page_comcount='".sed_get_comcount($code)."' WHERE page_id='".$page_id."'");
				}

				/* == Hook for the plugins == */
				$extp = sed_getextplugins('comments.send.new');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				sed_shield_update(20, "New comment");
				header("Location: " . SED_ABSOLUTE_URL . "$url&comments=1");
				exit;
			}
		}

		if ($ina=='delete' && $usr['isadmin_com'])
		{
			sed_check_xg();
			$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_id='$ind' LIMIT 1");

			if ($row = sed_sql_fetchassoc($sql))
			{
				if ($cfg['trash_comment'])
				{ sed_trash_put('comment', $L['Comment']." #".$ind." (".$row['com_author'].")", $ind, $row); }

				$sql = sed_sql_query("DELETE FROM $db_com WHERE com_id='$ind'");

				if (mb_substr($row['com_code'], 0, 1) == 'p')
				{
					$page_id = mb_substr($row['com_code'], 1, 10);
					$sql = sed_sql_query("UPDATE $db_pages SET page_comcount=".sed_get_comcount($row['com_code'])." WHERE page_id=".$page_id);
				}

				sed_log("Deleted comment #".$ind." in '".$code."'",'adm');
			}

			header("Location: " . SED_ABSOLUTE_URL . "".$url."&comments=1");
			exit;
		}

		$error_string .= ($ina=='added') ? $L['com_commentadded']."<br />" : '';

		$t = new XTemplate(sed_skinfile('comments'));

		/* == Hook for the plugins == */
		$extp = sed_getextplugins('comments.main');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$sql = sed_sql_query("SELECT c.*, u.user_avatar FROM $db_com AS c
		LEFT JOIN $db_users AS u ON u.user_id=c.com_authorid
		WHERE com_code='$code' ORDER BY com_id ASC");

		if (!empty($error_string))
		{
			$t->assign("COMMENTS_ERROR_BODY",$error_string);
			$t->parse("COMMENTS.COMMENTS_ERROR");
		}

		if ($usr['auth_write_com'])
		{
			$bbcodes = ($cfg['parsebbcodecom']) ? sed_build_bbcodes("newcomment", "rtext", $L['BBcodes']) : '';
			$smilies = ($cfg['parsesmiliescom']) ? sed_build_smilies("newcomment", "rtext", $L['Smilies']) : '';
			$pfs = ($usr['id']>0) ? sed_build_pfs($usr['id'], "newcomment", "rtext", $L['Mypfs']) : '';
			$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, "newcomment", "rtext", $L['SFS']) : '';
			$post_main = "<textarea class=\"editor\" name=\"rtext\" rows=\"4\" cols=\"40\">".$rtext."</textarea><br />".$bbcodes." ".$smilies." ".$pfs;
		}

		$t->assign(array(
			"COMMENTS_CODE" => $code,
			"COMMENTS_FORM_SEND" => $url."&amp;comments=1&amp;ina=send",
			"COMMENTS_FORM_AUTHOR" => $usr['name'],
			"COMMENTS_FORM_AUTHORID" => $usr['id'],
			"COMMENTS_FORM_TEXT" => $post_main,
			"COMMENTS_FORM_TEXTBOXER" => $post_main,
			"COMMENTS_FORM_BBCODES" => $bbcodes,
			"COMMENTS_FORM_SMILIES" => $smilies,
			"COMMENTS_FORM_MYPFS" => $pfs
		));

		if ($usr['auth_write_com'])
		{

			/* == Hook for the plugins == */
			$extp = sed_getextplugins('comments.newcomment.tags');
			if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */

			$t->parse("COMMENTS.COMMENTS_NEWCOMMENT");
		}

		if (sed_sql_numrows($sql)>0)
		{
			$i = 0;

			/* === Hook - Part1 : Set === */
			$extp = sed_getextplugins('comments.loop');
			/* ===== */

			while ($row = sed_sql_fetcharray($sql))
			{
				$i++;
				$com_author = sed_cc($row['com_author']);
				$com_text = sed_cc($row['com_text']);

				$com_admin = ($usr['isadmin_com']) ? $L['Ip'].":".sed_build_ipsearch($row['com_authorip'])." &nbsp;".$L['Delete'].":[<a href=\"".$url."&amp;comments=1&amp;ina=delete&amp;ind=".$row['com_id']."&amp;".sed_xg()."\">x</a>]" : '' ;
				$com_authorlink = ($row['com_authorid']>0) ? "<a href=\"users.php?m=details&amp;id=".$row['com_authorid']."\">".$com_author."</a>" : $com_author ;

				$t-> assign(array(
					"COMMENTS_ROW_ID" => $row['com_id'],
					"COMMENTS_ROW_ORDER" => $i,
					"COMMENTS_ROW_URL" => $url."&amp;comments=1#c".$row['com_id'],
					"COMMENTS_ROW_AUTHOR" => $com_authorlink,
					"COMMENTS_ROW_AUTHORID" => $row['com_authorid'],
					"COMMENTS_ROW_AVATAR" => sed_build_userimage($row['user_avatar']),
					"COMMENTS_ROW_TEXT" => sed_parse($com_text, $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1),
					"COMMENTS_ROW_DATE" => @date($cfg['dateformat'], $row['com_date'] + $usr['timezone'] * 3600),
					"COMMENTS_ROW_ADMIN" => $com_admin,
				));

				/* === Hook - Part2 : Include === */
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				$t->parse("COMMENTS.COMMENTS_ROW");
			}
		}
		else
		{
			$t-> assign(array(
				"COMMENTS_EMPTYTEXT" => $L['com_nocommentsyet'],
			));
			$t->parse("COMMENTS.COMMENTS_EMPTY");
		}

		/* == Hook for the plugins == */
		$extp = sed_getextplugins('comments.tags');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$t->parse("COMMENTS");
		$res_display = $t->text("COMMENTS");
	}
	else
	{
		$res_display = '';
	}

	$res = "<a href=\"".$url."&amp;comments=1\"><img src=\"skins/".$usr['skin']."/img/system/icon-comment.gif\" alt=\"\" />";

	if ($cfg['countcomments'])
	{
		$nbcomment = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_com where com_code='$code'"), 0, "COUNT(*)");
		$res .= " (".$nbcomment.")";
	}
	$res .= "</a>";

	return(array($res, $res_display, $nbcomment));
}

/**
 * Returns country text button
 *
 * @param string $flag Country code
 * @return string
 */
function sed_build_country($flag)
{
	global $sed_countries;

	$flag = (empty($flag)) ? '00' : $flag;
	return '<a href="users.php?f=country_'.$flag.'">'.$sed_countries[$flag].'</a>';
}

/**
 * Returns user email link
 *
 * @param string $email E-mail address
 * @param bool $hide Hide email option
 * @return string
 */
function sed_build_email($email, $hide = false)
{
	global $L;
	if($hide)
	{
		return $L['Hidden'];
	}
	elseif(!empty($email) && preg_match('#^\w[\._\w\-]+@[\w\.\-]+\.[a-z]+$#', $email))
	{
		return sed_obfuscate('<a href="mailto:'.$email.'">'.$email.'</a>');
	}
}

/**
 * Returns country flag button
 *
 * @param string $flag Country code
 * @return string
 */
function sed_build_flag($flag)
{
	$flag = (empty($flag)) ? '00' : $flag;
	return '<a href="users.php?f=country_'.$flag.'"><img src="images/flags/f-'.$flag.'.gif" alt="'.$flag.'" /></a>';
}

/**
 * Returns forum thread path
 *
 * @param int $sectionid Section ID
 * @param string $title Thread title
 * @param string $category Category code
 * @param string $link Display as links
 * @return string
 */
function sed_build_forums($sectionid, $title, $category, $link=TRUE)
{
	global $sed_forums_str, $cfg;
	$pathcodes = explode('.', $sed_forums_str[$category]['path']);

	if($link)
	{
		foreach($pathcodes as $k => $x)
		{
			$tmp[] = '<a href="forums.php?c='.$x.'#'.$x.'">'.sed_cc($sed_forums_str[$x]['title']).'</a>';
		}
		$tmp[] = '<a href="forums.php?m=topics&s='.$sectionid.'">'.sed_cc($title).'</a>';
	}
	else
	{
		foreach($pathcodes as $k => $x)
		{
			$tmp[]= sed_cc($sed_forums_str[$x]['title']);
		}
		$tmp[] = sed_cc($title);
	}

	return implode(' '.$cfg['separator'].' ', $tmp);
}


/* ------------------ */
// TODO eliminate this function
function sed_build_gallery($id, $c1, $c2, $title)
{
	return("<a href=\"javascript:gallery('".$id."','".$c1."','".$c2."')\">".$title."</a>");
}

/**
 * Returns group link (button)
 *
 * @param int $grpid Group ID
 * @return string
 */
function sed_build_group($grpid)
{
	if(empty($grpid)) return '';
	global $sed_groups, $L;

	if($sed_groups[$grpid]['hidden'])
	{
		if(sed_auth('users', 'a', 'A'))
		{
			return '<a href="users.php?gm='.$grpid.'">'.$sed_groups[$grpid]['title'].'</a> ('.$L['Hidden'].')';
		}
		else
		{
			return $L['Hidden'];
		}
	}
	else
	{
		return '<a href="users.php?gm='.$grpid.'">'.$sed_groups[$grpid]['title'].'</a>';
	}
}

/**
 * Builds "edit group" option group for "user edit" part
 *
 * @param int $userid Edited user ID
 * @param bool $edit Permission
 * @param int $maingrp User main group
 * @return string
 */
function sed_build_groupsms($userid, $edit=FALSE, $maingrp=0)
{
	global $db_groups_users, $sed_groups, $L, $usr;

	$sql = sed_sql_query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid='$userid'");

	while ($row = sed_sql_fetcharray($sql))
	{
		$member[$row['gru_groupid']] = TRUE;
	}

	foreach($sed_groups as $k => $i)
	{
		$checked = ($member[$k]) ? "checked=\"checked\"" : '';
		$checked_maingrp = ($maingrp==$k) ? "checked=\"checked\"" : '';
		$readonly = (!$edit || $usr['level'] < $sed_groups[$k]['level'] || $k==SED_GROUP_GUESTS || $k==SED_GROUP_INACTIVE || $k==SED_GROUP_BANNED || ($k==SED_GROUP_TOPADMINS && $userid==1)) ? "disabled=\"disabled\"" : '';
		$readonly_maingrp = (!$edit || $usr['level'] < $sed_groups[$k]['level'] || $k==SED_GROUP_GUESTS || ($k==SED_GROUP_INACTIVE && $userid==1) || ($k==SED_GROUP_BANNED && $userid==1)) ? "disabled=\"disabled\"" : '';

		if ($member[$k] || $edit)
		{
			if (!($sed_groups[$k]['hidden'] && !sed_auth('users', 'a', 'A')))
			{
				$res .= "<input type=\"radio\" class=\"radio\" name=\"rusermaingrp\" value=\"$k\" ".$checked_maingrp." ".$readonly_maingrp." /> \n";
				$res .= "<input type=\"checkbox\" class=\"checkbox\" name=\"rusergroupsms[$k]\" ".$checked." $readonly />\n";
				$res .= ($k == SED_GROUP_GUESTS) ? $sed_groups[$k]['title'] : "<a href=\"users.php?g=".$k."\">".$sed_groups[$k]['title']."</a>";
				$res .= ($sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
				$res .= "<br />";
			}
		}
	}

	return $res;
}

/**
 * Returns user ICQ pager link
 *
 * @param int $text ICQ number
 * @return string
 */
function sed_build_icq($text)
{
	global $cfg;

	$text = (int) $text;
	if($text > 0)
	{
		return $text.' <a href="http://www.icq.com/'.$text.'#pager"><img src="http://web.icq.com/whitepages/online?icq='.$text.'&img=5" alt="" /></a>';
	}
	return '';
}

/**
 * Returns IP Search link
 *
 * @param string $ip IP mask
 * @return string
 */
function sed_build_ipsearch($ip)
{
	global $xk;
	if(!empty($ip))
	{
		return '<a href="admin.php?m=tools&p=ipsearch&a=search&id='.$ip.'&x='.$xk.'">'.$ip.'</a>';
	}
	return '';
}

/**
 * Returns MSN link as e-mail link
 *
 * @param string $msn MSN address
 * @return string
 */
function sed_build_msn($msn)
{
	return sed_build_email($msn);
}

/**
 * Odd/even class choser for row
 *
 * @param int $number Row number
 * @return string
 */
function sed_build_oddeven($number)
{
	return ($number % 2 == 0 ) ? 'even' : 'odd';
}

/* ------------------ */
// TODO eliminate this function
function sed_build_pfs($id, $c1, $c2, $title)
{
	global $L, $cfg, $usr, $sed_groups;
	if ($cfg['disable_pfs'])
	{ $res = ''; }
	else
	{
		if ($id==0)
		{ $res = "<a href=\"javascript:pfs('0','".$c1."','".$c2."')\">".$title."</a>"; }
		elseif ($sed_groups[$usr['maingrp']]['pfs_maxtotal']>0 && $sed_groups[$usr['maingrp']]['pfs_maxfile']>0 && sed_auth('pfs', 'a', 'R'))
		{ $res = "<a href=\"javascript:pfs('".$id."','".$c1."','".$c2."')\">".$title."</a>"; }
		else
		{ $res = ''; }
	}
	return($res);
}

/**
 * Returns user PM link
 *
 * @param int $user User ID
 * @return string
 */
function sed_build_pm($user)
{
	global $usr;
	return '<a href="pm.php?m=send&to='.$user.'"><img src="skins/'.$usr['skin'].'/img/system/icon-pm.gif"  alt="" /></a>';
}

/* ------------------ */
// TODO replace with new ratings subsystem
function sed_build_ratings($code, $url, $display)
{
	global $db_ratings, $db_rated, $db_users, $cfg, $usr, $sys, $L;

	list($usr['auth_read_rat'], $usr['auth_write_rat'], $usr['isadmin_rat']) = sed_auth('ratings', 'a');

	if ($cfg['disable_ratings'] || !$usr['auth_read_rat'])
	{ return (array('','')); }

	$sql = sed_sql_query("SELECT * FROM $db_ratings WHERE rating_code='$code' LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{
		$rating_average = $row['rating_average'];
		$yetrated = TRUE;
		if ($rating_average<1)
		{ $rating_average = 1; }
		elseif ($rating_average>10)
		{ $rating_average = 10; }
		$rating_cntround = round($rating_average, 0);
	}
	else
	{
		$yetrated = FALSE;
		$rating_average = 0;
		$rating_cntround = 0;
	}

	$res = "<a href=\"".$url."&amp;ratings=1\"><img src=\"skins/".$usr['skin']."/img/system/vote".$rating_cntround.".gif\" alt=\"\" /></a>";

	if ($display)
	{
		$ina = sed_import('ina','G','ALP');
		$newrate = sed_import('newrate','P','INT');

		$alr_rated = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM ".$db_rated." WHERE rated_userid=".$usr['id']." AND rated_code = '".sed_sql_prep($code)."'"), 0, 'COUNT(*)');

		if ($ina=='send' && $newrate>=1 && $newrate<=10 && $usr['auth_write_rat'] && $alr_rated<=0)
		{
			/* == Hook for the plugins == */
			$extp = sed_getextplugins('ratings.send.first');
			if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */

			if (!$yetrated)
			{
				$sql = sed_sql_query("INSERT INTO $db_ratings (rating_code, rating_state, rating_average, rating_creationdate, rating_text) VALUES ('".sed_sql_prep($code)."', 0, ".(int)$newrate.", ".(int)$sys['now_offset'].", '') ");
			}

			$sql = sed_sql_query("INSERT INTO $db_rated (rated_code, rated_userid, rated_value) VALUES ('".sed_sql_prep($code)."', ".(int)$usr['id'].", ".(int)$newrate.")");
			$sql = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code'");
			$rating_voters = sed_sql_result($sql, 0, "COUNT(*)");
			$ratingnewaverage = ($rating_average * ($rating_voters - 1) + $newrate) / ( $rating_voters );
			$sql = sed_sql_query("UPDATE $db_ratings SET rating_average='$ratingnewaverage' WHERE rating_code='$code'");

			/* == Hook for the plugins == */
			$extp = sed_getextplugins('ratings.send.done');
			if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */

			header("Location: " . SED_ABSOLUTE_URL . "$url&ratings=1&ina=added");
			exit;
		}

		$votedcasted = ($ina=='added') ? 1 : 0;

		$rate_form = "<input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"1\" /><img src=\"skins/".$usr['skin']."/img/system/vote1.gif\" alt=\"\" /> 1 - ".$L['rat_choice1']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"2\" /><img src=\"skins/".$usr['skin']."/img/system/vote2.gif\" alt=\"\" /> 2 - ".$L['rat_choice2']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"3\" /><img src=\"skins/".$usr['skin']."/img/system/vote3.gif\" alt=\"\" /> 3 - ".$L['rat_choice3']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"4\" /><img src=\"skins/".$usr['skin']."/img/system/vote4.gif\" alt=\"\" /> 4 - ".$L['rat_choice4']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"5\" checked=\"checked\" /><img src=\"skins/".$usr['skin']."/img/system/vote5.gif\" alt=\"\" /> 5 - ".$L['rat_choice5']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"6\" /><img src=\"skins/".$usr['skin']."/img/system/vote6.gif\" alt=\"\" /> 6 - ".$L['rat_choice6']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"7\" /><img src=\"skins/".$usr['skin']."/img/system/vote7.gif\" alt=\"\" /> 7 - ".$L['rat_choice7']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"8\" /><img src=\"skins/".$usr['skin']."/img/system/vote8.gif\" alt=\"\" /> 8 - ".$L['rat_choice8']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"9\" /><img src=\"skins/".$usr['skin']."/img/system/vote9.gif\" alt=\"\" /> 9 - ".$L['rat_choice9']."<br /><input type=\"radio\" class=\"radio\" name=\"newrate\" value=\"10\" /><img src=\"skins/".$usr['skin']."/img/system/vote10.gif\" alt=\"\" /> 10 - ".$L['rat_choice10'];

		if ($usr['id']>0)
		{
			$sql1 = sed_sql_query("SELECT rated_value FROM $db_rated WHERE rated_code='$code' AND rated_userid='".$usr['id']."' LIMIT 1");

			if ($row1 = sed_sql_fetcharray($sql1))
			{
				$alreadyvoted = TRUE;
				$rating_uservote = $L['rat_alreadyvoted']." (".$row1['rated_value'].")";
			}
		}

		$t = new XTemplate(sed_skinfile('ratings'));

		/* == Hook for the plugins == */
		$extp = sed_getextplugins('ratings.main');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		if (!empty($error_string))
		{
			$t->assign("RATINGS_ERROR_BODY",$error_string);
			$t->parse("RATINGS.RATINGS_ERROR");
		}

		if ($yetrated)
		{
			$sql = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code' ");
			$rating_voters = sed_sql_result($sql, 0, "COUNT(*)");
			$rating_average = $row['rating_average'];
			$rating_since = $L['rat_since']." ".date($cfg['dateformat'], $row['rating_creationdate'] + $usr['timezone'] * 3600);
			if ($rating_average<1)
			{ $rating_average = 1; }
			elseif ($ratingaverage>10)
			{ $rating_average = 10; }

			$rating = round($rating_average,0);
			$rating_averageimg = "<img src=\"skins/".$usr['skin']."/img/system/vote".$rating.".gif\" alt=\"\" />";
			$sql = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code' ");
			$rating_voters = sed_sql_result($sql, 0, "COUNT(*)");
		}
		else
		{
			$rating_voters = 0;
			$rating_since = '';
			$rating_average = $L['rat_notyetrated'];
			$rating_averageimg = '';
		}

		$t->assign(array(
			"RATINGS_AVERAGE" => $rating_average,
			"RATINGS_AVERAGEIMG" => $rating_averageimg,
			"RATINGS_VOTERS" => $rating_voters,
			"RATINGS_SINCE" => $rating_since
		));


		if ($usr['id']>0 && $votedcasted)
		{
			$t->assign(array(
				"RATINGS_EXTRATEXT" => $L['rat_votecasted'],
			));
			$t->parse("RATINGS.RATINGS_EXTRA");
		}
		elseif ($usr['id']>0 && $alreadyvoted)
		{
			$t->assign(array(
				"RATINGS_EXTRATEXT" => $rating_uservote,
			));
			$t->parse("RATINGS.RATINGS_EXTRA");
		}
		elseif ($usr['id']==0)
		{
			$t->assign(array(
				"RATINGS_EXTRATEXT" => $L['rat_registeredonly'],
			));
			$t->parse("MAIN.RATINGS_EXTRA");
		}

		elseif ($usr['id']>0 && !$alreadyvoted)
		{
			$t->assign(array(
				"RATINGS_NEWRATE_FORM_SEND" => $url."&amp;ratings=1&amp;ina=send",
				"RATINGS_NEWRATE_FORM_VOTER" => $usr['name'],
				"RATINGS_NEWRATE_FORM_RATE" => $rate_form
			));
			$t->parse("RATINGS.RATINGS_NEWRATE");
		}

		/* == Hook for the plugins == */
		$extp = sed_getextplugins('ratings.tags');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$t->parse("RATINGS");
		$res_display = $t->text("RATINGS");
	}
	else
	{
		$res_display = '';
	}

	return(array($res, $res_display));
}

/* ------------------ */
// TODO eliminate this function
function sed_build_smilies($c1, $c2, $title)
{
	$result = "<a href=\"javascript:help('smilies','".$c1."','".$c2."')\">".$title."</a>";
	return($result);
}

/* ------------------ */
// TODO eliminate this function
function sed_build_smilies_local($limit)
{
	global $sed_smilies;

	$result = '<div class=\"smilies\">';

	if (is_array($sed_smilies))
	{
		reset ($sed_smilies);
		while (list($i,$dat) = each($sed_smilies))
		{
			$result .= "<a href=\"javascript:addtxt('".$dat[1]."')\"><img src=\"".$dat['smilie_image']."\" alt=\"\" /></a> ";
		}
	}

	$result .= "</div>";
	return($result);
}

/**
 * Returns stars image for user level
 *
 * @param int $level User level
 * @return unknown
 */
function sed_build_stars($level)
{
	global $skin;

	if($level>0 and $level<100)
	{
		return '<img src="skins/'.$skin.'/img/system/stars'.(floor($level/10)+1).'.gif" alt="" />';
	}
	else
	{
		return '';
	}
}

/**
 * Returns time gap between 2 dates
 *
 * @param int $t1 Stamp 1
 * @param int $t2 Stamp2
 * @return string
 */
function sed_build_timegap($t1,$t2)
{
	global $L;

	$gap = $t2 - $t1;

	if($gap<=0 || !$t2 || $gap>94608000)
	{
		$result = '';
	}
	elseif($gap<60)
	{
		$result  = $gap.' '.$L['Seconds'];
	}
	elseif($gap<3600)
	{
		$gap = floor($gap/60);
		$result = ($gap<2) ? '1 '.$L['Minute'] : $gap.' '.$L['Minutes'];
	}
	elseif($gap<86400)
	{
		$gap1 = floor($gap/3600);
		$gap2 = floor(($gap-$gap1*3600)/60);
		$result = ($gap1<2) ? '1 '.$L['Hour'].' ' : $gap1.' '.$L['Hours'].' ';
		if ($gap2>0)
		{
			$result .= ($gap2<2) ? '1 '.$L['Minute'] : $gap2.' '.$L['Minutes'];
		}
	}
	else
	{
		$gap = floor($gap/86400);
		$result = ($gap<2) ? '1 '.$L['Day'] : $gap.' '.$L['Days'];
	}

	return $result;
}

/**
 * Returns user timezone offset
 *
 * @param int $tz Timezone
 * @return string
 */
function sed_build_timezone($tz)
{
	global $L;

	$result = 'GMT';

	if($tz==-1 OR $tz==1)
	{
		$result .= $tz.' '.$L['Hour'];
	}
	elseif($tz!=0)
	{
		$result .= $tz.' '.$L['Hours'];
	}
	return $result;
}

/**
 * Returns link for URL
 *
 * @param string $text URL
 * @param int $maxlen Max. allowed length
 * @return unknown
 */
function sed_build_url($text, $maxlen=64)
{
	global $cfg;

	if(!empty($text))
	{
		if(mb_strpos($text, 'http://') !== 0)
		{
			$text='http://'. $text;
		}
		$text = sed_cc($text);
		$text = '<a href="'.$text.'">'.sed_cutstring($text, $maxlen).'</a>';
	}
	return $text;
}

/**
 * Returns link to user profile
 *
 * @param int $id User ID
 * @param string $user User name
 * @return string
 */
function sed_build_user($id, $user)
{
	global $cfg;

	if($id == 0 && !empty($user))
	{
		return $user;
	}
	elseif($id == 0)
	{
		return '';
	}
	else
	{
		return (!empty($user)) ? '<a href="users.php?m=details&id='.$id.'">'.$user.'</a>' : '?';
	}
}

/**
 * Returns user avatar image
 *
 * @param string $image Image src
 * @return string
 */
function sed_build_userimage($image)
{
	// TODO this is quite useless function
	if (!empty($image))
	{
		$result = "<img src=\"".$image."\" alt=\"\" class=\"avatar\" />";
	}
	return($result);
}

/**
 * Renders user signature text
 *
 * @param string $text Signature text
 * @return string
 */
function sed_build_usertext($text)
{
	global $cfg;
	return sed_parse($text, $cfg['parsebbcodesusertext'], $cfg['parsesmiliesusertext'], 1);
}

/*
 * ================================ Cache Subsystem ================================
 */
// TODO scheduled for complete removal and replacement with new cache system

/**
 * Clears cache item
 *
 * @param string $name Item name
 * @return bool
 */
function sed_cache_clear($name)
{
	global $db_cache;

	sed_sql_query("DELETE FROM $db_cache WHERE c_name='$name'");
	return(TRUE);
}

/**
 * Clears cache completely
 *
 * @return bool
 */
function sed_cache_clearall()
{
	global $db_cache;
	sed_sql_query("DELETE FROM $db_cache");
	return(TRUE);
}

/**
 * Fetches cache value
 *
 * @param string $name Item name
 * @return mixed
 */
function sed_cache_get($name)
{
	global $cfg, $sys, $db_cache;

	if (!$cfg['cache'])
	{ return FALSE; }
	$sql = sed_sql_query("SELECT c_value FROM $db_cache WHERE c_name='$name' AND c_expire>'".$sys['now']."'");
	if ($row = sed_sql_fetcharray($sql))
	{ return(unserialize($row['c_value'])); }
	else
	{ return(FALSE); }
}

/**
 * Get all cache data and import it into global scope
 *
 * @param int $auto Only with autoload flag
 * @return mixed
 */
function sed_cache_getall($auto = 1)
{
	global $cfg, $sys, $db_cache;

	if (!$cfg['cache'])
	{ return FALSE; }
	$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_expire<'".$sys['now']."'");
	if ($auto)
	{ $sql = sed_sql_query("SELECT c_name, c_value FROM $db_cache WHERE c_auto=1"); }
	else
	{ $sql = sed_sql_query("SELECT c_name, c_value FROM $db_cache"); }
	if (sed_sql_numrows($sql)>0)
	{ return($sql); }
	else
	{ return(FALSE); }
}

/**
 * Puts an item into cache
 *
 * @param string $name Item name
 * @param mixed $value Item value
 * @param int $expire Expires in seconds
 * @param int $auto Autload flag
 * @return bool
 */
function sed_cache_store($name,$value,$expire,$auto="1")
{
	global $db_cache, $sys, $cfg;

	if (!$cfg['cache'])
	{ return(FALSE); }
	$sql = sed_sql_query("REPLACE INTO $db_cache (c_name, c_value, c_expire, c_auto) VALUES ('$name', '".sed_sql_prep(serialize($value))."', '".($expire + $sys['now'])."', '$auto')");
	return(TRUE);
}

/**
 * Makes HTML sequences safe
 *
 * @param string $text Source string
 * @return string
 */
function sed_cc($text)
{
	$text = str_replace(
	array('{', '<', '>' , '$', '\'', '"', '\\', '&amp;', '&nbsp;'),
	array('&#123;', '&lt;', '&gt;', '&#036;', '&#039;', '&quot;', '&#92;', '&amp;amp;', '&amp;nbsp;'), $text);
	return $text;
}

/**
 * Checks GET anti-XSS parameter
 *
 * @return bool
 */
function sed_check_xg()
{
	global $xg, $cfg;

	if ($xg!=sed_sourcekey())
	{
		sed_diefatal('Wrong parameter in the URL.');
	}
	return TRUE;
}

/**
 * Checks POST anti-XSS parameter
 *
 * @return string
 */
function sed_check_xp()
{
	global $xp;

	$sk = sed_sourcekey();
	if($_SERVER["REQUEST_METHOD"]=='POST' && !defined('SED_AUTH'))
	{
		if ( empty($xp) || $xp!=$sk)
		{
			sed_diefatal('Wrong parameter in the URL.');
		}
	}
	return ($sk);
}

/**
 * Truncates a string
 *
 * @param string $res Source string
 * @param int $l Length
 * @return unknown
 */
function sed_cutstring($res, $l)
{
	global $cfg;
	if(mb_strlen($res)>$l)
	{
		$res = mb_substr($res, 0, ($l-3)).'...';
	}
	return $res;
}

/**
 * Creates image thumbnail
 *
 * @param string $img_big Original image path
 * @param string $img_small Thumbnail path
 * @param int $small_x Thumbnail width
 * @param int $small_y Thumbnail height
 * @param bool $keepratio Keep original ratio
 * @param string $extension Image type
 * @param string $filen Original file name
 * @param int $fsize File size in kB
 * @param string $textcolor Text color
 * @param int $textsize Text size
 * @param string $bgcolor Background color
 * @param int $bordersize Border thickness
 * @param int $jpegquality JPEG quality in %
 * @param string $dim_priority Resize priority dimension
 */
function sed_createthumb($img_big, $img_small, $small_x, $small_y, $keepratio, $extension, $filen, $fsize, $textcolor, $textsize, $bgcolor, $bordersize, $jpegquality, $dim_priority="Width")
{
	if (!function_exists('gd_info'))
	{ return; }

	global $cfg;

	$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

	switch($extension)
	{
		case 'gif':
			$source = imagecreatefromgif($img_big);
			break;

		case 'png':
			$source = imagecreatefrompng($img_big);
			break;

		default:
			$source = imagecreatefromjpeg($img_big);
			break;
	}

	$big_x = imagesx($source);
	$big_y = imagesy($source);

	if (!$keepratio)
	{
		$thumb_x = $small_x;
		$thumb_y = $small_y;
	}
	elseif ($dim_priority=="Width")
	{
		$thumb_x = $small_x;
		$thumb_y = floor($big_y * ($small_x / $big_x));
	}
	else
	{
		$thumb_x = floor($big_x * ($small_y / $big_y));
		$thumb_y = $small_y;
	}

	if ($textsize==0)
	{
		if ($cfg['th_amode']=='GD1')
		{ $new = imagecreate($thumb_x+$bordersize*2, $thumb_y+$bordersize*2); }
		else
		{ $new = imagecreatetruecolor($thumb_x+$bordersize*2, $thumb_y+$bordersize*2); }

		$background_color = imagecolorallocate ($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
		imagefilledrectangle ($new, 0,0, $thumb_x+$bordersize*2, $thumb_y+$bordersize*2, $background_color);

		if ($cfg['th_amode']=='GD1')
		{ imagecopyresized($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
		else
		{ imagecopyresampled($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }

	}
	else
	{
		if ($cfg['th_amode']=='GD1')
		{ $new = imagecreate($thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*3.5+6); }
		else
		{ $new = imagecreatetruecolor($thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*3.5+6); }

		$background_color = imagecolorallocate($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
		imagefilledrectangle ($new, 0,0, $thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*4+14, $background_color);
		$text_color = imagecolorallocate($new, $textcolor[0],$textcolor[1],$textcolor[2]);

		if ($cfg['th_amode']=='GD1')
		{ imagecopyresized($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
		else
		{ imagecopyresampled($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }

		imagestring ($new, $textsize, $bordersize, $thumb_y+$bordersize+$textsize+1, $big_x."x".$big_y." ".$fsize."kb", $text_color);
	}

	switch($extension)
	{
		case 'gif':
			imagegif($new, $img_small);
			break;

		case 'png':
			imagepng($new, $img_small);
			break;

		default:
			imagejpeg($new, $img_small, $jpegquality);
			break;
	}

	imagedestroy($new);
	imagedestroy($source);
}

/**
 * Terminates script execution and performs redirect
 *
 * @param bool $cond Really die?
 * @return bool
 */
function sed_die($cond=TRUE)
{
	if ($cond)
	{
		header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=950");
		exit;
	}
	return FALSE;
}

/**
 * Terminates script execution with fatal error
 *
 * @param string $text Reason
 * @param string $title Message title
 */
function sed_diefatal($text='Reason is unknown.', $title='Fatal error')
{
	global $cfg;

	$disp = "<strong><a href=\"".$cfg['mainurl']."\">".$cfg['maintitle']."</a></strong><br />";
	$disp .= @date('Y-m-d H:i').'<br />'.$title.' : '.$text;
	die($disp);
}

/**
 * Terminates with "disabled" error
 *
 * @param unknown_type $disabled
 */
function sed_dieifdisabled($disabled)
{
	if ($disabled)
	{
		header("Location: " . SED_ABSOLUTE_URL . "message.php?msg=940");
		exit;
	}
	return;
}

/*
 * ==================================== Forum Functions ==================================
 */

/**
 * Gets details for forum section
 *
 * @param int $id Section ID
 * @return mixed
 */
function sed_forum_info($id)
{
	global $db_forum_sections;

	$sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$id'");
	if($res = sed_sql_fetcharray($sql))
	{
		return ($res);
	}
	else
	{
		return ('');
	}
}

/**
 * Moves outdated topics to trash
 *
 * @param string $mode Selection criteria
 * @param int $section Section
 * @param int $param Selection parameter value
 * @return int
 */
function sed_forum_prunetopics($mode, $section, $param)
{
	global $cfg, $sys, $db_forum_topics, $db_forum_posts, $db_forum_sections, $L;

	$num = 0;
	$num1 = 0;

	switch ($mode)
	{
		case 'updated':
			$limit = $sys['now'] - ($param*86400);
			$sql1 = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_sectionid='$section' AND ft_updated<'$limit' AND ft_sticky='0'");
			break;

		case 'single':
			$sql1 = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_sectionid='$section' AND ft_id='$param'");
			break;
	}

	if (sed_sql_numrows($sql1)>0)
	{
		while ($row1 = sed_sql_fetchassoc($sql1))
		{
			$q = $row1['ft_id'];

			if ($cfg['trash_forum'])
			{
				$sql = sed_sql_query("SELECT * FROM $db_forum_posts WHERE fp_topicid='$q' ORDER BY fp_id DESC");

				while ($row = sed_sql_fetchassoc($sql))
				{ sed_trash_put('forumpost', $L['Post']." #".$row['fp_id']." from topic #".$q, "p".$row['fp_id']."-q".$q, $row); }
			}

			$sql = sed_sql_query("DELETE FROM $db_forum_posts WHERE fp_topicid='$q'");
			$num += sed_sql_affectedrows();

			if ($cfg['trash_forum'])
			{
				$sql = sed_sql_query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");

				while ($row = sed_sql_fetchassoc($sql))
				{ sed_trash_put('forumtopic', $L['Topic']." #".$q." (no post left)", "q".$q, $row); }
			}

			$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_id='$q'");
			$num1 += sed_sql_affectedrows();
		}

		$sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_movedto='$q'");
		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-'$num1', fs_postcount=fs_postcount-'$num', fs_topiccount_pruned=fs_topiccount_pruned+'$num1', fs_postcount_pruned=fs_postcount_pruned+'$num' WHERE fs_id='$section'");
	}
	$num1 = ($num1=='') ? '0' : $num1;
	return($num1);
}

/**
 * Changes last message for the section
 *
 * @param int $id Section ID
 */
function sed_forum_sectionsetlast($id)
{
	global $db_forum_topics, $db_forum_sections;
	// FIXME probably this function produces overhead, because lastest post/topic ID should have been known
	// by the time of its call
	$sql = sed_sql_query("SELECT ft_id, ft_lastposterid, ft_lastpostername, ft_updated, ft_title, ft_poll FROM $db_forum_topics WHERE ft_sectionid='$id' AND ft_movedto='0' and ft_mode='0' ORDER BY ft_updated DESC LIMIT 1");
	$row = sed_sql_fetcharray($sql);
	$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_lt_id=".(int)$row['ft_id'].", fs_lt_title='".sed_sql_prep($row['ft_title'])."', fs_lt_date=".(int)$row['ft_updated'].", fs_lt_posterid=".(int)$row['ft_lastposterid'].", fs_lt_postername='".sed_sql_prep($row['ft_lastpostername'])."' WHERE fs_id='$id'");
	return;
}

/**
 * Returns a list of plugins registered for a hook
 *
 * @param string $hook Hook name
 * @param string $cond Permissions
 * @return array
 */
function sed_getextplugins($hook, $cond='R')
{
	global $sed_plugins, $usr;

	if (is_array($sed_plugins))
	{
		foreach($sed_plugins as $i => $k)
		{
			if($k['pl_hook']==$hook && sed_auth('plug', $k['pl_code'], $cond))
			{
				$extplugins[$i] = $k;
			}
		}
	}
	return $extplugins;
}

/**
 * Returns number of comments for item
 *
 * @param string $code Item code
 * @return int
 */
function sed_get_comcount($code)
{
	global $db_com;

	$sql = sed_sql_query("SELECT DISTINCT com_code, COUNT(*) FROM $db_com WHERE com_code='$code' GROUP BY com_code");

	if ($row = sed_sql_fetcharray($sql))
	{
		return (int) $row['COUNT(*)'];
	}
	else
	{
		return 0;
	}
}

/* ------------------ */
// FIXME this function is obsolete, or meta/title generation must be reworked
function sed_htmlmetas()
{
	global $cfg;
	$contenttype = ($cfg['doctypeid']>2 && $cfg['xmlclient']) ? "application/xhtml+xml" : "text/html";
	$result = "<meta http-equiv=\"content-type\" content=\"".$contenttype."; charset=".$cfg['charset']."\" />
<meta name=\"description\" content=\"".$cfg['maintitle']." - ".$cfg['subtitle']."\" />
<meta name=\"keywords\" content=\"".$cfg['metakeywords']."\" />
<meta name=\"generator\" content=\"Seditio by Neocrome http://www.neocrome.net\" />
<meta http-equiv=\"expires\" content=\"Fri, Apr 01 1974 00:00:00 GMT\" />
<meta http-equiv=\"pragma\" content=\"no-cache\" />
<meta http-equiv=\"cache-control\" content=\"no-cache\" />
<meta http-equiv=\"last-modified\" content=\"".gmdate("D, d M Y H:i:s")." GMT\" />
<link rel=\"shortcut icon\" href=\"favicon.ico\" />
";
	return ($result);
}


/**
 * Imports data from the outer world
 *
 * @param string $name Variable name
 * @param string $source Source type: G (GET), P (POST), C (COOKIE) or D (variable filtering)
 * @param string $filter Filter type
 * @param int $maxlen Length limit
 * @param bool $dieonerror Die with fatal error on wrong input
 * @return mixed
 */
function sed_import($name, $source, $filter, $maxlen=0, $dieonerror=FALSE)
{
	switch($source)
	{
		case 'G':
			$v = $_GET[$name];
			$log = TRUE;
			break;

		case 'P':
			$v = $_POST[$name];
			$log = TRUE;
			if ($filter=='ARR') { return($v); }
			break;

		case 'C':
			$v = $_COOKIE[$name];
			$log = TRUE;
			break;

		case 'D':
			$v = $name;
			$log = FALSE;
			break;

		default:
			sed_diefatal('Unknown source for a variable : <br />Name = '.$name.'<br />Source = '.$source.' ? (must be G, P, C or D)');
			break;
	}

	if (MQGPC && ($source=='G' || $source=='P' || $source=='C') )
	{
		$v = stripslashes($v);
	}

	if ($v=='')
	{
		return('');
	}

	if ($maxlen>0)
	{
		$v = mb_substr($v, 0, $maxlen);
	}

	$pass = FALSE;
	$defret = NULL;
	$filter = ($filter=='STX') ? 'TXT' : $filter;

	switch($filter)
	{
		case 'INT':
			if (is_numeric($v) && floor($v)==$v)
			{
				$pass = TRUE;
			}
		break;

		case 'NUM':
			if(is_numeric($v))
			{
				$pass = TRUE;
			}
		break;

		case 'TXT':
			$v = trim($v);
			if (mb_strpos($v, '<')===FALSE)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = str_replace('<', '&lt;', $v);
			}
		break;

		case 'SLU':
			$v = trim($v);
			$f = preg_replace('/[^a-zA-Z0-9_=\/]/', '', $v);
			if($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = '';
			}
		break;

		case 'ALP':
			$v = trim($v);
			$f = sed_alphaonly($v);
			if($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = $f;
			}
		break;

		case 'PSW':
			$v = trim($v);
			$f = sed_alphaonly($v);
			$f = mb_substr($f, 0 ,32);

			if ($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = $f;
			}
		break;

		case 'HTM':
			$v = trim($v);
			$pass = TRUE;
		break;

		case 'ARR':
			$pass = TRUE;
		break;

		case 'BOL':
			if($v == '1' || $v == 'on')
			{
				$pass = TRUE;
				$v = '1';
			}
			elseif($v=='0' || $v=='off')
			{
				$pass = TRUE;
				$v = '0';
			}
			else
			{
				$defret = '0';
			}
			break;

		case 'LVL':
			if(is_numeric($v) && $v >= 0 && $v <= 100 && floor($v)==$v)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = NULL;
			}
			break;

		case 'NOC':
			$pass = TRUE;
			break;

		default:
			sed_diefatal('Unknown filter for a variable : <br />Var = '.$cv_v.'<br />Filter = '.$filter.' ?');
			break;
	}

	$v = preg_replace('/(&#\d+)(?![\d;])/', '$1;', $v);
	if($pass)
	{
		return($v);
	}
	else
	{
		if($log)
		{
			sed_log_sed_import($source, $filter, $name, $v);
		}
		if($dieonerror)
		{
			sed_diefatal('Wrong input.');
		}
		else
		{
			return($defret);
		}
	}
}


/**
 * Extract info from SED file headers
 *
 * @param string $file File path
 * @param string $limiter Tag name
 * @param int $maxsize Max header size
 * @return array
 */
function sed_infoget($file, $limiter='SED', $maxsize=32768)
{
	$result = array();

	if($fp = @fopen($file, 'r'))
	{
		$limiter_begin = "[BEGIN_".$limiter."]";
		$limiter_end = "[END_".$limiter."]";
		$data = fread($fp, $maxsize);
		$begin = mb_strpos($data, $limiter_begin);
		$end = mb_strpos($data, $limiter_end);

		if ($end>$begin && $begin>0)
		{
			$lines = mb_substr($data, $begin+8+mb_strlen($limiter), $end-$begin-mb_strlen($limiter)-8);
			$lines = explode ("\n",$lines);

			foreach ($lines as $k => $line)
			{
				$linex = explode ("=", $line);
				$ii=1;
				while (!empty($linex[$ii]))
				{
					$result[$linex[0]] .= trim($linex[$ii]);
					$ii++;
				}
			}
		}
		else
		{ $result['Error'] = 'Warning: No tags found in '.$file; }
	}
	else
	{ $result['Error'] = 'Error: File '.$file.' is missing!'; }
	@fclose($fp);
	return ($result);
}

/**
 * Outputs standard javascript
 *
 * @param string $more Extra javascript
 * @return string
 */
function sed_javascript($more='')
{
	$result = <<<END
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/base.js"></script>
<script type="text/javascript">
<!--
$more
//-->
</script>
END;
	return $result;
}

/**
 * Loads comlete category structure into array
 *
 * @return array
 */
function sed_load_structure()
{
	global $db_structure, $cfg, $L;

	$res = array();
	$sql = sed_sql_query("SELECT * FROM $db_structure ORDER BY structure_path ASC");

	while ($row = sed_sql_fetcharray($sql))
	{
		if (!empty($row['structure_icon']))
		{ $row['structure_icon'] = "<img src=\"".$row['structure_icon']."\" alt=\"\" />"; }

		$path2 = mb_strrpos($row['structure_path'], '.');

		$row['structure_tpl'] = (empty($row['structure_tpl'])) ? $row['structure_code'] : $row['structure_tpl'];

		if ($path2>0)
		{
			$path1 = mb_substr($row['structure_path'],0,($path2));
			$path[$row['structure_path']] = $path[$path1].'.'.$row['structure_code'];
			$tpath[$row['structure_path']] = $tpath[$path1].' '.$cfg['separator'].' '.$row['structure_title'];
			$row['structure_tpl'] = ($row['structure_tpl']=='same_as_parent') ? $parent_tpl : $row['structure_tpl'];
		}
		else
		{
			$path[$row['structure_path']] = $row['structure_code'];
			$tpath[$row['structure_path']] = $row['structure_title'];
		}

		$order = explode('.',$row['structure_order']);
		$parent_tpl = $row['structure_tpl'];

		$res[$row['structure_code']] = array (
			'path' => $path[$row['structure_path']],
			'tpath' => $tpath[$row['structure_path']],
			'rpath' => $row['structure_path'],
			'tpl' => $row['structure_tpl'],
			'title' => $row['structure_title'],
			'desc' => $row['structure_desc'],
			'icon' => $row['structure_icon'],
			'group' => $row['structure_group'],
			'order' => $order[0],
			'way' => $order[1]
		);
	}

	return($res);
}

/**
 * Loads complete forum structure into array
 *
 * @return array
 */
function sed_load_forum_structure()
{
	global $db_forum_structure, $cfg, $L;

	$res = array();
	$sql = sed_sql_query("SELECT * FROM $db_forum_structure ORDER BY fn_path ASC");

	while ($row = sed_sql_fetcharray($sql))
	{
		if (!empty($row['fn_icon']))
		{ $row['fn_icon'] = "<img src=\"".$row['fn_icon']."\" alt=\"\" />"; }

		$path2 = mb_strrpos($row['fn_path'], '.');

		$row['fn_tpl'] = (empty($row['fn_tpl'])) ? $row['fn_code'] : $row['fn_tpl'];

		if ($path2>0)
		{
			$path1 = mb_substr($row['fn_path'],0,($path2));
			$path[$row['fn_path']] = $path[$path1].'.'.$row['fn_code'];
			$tpath[$row['fn_path']] = $tpath[$path1].' '.$cfg['separator'].' '.$row['fn_title'];
			$row['fn_tpl'] = ($row['fn_tpl']=='same_as_parent') ? $parent_tpl : $row['fn_tpl'];
		}
		else
		{
			$path[$row['fn_path']] = $row['fn_code'];
			$tpath[$row['fn_path']] = $row['fn_title'];
		}

		$parent_tpl = $row['fn_tpl'];

		$res[$row['fn_code']] = array (
			'path' => $path[$row['fn_path']],
			'tpath' => $tpath[$row['fn_path']],
			'rpath' => $row['fn_path'],
			'tpl' => $row['fn_tpl'],
			'title' => $row['fn_title'],
			'desc' => $row['fn_desc'],
			'icon' => $row['fn_icon'],
			'defstate' => $row['fn_defstate']
		);
	}

	return($res);
}

/**
 * Logs an event
 *
 * @param string $text Event description
 * @param string $group Event group
 */
function sed_log($text, $group='def')
{
	global $db_logger, $sys, $usr, $_SERVER;

	$sql = sed_sql_query("INSERT INTO $db_logger (log_date, log_ip, log_name, log_group, log_text) VALUES (".(int)$sys['now_offset'].", '".$usr['ip']."', '".sed_sql_prep($usr['name'])."', '$group', '".sed_sql_prep($text.' - '.$_SERVER['REQUEST_URI'])."')");
	return;
}

/**
 * Logs wrong input
 *
 * @param string $s Source type
 * @param string $e Filter type
 * @param string $v Variable name
 * @param string $o Value
 */
function sed_log_sed_import($s, $e, $v, $o)
{
	$text = "A variable type check failed, expecting ".$s."/".$e." for '".$v."' : ".$o;
	sed_log($text, 'sec');
	return;
}

/**
 * Sends mail with standard PHP mail()
 *
 * @global $cfg
 * @param string $fmail Recipient
 * @param string $subject Subject
 * @param string $body Message body
 * @param string $headers Message headers
 * @param string $additional_parameters Additional parameters passed to sendmail
 * @return bool
 */
function sed_mail($fmail, $subject, $body, $headers='', $additional_parameters = null)
{
	global $cfg;

	if(empty($fmail))
	{
		return(FALSE);
	}
	else
	{
		$headers = (empty($headers)) ? "From: \"".$cfg['maintitle']."\" <".$cfg['adminemail'].">\n"."Reply-To: <".$cfg['adminemail'].">\n"."Content-Type: text/plain; charset=".$cfg['charset']."\n" : $headers;
		$body .= "\n\n".$cfg['maintitle']." - ".$cfg['mainurl']."\n".$cfg['subtitle'];
		mail($fmail, $subject, $body, $headers, $additional_parameters);
		sed_stat_inc('totalmailsent');
		return(TRUE);
	}
}

/**
 * Creates UNIX timestamp out of a date
 *
 * @param int $hour Hours
 * @param int $minute Minutes
 * @param int $second Seconds
 * @param int $month Month
 * @param int $date Day of the month
 * @param int $year Year
 * @return int
 */
function sed_mktime($hour = false, $minute = false, $second = false, $month = false, $date = false, $year = false)
{
	// Code from http://www.php.net/date
	// Author rickenmeer at hotmail dot com
	// 12-Jan-2004 12:30

	if ($hour === false)  $hour  = Date ('G');
	if ($minute === false) $minute = Date ('i');
	if ($second === false) $second = Date ('s');
	if ($month === false)  $month  = Date ('n');
	if ($date === false)  $date  = Date ('j');
	if ($year === false)  $year  = Date ('Y');

	if ($year >= 1970) return mktime ($hour, $minute, $second, $month, $date, $year);

	$m_days = Array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if ($year % 4 == 0 && ($year % 100 > 0 || $year % 400 == 0))
	{ $m_days[1] = 29;  }

	$d_year = 1970 - $year;
	$days = -1 - $d_year * 365;
	$days -= floor ($d_year / 4);
	$days += floor (($d_year - 70) / 100);
	$days -= floor (($d_year - 370) / 400);

	for ($i = 1; $i < $month; $i++)
	{ $days += $m_days [$i - 1]; }
	$days += $date - 1;

	$stamp = $days * 86400;
	$stamp += $hour * 3600;
	$stamp += $minute * 60;
	$stamp += $second;

	return $stamp;
}

/**
 * Standard SED output filters, adds XSS protection to forms
 *
 * @param unknown_type $output
 * @return unknown
 */
function sed_outputfilters($output)
{
	global $cfg;

	/* === Hook === */
	$extp = sed_getextplugins('output');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ==== */

	$output = str_replace('</FORM>', '</form>', $output);
	$output = str_replace('</form>', sed_xp().'</form>', $output);

	return($output);
}

/**
 * Renders page navigation bar
 *
 * @param string $url Basic URL
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @return string
 */
function sed_pagination($url, $current, $entries, $perpage)
{
	global $cfg;

	if ($entries<=$perpage)
	{ return (""); }

	$totalpages = ceil($entries / $perpage);
	$currentpage = $current / $perpage;

	for ($i = 0; $i < $totalpages; $i++)
	{
		$j = $i * $perpage;
		if ($i==$currentpage)
		{ $res .= sprintf($cfg['pagination_cur'], "<a href=\"".$url."&amp;d=".$j."\">".($i+1)."</a>"); }
		elseif (is_int(($i+1)/10) || $i<4 || ($totalpages-$i)<4 || abs($i-$currentpage)<4)
		{ $res .= sprintf($cfg['pagination'], "<a href=\"".$url."&amp;d=".$j."\">".($i+1)."</a>"); }
	}
	return ($res);
}

/**
 * Renders page navigation previous/next buttons
 *
 * @param string $url Basic URL
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param bool $res_array Return results as array
 * @return mixed
 */
function sed_pagination_pn($url, $current, $entries, $perpage, $res_array=FALSE)
{
	global $L, $sed_img_left, $sed_img_right;

	if ($current>0)
	{
		$prevpage = $current - $perpage;
		if ($prevpage<0)
		{ $prevpage = 0; }
		$res_l = "<a href=\"".$url."&amp;d=".$prevpage."\">".$L['Previous']." $sed_img_left</a>";
	}

	if (($current + $perpage)<$entries)
	{
		$nextpage = $current + $perpage;
		$res_r = "<a href=\"".$url."&amp;d=".$nextpage."\">$sed_img_right ".$L['Next']."</a>";
	}
	if ($res_array)
	{ return (array($res_l, $res_r)); }
	else
	{ return ($res_l." ".$res_r); }
}

/**
 * Delete all PFS files for a specific user. Returns number of items removed.
 *
 * @param int $userid User ID
 * @return int
 */
function sed_pfs_deleteall($userid)
{
	global $db_pfs_folders, $db_pfs, $cfg;

	if (!$userid)
	{ return; }
	$sql = sed_sql_query("DELETE FROM $db_pfs_folders WHERE pff_userid='$userid'");
	$num = $num + sed_sql_affectedrows();
	$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_userid='$userid'");
	$num = $num + sed_sql_affectedrows();

	$cfg['pfs_dir_user'] = sed_pfs_path($userid);
	$cfg['th_dir_user'] = sed_pfs_thumbpath($userid);

	$bg = $userid.'-';
	$bgl = mb_strlen($bg);

	$handle = @opendir($cfg['pfs_dir_user']);
	while ($f = @readdir($handle))
	{
		if ($cfg['pfsuserfolder'])
		{ @unlink($cfg['pfs_dir_user'].$f);
		}
		elseif (mb_substr($f, 0, $bgl)==$bg)
		{ @unlink($cfg['pfs_dir_user'].$f); }
	}
	@closedir($handle);

	$handle = @opendir($cfg['th_dir_user']);
	while ($f = @readdir($handle))
	{
		if ($cfg['pfsuserfolder'])
		{ @unlink($cfg['th_dir_user'].$f); }
		elseif (mb_substr($f, 0, $bgl)==$bg)
		{ @unlink($cfg['th_dir_user'].$f); }
	}
	@closedir($handle);

	if ($cfg['pfsuserfolder'] && $userid>0)
	{
		@rmdir($cfg['pfs_dir_user']);
		@rmdir($cfg['th_dir_user']);
	}

	return($num);
}

/**
 * Returns PFS path for a user, relative from site root
 *
 * @param int $userid User ID
 * @return string
 */
function sed_pfs_path($userid)
{
	global $cfg;

	if ($cfg['pfsuserfolder'])
	{ return($cfg['pfs_dir'].$userid.'/'); }
	else
	{ return($cfg['pfs_dir']); }
}

/**
 * Returns PFS path for a user, relative from PFS root
 *
 * @param int $userid User ID
 * @return string
 */
function sed_pfs_relpath($userid)
{
	global $cfg;

	if ($cfg['pfsuserfolder'])
	{ return($userid.'/'); }
	else
	{ return(''); }
}

/**
 * Returns absolute path
 *
 * @param unknown_type $userid
 * @return unknown
 */
function sed_pfs_thumbpath($userid)
{
	global $cfg;

	if ($cfg['pfsuserfolder'])
	{ return($cfg['th_dir'].$userid.'/'); }
	else
	{ return($cfg['th_dir']); }
}

/**
 * Reads raw data from file
 *
 * @param string $file File path
 * @return string
 */
function sed_readraw($file)
{
	if(file_exists($file))
	{
		return file_get_contents($file);
	}
	else
	{
		return 'File not found : '.$file;
	}
}

/**
 * Displays redirect page
 *
 * @param string $url Target URI
 */
function sed_redirect($url)
{
	global $cfg;

	if ($cfg['redirmode'])
	{
		$output = $cfg['doctype']."
		<html>
		<head>
		<meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\" />
		<meta http-equiv=\"refresh\" content=\"0; url=".SED_ABSOLUTE_URL . $url."\" />
		<title>Redirecting...</title></head>
		<body>Redirecting to <a href=\"". SED_ABSOLUTE_URL .$url."\">".$cfg['mainurl']."/".$url."</a>
		</body>
		</html>";
		header("Refresh: 0; URL=". SED_ABSOLUTE_URL .$url);
		echo($output);
		exit;
	}
	else
	{
		header("Location: " . SED_ABSOLUTE_URL . $url);
		exit;
	}
	return;
}

/**
 * Renders a dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param array $values Options available
 * @return string
 */
function sed_selectbox($check, $name, $values)
{
	$check = trim($check);
	$values = explode(',', $values);
	$selected = (empty($check) || $check=="00") ? "selected=\"selected\"" : '';
	$result =  "<select name=\"$name\" size=\"1\"><option value=\"\" $selected>---</option>";
	foreach ($values as $k => $x)
	{
		$x = trim($x);
		$selected = ($x == $check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$x\" $selected>".sed_cc($x)."</option>";
	}
	$result .= "</select>";
	return($result);
}

/**
 * Renders category dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param bool $hideprivate Hide private categories
 * @return string
 */
function sed_selectbox_categories($check, $name, $hideprivate=TRUE)
{
	global $db_structure, $usr, $sed_cat, $L;

	$result =  "<select name=\"$name\" size=\"1\">";

	foreach($sed_cat as $i => $x)
	{
		$display = ($hideprivate) ? sed_auth('page', $i, 'W') : TRUE;

		if (sed_auth('page', $i, 'R') && $i!='all' && $display)
		{
			$selected = ($i==$check) ? "selected=\"selected\"" : '';
			$result .= "<option value=\"".$i."\" $selected> ".$x['tpath']."</option>";
		}
	}
	$result .= "</select>";
	return($result);
}

/**
 * Renders country dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_countries($check,$name)
{
	global $sed_countries;

	$selected = (empty($check) || $check=='00') ? "selected=\"selected\"" : '';
	$result =  "<select name=\"$name\" size=\"1\">";
	foreach($sed_countries as $i => $x)
	{
		$selected = ($i==$check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>".$x."</option>";
	}
	$result .= "</select>";

	return($result);
}

/**
 * Generates date part dropdown
 *
 * @param int $utime Selected timestamp
 * @param string $mode Display mode: 'short' or complete
 * @param string $ext Variable name suffix
 * @return string
 */
function sed_selectbox_date($utime, $mode, $ext='')
{
	global $L;
	list($s_year, $s_month, $s_day, $s_hour, $s_minute) = explode('-', @date('Y-m-d-H-i', $utime));
	$p_monthes = array();
	$p_monthes[] = array(1, $L['January']);
	$p_monthes[] = array(2, $L['February']);
	$p_monthes[] = array(3, $L['March']);
	$p_monthes[] = array(4, $L['April']);
	$p_monthes[] = array(5, $L['May']);
	$p_monthes[] = array(6, $L['June']);
	$p_monthes[] = array(7, $L['July']);
	$p_monthes[] = array(8, $L['August']);
	$p_monthes[] = array(9, $L['September']);
	$p_monthes[] = array(10, $L['October']);
	$p_monthes[] = array(11, $L['November']);
	$p_monthes[] = array(12, $L['December']);

	$result = "<select name=\"ryear".$ext."\">";
	for ($i = 1902; $i<2030; $i++)
	{
		$selected = ($i==$s_year) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>$i</option>";
	}
	$result .= ($utime==0) ? "<option value=\"0\" selected=\"selected\">---</option>" : "<option value=\"0\">---</option>";

	$result .= "</select><select name=\"rmonth".$ext."\">";
	reset($p_monthes);
	foreach ($p_monthes as $k => $line)
	{
		$selected = ($line[0]==$s_month) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"".$line[0]."\" $selected>".$line[1]."</option>";
	}
	$result .= ($utime==0) ? "<option value=\"0\" selected=\"selected\">---</option>" : "<option value=\"0\">---</option>";

	$result .= "</select><select name=\"rday".$ext."\">";
	for ($i = 1; $i<32; $i++)
	{
		$selected = ($i==$s_day) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>$i</option>";
	}
	$result .= ($utime==0) ? "<option value=\"0\" selected=\"selected\">---</option>" : "<option value=\"0\">---</option>";
	$result .= "</select> ";

	if ($mode=='short')
	{ return ($result); }

	$result .= " <select name=\"rhour".$ext."\">";
	for ($i = 0; $i<24; $i++)
	{
		$selected = ($i==$s_hour) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>".sprintf("%02d",$i)."</option>";
	}
	$result .= ($utime==0) ? "<option value=\"0\" selected=\"selected\">---</option>" : "<option value=\"0\">---</option>";

	$result .= "</select>:<select name=\"rminute".$ext."\">";
	for ($i = 0; $i<60; $i=$i+1)
	{
		$selected = ($i==$s_minute) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>".sprintf("%02d",$i)."</option>";
	}
	$result .= ($utime==0) ? "<option value=\"0\" selected=\"selected\">---</option>" : "<option value=\"0\">---</option>";
	$result .= "</select>";

	return ($result);
}

/**
 * Renders PFS folder selection dropdown
 *
 * @param int $user User ID
 * @param int $skip Skip folder
 * @param int $check Checked folder
 * @return string
 */
function sed_selectbox_folders($user, $skip, $check)
{
	global $db_pfs_folders;

	$sql = sed_sql_query("SELECT pff_id, pff_title, pff_isgallery, pff_ispublic FROM $db_pfs_folders WHERE pff_userid='$user' ORDER BY pff_title ASC");

	$result =  "<select name=\"folderid\" size=\"1\">";

	if ($skip!="/" && $skip!="0")
	{
		$selected = (empty($check) || $check=="/") ? "selected=\"selected\"" : '';
		$result .=  "<option value=\"0\" $selected>/ &nbsp; &nbsp;</option>";
	}

	while ($row = sed_sql_fetcharray($sql))
	{
		if ($skip!=$row['pff_id'])
		{
			$selected = ($row['pff_id']==$check) ? "selected=\"selected\"" : '';
			$result .= "<option value=\"".$row['pff_id']."\" $selected>".sed_cc($row['pff_title'])."</option>";
		}
	}
	$result .= "</select>";
	return ($result);
}

/**
 * Returns forum category dropdown code
 *
 * @param int $check Selected category
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_forumcat($check, $name)
{
	global $usr, $sed_forums_str, $L;

	$result =  "<select name=\"$name\" size=\"1\">";

	foreach($sed_forums_str as $i => $x)
	{
		$selected = ($i==$check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"".$i."\" $selected> ".$x['tpath']."</option>";
	}
	$result .= "</select>";
	return($result);
}


/**
 * Generates gender dropdown
 *
 * @param string $check Checked gender
 * @param string $name Input name
 * @return string
 */
function sed_selectbox_gender($check,$name)
{
	global $L;

	$genlist = array ('U', 'M', 'F');
	$result =  "<select name=\"$name\" size=\"1\">";
	foreach(array ('U', 'M', 'F') as $i)
	{
		$selected = ($i==$check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"$i\" $selected>".$L['Gender_'.$i]."</option>";
	}
	$result .= "</select>";
	return($result);
}

/**
 * Returns group selection dropdown code
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param array $skip Hidden groups
 * @return string
 */
function sed_selectbox_groups($check, $name, $skip=array(0))
{
	global $sed_groups;

	$res = "<select name=\"$name\" size=\"1\">";

	foreach($sed_groups as $k => $i)
	{
		$selected = ($k==$check) ? "selected=\"selected\"" : '';
		$res .= (in_array($k, $skip)) ? '' : "<option value=\"$k\" $selected>".$sed_groups[$k]['title']."</option>";
	}
	$res .= "</select>";

	return($res);
}

/**
 * Returns language selection dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_lang($check, $name)
{
	global $sed_languages, $sed_countries, $cfg;

	$handle = opendir($cfg['system_dir'].'/lang/');
	while ($f = readdir($handle))
	{
		if ($f[0] != '.')
		{ $langlist[] = $f; }
	}
	closedir($handle);
	sort($langlist);

	$result = "<select name=\"$name\" size=\"1\">";
	while(list($i,$x) = each($langlist))
	{
		$selected = ($x==$check) ? "selected=\"selected\"" : '';
		$lng = (empty($sed_languages[$x])) ? $sed_countries[$x] : $sed_languages[$x];
		$result .= "<option value=\"$x\" $selected>".$lng." (".$x.")</option>";
	}
	$result .= "</select>";

	return($result);
}

/**
 * Renders forum section selection dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_sections($check, $name)
{
	global $db_forum_sections, $cfg;

	$sql = sed_sql_query("SELECT fs_id, fs_title, fs_category FROM $db_forum_sections WHERE 1 ORDER by fs_order ASC");
	$result = "<select name=\"$name\" size=\"1\">";
	while ($row = sed_sql_fetcharray($sql))
	{
		$selected = ($row['fs_id'] == $check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"".$row['fs_id']."\" $selected>".sed_cc(sed_cutstring($row['fs_category'], 24));
		$result .= ' '.$cfg['separator'].' '.sed_cc(sed_cutstring($row['fs_title'], 32));
	}
	$result .= "</select>";
	return($result);
}

/**
 * Returns skin selection dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_skin($check, $name)
{
	$handle = opendir("skins/");
	while ($f = readdir($handle))
	{
		if (mb_strpos($f, '.')  === FALSE)
		{ $skinlist[] = $f; }
	}
	closedir($handle);
	sort($skinlist);

	$result = "<select name=\"$name\" size=\"1\">";
	while(list($i,$x) = each($skinlist))
	{
		$selected = ($x==$check) ? "selected=\"selected\"" : '';
		$skininfo = "skins/".$x."/".$x.".php";
		if (file_exists($skininfo))
		{
			$info = sed_infoget($skininfo);
			$result .= (!empty($info['Error'])) ? "<option value=\"$x\" $selected>".$x." (".$info['Error'].")" : "<option value=\"$x\" $selected>".$info['Name'];
		}
		else
		{ $result .= "<option value=\"$x\" $selected>".$x; }
		$result .= "</option>";
	}
	$result .= "</select>";

	return($result);
}

/**
 * Gets huge user selection box
 *
 * @param int $to Selected user ID
 * @return string
 */
function sed_selectbox_users($to)
{
	global $db_users;

	$result = "<select name=\"userid\">";
	$sql = sed_sql_query("SELECT user_id, user_name FROM $db_users ORDER BY user_name ASC");
	while ($row = sed_sql_fetcharray($sql))
	{
		$selected = ($row['user_id']==$to) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"".$row['user_id']."\" $selected>".sed_cc($row['user_name'])."</option>";
	}
	$result .= "</select>";
	return($result);
}

/**
 * Sends standard HTTP headers and disables browser cache
 *
 * @return bool
 */
function sed_sendheaders()
{
	global $cfg;
	$contenttype = ($cfg['doctypeid']>2 && $cfg['xmlclient']) ? 'application/xhtml+xml' : 'text/html';
	header('Expires: Fri, Apr 01 1974 00:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0,pre-check=0', FALSE);
	header('Content-Type: '.$contenttype);
	header('Cache-Control: no-store,no-cache,must-revalidate');
	header('Cache-Control: post-check=0,pre-check=0', FALSE);
	header('Pragma: no-cache');
	return(TRUE);
}

/* ------------------ */
// TODO this function is obsolete, doctype should be set in header.tpl
function sed_setdoctype($type)
{
	switch($type)
	{
		case '0': // HTML 4.01
			return ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">");
			break;

		case '1': // HTML 4.01 Transitional
			return ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">");
			break;

		case '2': // HTML 4.01 Frameset
			return ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">");
			break;

		case '3': // XHTML 1.0 Strict
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
			break;

		case '4': // XHTML 1.0 Transitional
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
			break;

		case '5': // XHTML 1.0 Frameset
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">");
			break;

		case '6': // XHTML 1.1
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">");
			break;

		case '7': // XHTML 2  ;]
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 2//EN\" \"http://www.w3.org/TR/xhtml2/DTD/xhtml2.dtd\">");
			break;

		default: // ...
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
			break;
	}
}

/**
 * Clears current user action in Who's online.
 *
 */
function sed_shield_clearaction()
{
	global  $db_online, $usr;

	$sql = sed_sql_query("UPDATE $db_online SET online_action='' WHERE online_ip='".$usr['ip']."'");
}

/**
 * Anti-hammer protection
 *
 * @param int $hammer Hammer rate
 * @param string $action Action type
 * @param int $lastseen User last seen timestamp
 * @return int
 */
function sed_shield_hammer($hammer,$action, $lastseen)
{
	global $cfg, $sys, $usr;

	if ($action=='Hammering')
	{
		sed_shield_protect();
		sed_shield_clearaction();
		sed_stat_inc('totalantihammer');
	}

	if (($sys['now']-$lastseen)<4)
	{
		$hammer++;
		if($hammer>$cfg['shieldzhammer'])
		{
			sed_shield_update(180, 'Hammering');
			sed_log('IP banned 3 mins, was hammering', 'sec');
			$hammer = 0;
		}
	}
	else
	{
		if ($hammer>0)
		{ $hammer--; }
	}
	return($hammer);
}

/**
 * Warn user of shield protection
 *
 */
function sed_shield_protect()
{
	global $cfg, $sys, $online_count, $shield_limit, $shield_action;

	if ($cfg['shieldenabled'] && $online_count>0 && $shield_limit>$sys['now'])
	{
		sed_diefatal('Shield protection activated, please retry in '.($shield_limit-$sys['now']).' seconds...<br />After this duration, you can refresh the current page to continue.<br />Last action was : '.$shield_action);
	}
}

/**
 * Updates shield state
 *
 * @param int $shield_add Hammer
 * @param string $shield_newaction New action type
 */
function sed_shield_update($shield_add, $shield_newaction)
{
	global $cfg, $usr, $sys, $db_online;
	if ($cfg['shieldenabled'])
	{
		$shield_newlimit = $sys['now'] + floor($shield_add * $cfg['shieldtadjust'] /100);
		$sql = sed_sql_query("UPDATE $db_online SET online_shield='$shield_newlimit', online_action='$shield_newaction' WHERE online_ip='".$usr['ip']."'");
	}
}

/**
 * Returns skin file path
 *
 * @param string $base Item name
 * @return string
 */
function sed_skinfile($base, $plug = false)
{
	global $usr, $cfg;
	if($plug)
	{
		$bname = mb_strstr($base, '.') ? mb_substr($base, 0, mb_strpos($base, '.')) : $base;
		if(file_exists('./skins/'.$usr['skin'].'/plugin.standalone.'.$base.'.tpl'))
		{
			return './skins/'.$usr['skin'].'/plugin.standalone.'.$base.'.tpl';
		}
		elseif(file_exists('./skins/'.$usr['skin'].'/'.$base.'.tpl'))
		{
			return './skins/'.$usr['skin'].'/'.$base.'.tpl';
		}
		elseif(file_exists('./skins/'.$cfg['defaultskin'].'/plugin.standalone.'.$base.'.tpl'))
		{
			return './skins/'.$cfg['defaultskin'].'/plugin.standalone.'.$base.'.tpl';
		}
		elseif(file_exists('skins/'.$cfg['defaultskin'].'/'.$base.'.tpl'))
		{
			return 'skins/'.$cfg['defaultskin'].'/'.$base.'.tpl';
		}
		elseif(file_exists($cfg['plugins_dir'].'/'.$bname.'/tpl/'.$base.'.tpl'))
		{
			return $cfg['plugins_dir'].'/'.$bname.'/tpl/'.$base.'.tpl';
		}
		else
		{
			return $cfg['plugins_dir'].'/'.$bname.'/'.$base.'.tpl';
		}
	}
	$base_depth = count($base);
	if($base_depth==1)
	{
		if(file_exists('skins/'.$usr['skin'].'/'.$base.'.tpl'))
		{
			return 'skins/'.$usr['skin'].'/'.$base.'.tpl';
		}
		else
		{
			return 'skins/'.$cfg['defaultskin'].'/'.$base.'.tpl';
		}

	}

	for($i=$base_depth; $i>1; $i--)
	{
		$levels = array_slice($base, 0, $i);
		$skinfile = 'skins/'.$usr['skin'].'/'.implode('.', $levels).'.tpl';
		if(file_exists($skinfile))
		{
			return $skinfile;
		}
		$skinfile = 'skins/'.$cfg['defaultskin'].'/'.implode('.', $levels).'.tpl';
		if(file_exists($skinfile))
		{
			return $skinfile;
		}
	}
	return 'skins/'.$usr['skin'].'/'.$base[0].'.tpl';
}


/**
 * Parses smiles in text
 *
 * @param string $res Source text
 * @return string
 */
function sed_smilies($res)
{
	global $sed_smilies;

	if (is_array($sed_smilies))
	{
		foreach($sed_smilies as $k => $v)
		{ $res = str_replace($v['smilie_code'],"<img src=\"".$v['smilie_image']."\" alt=\"\" />", $res); }
	}
	return($res);
}

/**
 * Gets XSS protection code
 *
 * @return string
 */
function sed_sourcekey()
{
	global $usr;

	$result = ($usr['id']>0) ? mb_strtoupper(mb_substr($usr['sessionid'], 0, 6)) : 'GUEST';
	return ($result);
}

/*
 * ===================================== Statistics API ==========================================
 */

/**
 * Creates new stats parameter
 *
 * @param string $name Parameter name
 */
function sed_stat_create($name)
{
	global $db_stats;

	$sql = sed_sql_query("INSERT INTO $db_stats (stat_name, stat_value) VALUES ('".sed_sql_prep($name)."', 1)");
	return;
}

/**
 * Returns statistics parameter
 *
 * @param string $name Parameter name
 * @return int
 */
function sed_stat_get($name)
{
	global $db_stats;

	$sql = sed_sql_query("SELECT stat_value FROM $db_stats where stat_name='$name' LIMIT 1");
	$result = (sed_sql_numrows($sql)>0) ? sed_sql_result($sql, 0, 'stat_value') : FALSE;
	return($result);
}

/**
 * Increments stats
 *
 * @param string $name Parameter name
 */
function sed_stat_inc($name)
{
	global $db_stats;

	$sql = sed_sql_query("UPDATE $db_stats SET stat_value=stat_value+1 WHERE stat_name='$name'");
	return;
}

/**
 * Returns substring position in file
 *
 * @param string $file File path
 * @param string $str Needle
 * @param int $maxsize Search limit
 * @return int
 */
function sed_stringinfile($file, $str, $maxsize=32768)
{
	if ($fp = @fopen($file, 'r'))
	{
		$data = fread($fp, $maxsize);
		$pos = mb_strpos($data, $str);
		$result = ($pos===FALSE) ? FALSE : TRUE;
	}
	else
	{ $result = FALSE; }
	@fclose($fp);
	return ($result);
}

/**
 * Sends item to trash
 *
 * @param string $type Item type
 * @param string $title Title
 * @param int $itemid Item ID
 * @param mixed $datas Item contents
 */
function sed_trash_put($type, $title, $itemid, $datas)
{
	global $db_trash, $sys, $usr;

	$sql = sed_sql_query("INSERT INTO $db_trash (tr_date, tr_type, tr_title, tr_itemid, tr_trashedby, tr_datas)
	VALUES
	(".$sys['now_offset'].", '".sed_sql_prep($type)."', '".sed_sql_prep($title)."', '".sed_sql_prep($itemid)."', ".$usr['id'].", '".sed_sql_prep(serialize($datas))."')");
}

/**
 * Generates random string
 *
 * @param int $l Length
 * @return string
 */
function sed_unique($l=16)
{
	return(mb_substr(md5(mt_rand(0,1000000)), 0, $l));
}

/**
 * Fetches user entry from DB
 *
 * @param int $id User ID
 * @return array
 */
function sed_userinfo($id)
{
	global $db_users;

	$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id'");
	if ($res = sed_sql_fetcharray($sql))
	{ return ($res); }
	else
	{
		$res['user_name'] = '?';
		return ($res);
	}
}

/**
 * Checks whether user is online
 *
 * @param int $id User ID
 * @return bool
 */
function sed_userisonline($id)
{
	global $sed_usersonline;

	$res = FALSE;
	if (is_array($sed_usersonline))
	{ $res = (in_array($id,$sed_usersonline)) ? TRUE : FALSE; }
	return ($res);
}

/**
 * Wraps text
 *
 * @param string $str Source text
 * @param int $wrap Wrapping boundary
 * @return string
 */
function sed_wraptext($str,$wrap=128)
{
	if (!empty($str))
	{ $str = preg_replace("/([^\n\r ?&\.\/<>\"\\-]{80})/i"," \\1\n", $str); }
	return($str);
}

/**
 * Returns XSS protection variable for GET URLs
 *
 * @return unknown
 */
function sed_xg()
{
	return ('x='.sed_sourcekey());
}

/**
 * Returns XSS protection field for POST forms
 *
 * @return string
 */
function sed_xp()
{
	return ('<div><input type="hidden" name="x" value="'.sed_sourcekey().'" /></div>');
}


/* ============== FLAGS AND COUNTRIES (ISO 3166) =============== */

$sed_languages['de']= 'Deutsch';
$sed_languages['dk']= 'Dansk';
$sed_languages['es']= 'Espa�ol';
$sed_languages['fi']= 'Suomi';
$sed_languages['fr']= 'Fran�ais';
$sed_languages['it']= 'Italiano';
$sed_languages['nl']= 'Nederlands';
$sed_languages['ru']= '&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;';
$sed_languages['se']= 'Svenska';
$sed_languages['en']= 'English';
$sed_languages['pl']= 'Polski';
$sed_languages['pt']= 'Portugese';
$sed_languages['cn']= '&#27721;&#35821;';
$sed_languages['gr']= 'Greek';
$sed_languages['hu']= 'Hungarian';
$sed_languages['jp']= '&#26085;&#26412;&#35486;';
$sed_languages['kr']= '&#54620;&#44397;&#47568;';

$sed_countries = array (
'00' => '---',
'af' => 'Afghanistan',
'al' => 'Albania',
'dz' => 'Algeria',
'as' => 'American Samoa',
'ad' => 'Andorra',
'ao' => 'Angola',
'ai' => 'Anguilla',
'aq' => 'Antarctica',
'ag' => 'Antigua And Barbuda',
'ar' => 'Argentina',
'am' => 'Armenia',
'aw' => 'Aruba',
'au' => 'Australia',
'at' => 'Austria',
'az' => 'Azerbaijan',
'bs' => 'Bahamas',
'bh' => 'Bahrain',
'bd' => 'Bangladesh',
'bb' => 'Barbados',
'by' => 'Belarus',
'be' => 'Belgium',
'bz' => 'Belize',
'bj' => 'Benin',
'bm' => 'Bermuda',
'bt' => 'Bhutan',
'bo' => 'Bolivia',
'ba' => 'Bosnia And Herzegovina',
'bw' => 'Botswana',
'bv' => 'Bouvet Island',
'br' => 'Brazil',
'io' => 'British Indian Ocean Territory',
'bn' => 'Brunei Darussalam',
'bg' => 'Bulgaria',
'bf' => 'Burkina Faso',
'bi' => 'Burundi',
'kh' => 'Cambodia',
'cm' => 'Cameroon',
'ca' => 'Canada',
'cv' => 'Cape Verde',
'ky' => 'Cayman Islands',
'cf' => 'Central African Republic',
'td' => 'Chad',
'cl' => 'Chile',
'cn' => 'China',
'cx' => 'Christmas Island',
'cc' => 'Cocos Islands',
'co' => 'Colombia',
'km' => 'Comoros',
'cg' => 'Congo',
'ck' => 'Cook Islands',
'cr' => 'Costa Rica',
'ci' => 'Cote D\'ivoire',
'hr' => 'Croatia',
'cu' => 'Cuba',
'cy' => 'Cyprus',
'cz' => 'Czech Republic',
'dk' => 'Denmark',
'dj' => 'Djibouti',
'dm' => 'Dominica',
'do' => 'Dominican Republic',
'tp' => 'East Timor',
'ec' => 'Ecuador',
'eg' => 'Egypt',
'sv' => 'El Salvador',
'en' => 'England',
'gq' => 'Equatorial Guinea',
'er' => 'Eritrea',
'ee' => 'Estonia',
'et' => 'Ethiopia',
'eu' => 'Europe',
'fk' => 'Falkland Islands',
'fo' => 'Faeroe Islands',
'fj' => 'Fiji',
'fi' => 'Finland',
'fr' => 'France',
'gf' => 'French Guiana',
'pf' => 'French Polynesia',
'tf' => 'French Southern Territories',
'ga' => 'Gabon',
'gm' => 'Gambia',
'ge' => 'Georgia',
'de' => 'Germany',
'gh' => 'Ghana',
'gi' => 'Gibraltar',
'gr' => 'Greece',
'gl' => 'Greenland',
'gd' => 'Grenada',
'gp' => 'Guadeloupe',
'gu' => 'Guam',
'gt' => 'Guatemala',
'gn' => 'Guinea',
'gw' => 'Guinea-bissau',
'gy' => 'Guyana',
'ht' => 'Haiti',
'hm' => 'Heard And Mc Donald Islands',
'hn' => 'Honduras',
'hk' => 'Hong Kong',
'hu' => 'Hungary',
'is' => 'Iceland',
'in' => 'India',
'id' => 'Indonesia',
'ir' => 'Iran',
'iq' => 'Iraq',
'ie' => 'Ireland',
'il' => 'Israel',
'it' => 'Italy',
'jm' => 'Jamaica',
'jp' => 'Japan',
'jo' => 'Jordan',
'kz' => 'Kazakhstan',
'ke' => 'Kenya',
'ki' => 'Kiribati',
'kp' => 'North Korea',
'kr' => 'South Korea',
'kw' => 'Kuwait',
'kg' => 'Kyrgyzstan',
'la' => 'Laos',
'lv' => 'Latvia',
'lb' => 'Lebanon',
'ls' => 'Lesotho',
'lr' => 'Liberia',
'ly' => 'Libya',
'li' => 'Liechtenstein',
'lt' => 'Lithuania',
'lu' => 'Luxembourg',
'mo' => 'Macau',
'mk' => 'Macedonia',
'mg' => 'Madagascar',
'mw' => 'Malawi',
'my' => 'Malaysia',
'mv' => 'Maldives',
'ml' => 'Mali',
'mt' => 'Malta',
'mh' => 'Marshall Islands',
'mq' => 'Martinique',
'mr' => 'Mauritania',
'mu' => 'Mauritius',
'yt' => 'Mayotte',
'mx' => 'Mexico',
'fm' => 'Micronesia',
'md' => 'Moldavia',
'mc' => 'Monaco',
'mn' => 'Mongolia',
'ms' => 'Montserrat',
'ma' => 'Morocco',
'mz' => 'Mozambique',
'mm' => 'Myanmar',
'na' => 'Namibia',
'nr' => 'Nauru',
'np' => 'Nepal',
'nl' => 'Netherlands',
'an' => 'Netherlands Antilles',
'nc' => 'New Caledonia',
'nz' => 'New Zealand',
'ni' => 'Nicaragua',
'ne' => 'Niger',
'ng' => 'Nigeria',
'nu' => 'Niue',
'nf' => 'Norfolk Island',
'mp' => 'Northern Mariana Islands',
'no' => 'Norway',
'om' => 'Oman',
'pk' => 'Pakistan',
'pw' => 'Palau',
'ps' => 'Palestine',
'pa' => 'Panama',
'pg' => 'Papua New Guinea',
'py' => 'Paraguay',
'pe' => 'Peru',
'ph' => 'Philippines',
'pn' => 'Pitcairn',
'pl' => 'Poland',
'pt' => 'Portugal',
'pr' => 'Puerto Rico',
'qa' => 'Qatar',
're' => 'Reunion',
'ro' => 'Romania',
'ru' => 'Russia',
'rw' => 'Rwanda',
'kn' => 'Saint Kitts And Nevis',
'lc' => 'Saint Lucia',
'vc' => 'Saint Vincent',
'ws' => 'Samoa',
'sm' => 'San Marino',
'st' => 'Sao Tome And Principe',
'sa' => 'Saudi Arabia',
'sx' => 'Scotland',
'sn' => 'Senegal',
'sc' => 'Seychelles',
'sl' => 'Sierra Leone',
'sg' => 'Singapore',
'sk' => 'Slovakia',
'si' => 'Slovenia',
'sb' => 'Solomon Islands',
'so' => 'Somalia',
'za' => 'South Africa',
'gs' => 'South Georgia',
'es' => 'Spain',
'lk' => 'Sri Lanka',
'sh' => 'St. Helena',
'pm' => 'St. Pierre And Miquelon',
'sd' => 'Sudan',
'sr' => 'Suriname',
'sj' => 'Svalbard And Jan Mayen Islands',
'sz' => 'Swaziland',
'se' => 'Sweden',
'ch' => 'Switzerland',
'sy' => 'Syria',
'tw' => 'Taiwan',
'tj' => 'Tajikistan',
'tz' => 'Tanzania',
'th' => 'Thailand',
'tg' => 'Togo',
'tk' => 'Tokelau',
'to' => 'Tonga',
'tt' => 'Trinidad And Tobago',
'tn' => 'Tunisia',
'tr' => 'Turkiye',
'tm' => 'Turkmenistan',
'tc' => 'Turks And Caicos Islands',
'tv' => 'Tuvalu',
'ug' => 'Uganda',
'ua' => 'Ukraine',
'ae' => 'United Arab Emirates',
'uk' => 'United Kingdom',
'us' => 'United States',
'uy' => 'Uruguay',
'uz' => 'Uzbekistan',
'vu' => 'Vanuatu',
'va' => 'Vatican',
've' => 'Venezuela',
'vn' => 'Vietnam',
'vg' => 'Virgin Islands (british)',
'vi' => 'Virgin Islands (u.s.)',
'wa' => 'Wales',
'wf' => 'Wallis And Futuna Islands',
'eh' => 'Western Sahara',
'ye' => 'Yemen',
'yu' => 'Yugoslavia',
'zr' => 'Zaire',
'zm' => 'Zambia',
'zw' => 'Zimbabwe'
);

/**
 * XTemplate PHP templating engine
 *
 * @package XTemplate
 * @author Barnabas Debreceni [cranx@users.sourceforge.net]
 * @copyright Barnabas Debreceni 2000-2001
 * @author Jeremy Coates [cocomp@users.sourceforge.net]
 * @copyright Jeremy Coates 2002-2007
 * @see license.txt LGPL / BSD license
 * @since PHP 5
 * @link $HeadURL: https://xtpl.svn.sourceforge.net/svnroot/xtpl/trunk/xtemplate.class.php $
 * @version $Id: xtemplate.class.php 21 2007-05-29 18:01:15Z cocomp $
 *
 *
 * XTemplate class - http://www.phpxtemplate.org/ (x)html / xml generation with templates - fast & easy
 * Latest stable & Subversion versions available @ http://sourceforge.net/projects/xtpl/
 * License: LGPL / BSD - see license.txt
 * Changelog: see changelog.txt
 */
class XTemplate {

	/**
	 * Properties
	 */

	/**
	 * Raw contents of the template file
	 *
	 * @access public
	 * @var string
	 */
	public $filecontents = '';

	/**
	 * Unparsed blocks
	 *
	 * @access public
	 * @var array
	 */
	public $blocks = array();

	/**
	 * Parsed blocks
	 *
	 * @var unknown_type
	 */
	public $parsed_blocks = array();

	/**
	 * Preparsed blocks (for file includes)
	 *
	 * @access public
	 * @var array
	 */
	public $preparsed_blocks = array();

	/**
	 * Block parsing order for recursive parsing
	 * (Sometimes reverse :)
	 *
	 * @access public
	 * @var array
	 */
	public $block_parse_order = array();

	/**
	 * Store sub-block names
	 * (For fast resetting)
	 *
	 * @access public
	 * @var array
	 */
	public $sub_blocks = array();

	/**
	 * Variables array
	 *
	 * @access public
	 * @var array
	 */
	public $vars = array();

	/**
	 * File variables array
	 *
	 * @access public
	 * @var array
	 */
	public $filevars = array();

	/**
	 * Filevars' parent block
	 *
	 * @access public
	 * @var array
	 */
	public $filevar_parent = array();

	/**
	 * File caching during duration of script
	 * e.g. files only cached to speed {FILE "filename"} repeats
	 *
	 * @access public
	 * @var array
	 */
	public $filecache = array();

	/**
	 * Location of template files
	 *
	 * @access public
	 * @var string
	 */
	public $tpldir = '';

	/**
	 * Filenames lookup table
	 *
	 * @access public
	 * @var null
	 */
	public $files = null;

	/**
	 * Template filename
	 *
	 * @access public
	 * @var string
	 */
	public $filename = '';

	// moved to setup method so uses the tag_start & end_delims
	/**
	 * RegEx for file includes
	 *
	 * "/\{FILE\s*\"([^\"]+)\"\s*\}/m";
	 *
	 * @access public
	 * @var string
	 */
	public $file_delim = '';

	/**
	 * RegEx for file include variable
	 *
	 * "/\{FILE\s*\{([A-Za-z0-9\._]+?)\}\s*\}/m";
	 *
	 * @access public
	 * @var string
	 */
	public $filevar_delim = '';

	/**
	 * RegEx for file includes with newlines
	 *
	 * "/^\s*\{FILE\s*\{([A-Za-z0-9\._]+?)\}\s*\}\s*\n/m";
	 *
	 * @access public
	 * @var string
	 */
	public $filevar_delim_nl = '';

	/**
	 * Template block start delimiter
	 *
	 * @access public
	 * @var string
	 */
	public $block_start_delim = '<!-- ';

	/**
	 * Template block end delimiter
	 *
	 * @access public
	 * @var string
	 */
	public $block_end_delim = '-->';

	/**
	 * Template block start word
	 *
	 * @access public
	 * @var string
	 */
	public $block_start_word = 'BEGIN:';

	/**
	 * Template block end word
	 *
	 * The last 3 properties and this make the delimiters look like:
	 * @example <!-- BEGIN: block_name -->
	 * if you use the default syntax.
	 *
	 * @access public
	 * @var string
	 */
	public $block_end_word = 'END:';

	/**
	 * Template tag start delimiter
	 *
	 * This makes the delimiters look like:
	 * @example {tagname}
	 * if you use the default syntax.
	 *
	 * @access public
	 * @var string
	 */
	public $tag_start_delim = '{';

	/**
	 * Template tag end delimiter
	 *
	 * This makes the delimiters look like:
	 * @example {tagname}
	 * if you use the default syntax.
	 *
	 * @access public
	 * @var string
	 */
	public $tag_end_delim = '}';
	/* this makes the delimiters look like: {tagname} if you use my syntax. */

	/**
	 * Regular expression element for comments within tags and blocks
	 *
	 * @example {tagname#My Comment}
	 * @example {tagname #My Comment}
	 * @example <!-- BEGIN: blockname#My Comment -->
	 * @example <!-- BEGIN: blockname #My Comment -->
	 *
	 * @access public
	 * @var string
	 */
	public $comment_preg = '( ?#.*?)?';

	/**
	 * Default main template block name
	 *
	 * @access public
	 * @var string
	 */
	public $mainblock = 'main';

	/**
	 * Script output type
	 *
	 * @access public
	 * @var string
	 */
	public $output_type = 'HTML';

	/**
	 * Debug mode
	 *
	 * @access public
	 * @var boolean
	 */
	public $debug = false;

	/**
	 * Null string for unassigned vars
	 *
	 * @access protected
	 * @var array
	 */
	protected $_null_string = array('' => '');

	/**
	 * Null string for unassigned blocks
	 *
	 * @access protected
	 * @var array
	 */
	protected $_null_block = array('' => '');

	/**
	 * Errors
	 *
	 * @access protected
	 * @var string
	 */
	protected $_error = '';

	/**
	 * Auto-reset sub blocks
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_autoreset = true;

	/**
	 * Set to FALSE to generate errors if a non-existant blocks is referenced
	 *
	 * @author NW
	 * @since 2002/10/17
	 * @access protected
	 * @var boolean
	 */
	protected $_ignore_missing_blocks = true;

	/**
     * PHP 5 Constructor - Instantiate the object
     *
     * @param string $file Template file to work on
     * @param string/array $tpldir Location of template files (useful for keeping files outside web server root)
     * @param array $files Filenames lookup
     * @param string $mainblock Name of main block in the template
     * @param boolean $autosetup If true, run setup() as part of constuctor
     * @return XTemplate
     */
	public function __construct($file, $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true) {

		$this->restart($file, $tpldir, $files, $mainblock, $autosetup, $this->tag_start_delim, $this->tag_end_delim);
	}

	/**
     * PHP 4 Constructor - Instantiate the object
     *
     * @deprecated Use PHP 5 constructor instead
     * @param string $file Template file to work on
     * @param string/array $tpldir Location of template files (useful for keeping files outside web server root)
     * @param array $files Filenames lookup
     * @param string $mainblock Name of main block in the template
     * @param boolean $autosetup If true, run setup() as part of constuctor
     * @return XTemplate
     */
	public function XTemplate ($file, $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true) {

		assert('Deprecated - use PHP 5 constructor');
	}


	/***************************************************************************/
	/***[ public stuff ]********************************************************/
	/***************************************************************************/

	/**
	 * Restart the class - allows one instantiation with several files processed by restarting
	 * e.g. $xtpl = new XTemplate('file1.xtpl');
	 * $xtpl->parse('main');
	 * $xtpl->out('main');
	 * $xtpl->restart('file2.xtpl');
	 * $xtpl->parse('main');
	 * $xtpl->out('main');
	 * (Added in response to sf:641407 feature request)
	 *
	 * @param string $file Template file to work on
	 * @param string/array $tpldir Location of template files
	 * @param array $files Filenames lookup
	 * @param string $mainblock Name of main block in the template
	 * @param boolean $autosetup If true, run setup() as part of restarting
	 * @param string $tag_start {
	 * @param string $tag_end }
	 */
	public function restart ($file, $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true, $tag_start = '{', $tag_end = '}') {

		$this->filename = $file;

		// From SF Feature request 1202027
		// Kenneth Kalmer
		$this->tpldir = $tpldir;
		if (defined('XTPL_DIR') && empty($this->tpldir)) {
			$this->tpldir = XTPL_DIR;
		}

		if (is_array($files)) {
			$this->files = $files;
		}

		$this->mainblock = $mainblock;

		$this->tag_start_delim = $tag_start;
		$this->tag_end_delim = $tag_end;

		// Start with fresh file contents
		$this->filecontents = '';

		// Reset the template arrays
		$this->blocks = array();
		$this->parsed_blocks = array();
		$this->preparsed_blocks = array();
		$this->block_parse_order = array();
		$this->sub_blocks = array();
		$this->vars = array();
		$this->filevars = array();
		$this->filevar_parent = array();
		$this->filecache = array();

		if ($autosetup) {
			$this->setup();
		}
	}

	/**
     * setup - the elements that were previously in the constructor
     *
     * @access public
     * @param boolean $add_outer If true is passed when called, it adds an outer main block to the file
     */
	public function setup ($add_outer = false) {

		$this->tag_start_delim = preg_quote($this->tag_start_delim);
		$this->tag_end_delim = preg_quote($this->tag_end_delim);

		// Setup the file delimiters

		// regexp for file includes
		$this->file_delim = "/" . $this->tag_start_delim . "FILE\s*\"([^\"]+)\"" . $this->comment_preg . $this->tag_end_delim . "/m";

		// regexp for file includes
		$this->filevar_delim = "/" . $this->tag_start_delim . "FILE\s*" . $this->tag_start_delim . "([A-Za-z0-9\._]+?)" . $this->comment_preg . $this->tag_end_delim . $this->comment_preg . $this->tag_end_delim . "/m";

		// regexp for file includes w/ newlines
		$this->filevar_delim_nl = "/^\s*" . $this->tag_start_delim . "FILE\s*" . $this->tag_start_delim . "([A-Za-z0-9\._]+?)" . $this->comment_preg . $this->tag_end_delim . $this->comment_preg . $this->tag_end_delim . "\s*\n/m";

		if (empty($this->filecontents)) {
			// read in template file
			$this->filecontents = $this->_r_getfile($this->filename);
		}

		if ($add_outer) {
			$this->_add_outer_block();
		}

		// preprocess some stuff
		$this->blocks = $this->_maketree($this->filecontents, '');
		$this->filevar_parent = $this->_store_filevar_parents($this->blocks);
		$this->scan_globals();
	}

	/**
     * assign a variable
     *
     * @example Simplest case:
     * @example $xtpl->assign('name', 'value');
     * @example {name} in template
     *
     * @example Array assign:
     * @example $xtpl->assign(array('name' => 'value', 'name2' => 'value2'));
     * @example {name} {name2} in template
     *
     * @example Value as array assign:
     * @example $xtpl->assign('name', array('key' => 'value', 'key2' => 'value2'));
     * @example {name.key} {name.key2} in template
     *
     * @example Reset array:
     * @example $xtpl->assign('name', array('key' => 'value', 'key2' => 'value2'));
     * @example // Other code then:
     * @example $xtpl->assign('name', array('key3' => 'value3'), false);
     * @example {name.key} {name.key2} {name.key3} in template
     *
     * @access public
     * @param string $name Variable to assign $val to
     * @param string / array $val Value to assign to $name
	 * @param boolean $reset_array Reset the variable array if $val is an array
     */
	public function assign ($name, $val = '', $reset_array = true) {

		if (is_array($name)) {

			foreach ($name as $k => $v) {

				$this->vars[$k] = $v;
			}
		} elseif (is_array($val)) {

			// Clear the existing values
    		if ($reset_array) {
    			$this->vars[$name] = array();
    		}

        	foreach ($val as $k => $v) {

        		$this->vars[$name][$k] = $v;
        	}

		} else {

			$this->vars[$name] = $val;
		}
	}

	/**
     * assign a file variable
     *
     * @access public
     * @param string $name Variable to assign $val to
     * @param string / array $val Values to assign to $name
     */
	public function assign_file ($name, $val = '') {

		if (is_array($name)) {

			foreach ($name as $k => $v) {

				$this->_assign_file_sub($k, $v);
			}
		} else {

			$this->_assign_file_sub($name, $val);
		}
	}

	/**
     * parse a block
     *
     * @access public
     * @param string $bname Block name to parse
     */
	public function parse ($bname) {

		if (isset($this->preparsed_blocks[$bname])) {

			$copy = $this->preparsed_blocks[$bname];

		} elseif (isset($this->blocks[$bname])) {

			$copy = $this->blocks[$bname];

		} elseif ($this->_ignore_missing_blocks) {
			// ------------------------------------------------------
			// NW : 17 Oct 2002. Added default of ignore_missing_blocks
			//      to allow for generalised processing where some
			//      blocks may be removed from the HTML without the
			//      processing code needing to be altered.
			// ------------------------------------------------------
			// JRC: 3/1/2003 added set error to ignore missing functionality
			$this->_set_error("parse: blockname [$bname] does not exist");
			return;

		} else {

			$this->_set_error("parse: blockname [$bname] does not exist");
		}

		/* from there we should have no more {FILE } directives */
		if (!isset($copy)) {
			die('Block: ' . $bname);
		}

		$copy = preg_replace($this->filevar_delim_nl, '', $copy);

		$var_array = array();

		/* find & replace variables+blocks */
		preg_match_all("|" . $this->tag_start_delim . "([A-Za-z0-9\._]+?" . $this->comment_preg . ")" . $this->tag_end_delim. "|", $copy, $var_array);

		$var_array = $var_array[1];

		foreach ($var_array as $k => $v) {

			// Are there any comments in the tags {tag#a comment for documenting the template}
			$any_comments = explode('#', $v);
			$v = rtrim($any_comments[0]);

			if (sizeof($any_comments) > 1) {

				$comments = $any_comments[1];
			} else {

				$comments = '';
			}

			$sub = explode('.', $v);

			if ($sub[0] == '_BLOCK_') {

				unset($sub[0]);

				$bname2 = implode('.', $sub);

				// trinary operator eliminates assign error in E_ALL reporting
				$var = isset($this->parsed_blocks[$bname2]) ? $this->parsed_blocks[$bname2] : null;
				$nul = (!isset($this->_null_block[$bname2])) ? $this->_null_block[''] : $this->_null_block[$bname2];

				if ($var === '') {

					if ($nul == '') {
						// -----------------------------------------------------------
						// Removed requirement for blocks to be at the start of string
						// -----------------------------------------------------------
						//                      $copy=preg_replace("/^\s*\{".$v."\}\s*\n*/m","",$copy);
						// Now blocks don't need to be at the beginning of a line,
						//$copy=preg_replace("/\s*" . $this->tag_start_delim . $v . $this->tag_end_delim . "\s*\n*/m","",$copy);
						$copy = preg_replace("|" . $this->tag_start_delim . $v . $this->tag_end_delim . "|m", '', $copy);

					} else {

						$copy = preg_replace("|" . $this->tag_start_delim . $v . $this->tag_end_delim . "|m", "$nul", $copy);
					}
				} else {

					//$var = trim($var);
					switch (true) {
						case preg_match('/^\n/', $var) && preg_match('/\n$/', $var):
							$var = mb_substr($var, 1, -1);
							break;

						case preg_match('/^\n/', $var):
							$var = mb_substr($var, 1);
							break;

						case preg_match('/\n$/', $var):
							$var = mb_substr($var, 0, -1);
							break;
					}

					// SF Bug no. 810773 - thanks anonymous
					$var = str_replace('\\', '\\\\', $var);
					// Ensure dollars in strings are not evaluated reported by SadGeezer 31/3/04
					$var = str_replace('$', '\\$', $var);
					// Replaced str_replaces with preg_quote
					//$var = preg_quote($var);
					$var = str_replace('\\|', '|', $var);
					$copy = preg_replace("|" . $this->tag_start_delim . $v . $this->tag_end_delim . "|m", "$var", $copy);

					if (preg_match('/^\n/', $copy) && preg_match('/\n$/', $copy)) {
						$copy = mb_substr($copy, 1, -1);
					}
				}
			} else {

				$var = $this->vars;

				foreach ($sub as $v1) {

					// NW 4 Oct 2002 - Added isset and is_array check to avoid NOTICE messages
					// JC 17 Oct 2002 - Changed EMPTY to stlen=0
					//                if (empty($var[$v1])) { // this line would think that zeros(0) were empty - which is not true
					if (!isset($var[$v1]) || (!is_array($var[$v1]) && mb_strlen($var[$v1]) == 0)) {

						// Check for constant, when variable not assigned
						if (defined($v1)) {

							$var[$v1] = constant($v1);

						} else {

							$var[$v1] = null;
						}
					}

					$var = $var[$v1];
				}

				$nul = (!isset($this->_null_string[$v])) ? ($this->_null_string[""]) : ($this->_null_string[$v]);
				$var = (!isset($var)) ? $nul : $var;

				if ($var === '') {
					// -----------------------------------------------------------
					// Removed requriement for blocks to be at the start of string
					// -----------------------------------------------------------
					//                    $copy=preg_replace("|^\s*\{".$v." ?#?".$comments."\}\s*\n|m","",$copy);
					$copy = preg_replace("|" . $this->tag_start_delim . $v . "( ?#" . $comments . ")?" . $this->tag_end_delim . "|m", '', $copy);
				}

				$var = trim($var);
				// SF Bug no. 810773 - thanks anonymous
				$var = str_replace('\\', '\\\\', $var);
				// Ensure dollars in strings are not evaluated reported by SadGeezer 31/3/04
				$var = str_replace('$', '\\$', $var);
				// Replace str_replaces with preg_quote
				//$var = preg_quote($var);
				$var = str_replace('\\|', '|', $var);
				$copy = preg_replace("|" . $this->tag_start_delim . $v . "( ?#" . $comments . ")?" . $this->tag_end_delim . "|m", "$var", $copy);

				if (preg_match('/^\n/', $copy) && preg_match('/\n$/', $copy)) {
					$copy = mb_substr($copy, 1);
				}
			}
		}

		if (isset($this->parsed_blocks[$bname])) {
			$this->parsed_blocks[$bname] .= $copy;
		} else {
			$this->parsed_blocks[$bname] = $copy;
		}

		/* reset sub-blocks */
		if ($this->_autoreset && (!empty($this->sub_blocks[$bname]))) {

			reset($this->sub_blocks[$bname]);

			foreach ($this->sub_blocks[$bname] as $k => $v) {
				$this->reset($v);
			}
		}
	}

	/**
     * returns the parsed text for a block, including all sub-blocks.
     *
     * @access public
     * @param string $bname Block name to parse
     */
	public function rparse ($bname) {

		if (!empty($this->sub_blocks[$bname])) {

			reset($this->sub_blocks[$bname]);

			foreach ($this->sub_blocks[$bname] as $k => $v) {

				if (!empty($v)) {
					$this->rparse($v);
				}
			}
		}

		$this->parse($bname);
	}

	/**
     * inserts a loop ( call assign & parse )
     *
     * @access public
     * @param string $bname Block name to assign
     * @param string $var Variable to assign values to
     * @param string / array $value Value to assign to $var
    */
	public function insert_loop ($bname, $var, $value = '') {

		$this->assign($var, $value);
		$this->parse($bname);
	}

	/**
     * parses a block for every set of data in the values array
     *
     * @access public
     * @param string $bname Block name to loop
     * @param string $var Variable to assign values to
     * @param array $values Values to assign to $var
    */
	public function array_loop ($bname, $var, &$values) {

		if (is_array($values)) {

			foreach($values as $v) {

				$this->insert_loop($bname, $var, $v);
			}
		}
	}

	/**
     * returns the parsed text for a block
     *
     * @access public
     * @param string $bname Block name to return
     * @return string
     */
	public function text ($bname = '') {

		$text = '';

		if ($this->debug && $this->output_type == 'HTML') {
			// JC 20/11/02 echo the template filename if in development as
			// html comment
			$text .= '<!-- XTemplate: ' . realpath($this->filename) . " -->\n";
		}

		$bname = !empty($bname) ? $bname : $this->mainblock;

		$text .= isset($this->parsed_blocks[$bname]) ? $this->parsed_blocks[$bname] : $this->get_error();

		return $text;
	}

	/**
     * prints the parsed text
     *
     * @access public
     * @param string $bname Block name to echo out
     */
	public function out ($bname) {

		$out = $this->text($bname);
		//        $length=mb_strlen($out);
		//header("Content-Length: ".$length); // TODO: Comment this back in later

		echo $out;
	}

	/**
     * prints the parsed text to a specified file
     *
     * @access public
     * @param string $bname Block name to write out
     * @param string $fname File name to write to
     */
	public function out_file ($bname, $fname) {

		if (!empty($bname) && !empty($fname) && is_writeable($fname)) {

			$fp = fopen($fname, 'w');
			fwrite($fp, $this->text($bname));
			fclose($fp);
		}
	}

	/**
     * resets the parsed text
     *
     * @access public
     * @param string $bname Block to reset
     */
	public function reset ($bname) {

		$this->parsed_blocks[$bname] = '';
	}

	/**
     * returns true if block was parsed, false if not
     *
     * @access public
     * @param string $bname Block name to test
     * @return boolean
     */
	public function parsed ($bname) {

		return (!empty($this->parsed_blocks[$bname]));
	}

	/**
     * sets the string to replace in case the var was not assigned
     *
     * @access public
     * @param string $str Display string for null block
     * @param string $varname Variable name to apply $str to
     */
	public function set_null_string($str, $varname = '') {

		$this->_null_string[$varname] = $str;
	}

	/**
	 * Backwards compatibility only
	 *
	 * @param string $str
	 * @param string $varname
	 * @deprecated Change to set_null_string to keep in with rest of naming convention
	 */
	public function SetNullString ($str, $varname = '') {
		$this->set_null_string($str, $varname);
	}

	/**
     * sets the string to replace in case the block was not parsed
     *
     * @access public
     * @param string $str Display string for null block
     * @param string $bname Block name to apply $str to
     */
	public function set_null_block ($str, $bname = '') {

		$this->_null_block[$bname] = $str;
	}

	/**
	 * Backwards compatibility only
	 *
	 * @param string $str
	 * @param string $bname
	 * @deprecated Change to set_null_block to keep in with rest of naming convention
	 */
	public function SetNullBlock ($str, $bname = '') {
		$this->set_null_block($str, $bname);
	}

	/**
     * sets AUTORESET to 1. (default is 1)
     * if set to 1, parse() automatically resets the parsed blocks' sub blocks
     * (for multiple level blocks)
     *
     * @access public
     */
	public function set_autoreset () {

		$this->_autoreset = true;
	}

	/**
     * sets AUTORESET to 0. (default is 1)
     * if set to 1, parse() automatically resets the parsed blocks' sub blocks
     * (for multiple level blocks)
     *
     * @access public
     */
	public function clear_autoreset () {

		$this->_autoreset = false;
	}

	/**
     * scans global variables and assigns to PHP array
     *
     * @access public
     */
	public function scan_globals () {

		reset($GLOBALS);

		foreach ($GLOBALS as $k => $v) {
			$GLOB[$k] = $v;
		}

		/**
		 * Access global variables as:
		 * @example {PHP._SERVER.HTTP_HOST}
		 * in your template!
		 */
		$this->assign('PHP', $GLOB);
	}

	/**
     * gets error condition / string
     *
     * @access public
     * @return boolean / string
     */
	public function get_error () {

		// JRC: 3/1/2003 Added ouptut wrapper and detection of output type for error message output
		$retval = false;

		if ($this->_error != '') {

			switch ($this->output_type) {
				case 'HTML':
				case 'html':
					$retval = '<b>[XTemplate]</b><ul>' . nl2br(str_replace('* ', '<li>', str_replace(" *\n", "</li>\n", $this->_error))) . '</ul>';
					break;

				default:
					$retval = '[XTemplate] ' . str_replace(' *\n', "\n", $this->_error);
					break;
			}
		}

		return $retval;
	}

	/***************************************************************************/
	/***[ private stuff ]*******************************************************/
	/***************************************************************************/

	/**
     * generates the array containing to-be-parsed stuff:
     * $blocks["main"],$blocks["main.table"],$blocks["main.table.row"], etc.
     * also builds the reverse parse order.
     *
     * @access public - aiming for private
     * @param string $con content to be processed
     * @param string $parentblock name of the parent block in the block hierarchy
     */
	public function _maketree ($con, $parentblock='') {

		$blocks = array();

		$con2 = explode($this->block_start_delim, $con);

		if (!empty($parentblock)) {

			$block_names = explode('.', $parentblock);
			$level = sizeof($block_names);

		} else {

			$block_names = array();
			$level = 0;
		}

		// JRC 06/04/2005 Added block comments (on BEGIN or END) <!-- BEGIN: block_name#Comments placed here -->
		//$patt = "($this->block_start_word|$this->block_end_word)\s*(\w+)\s*$this->block_end_delim(.*)";
		$patt = "(" . $this->block_start_word . "|" . $this->block_end_word . ")\s*(\w+)" . $this->comment_preg . "\s*" . $this->block_end_delim . "(.*)";

		foreach($con2 as $k => $v) {

			$res = array();

			if (preg_match_all("/$patt/ims", $v, $res, PREG_SET_ORDER)) {
				// $res[0][1] = BEGIN or END
				// $res[0][2] = block name
				// $res[0][3] = comment
				// $res[0][4] = kinda content
				$block_word	= $res[0][1];
				$block_name	= $res[0][2];
				$comment	= $res[0][3];
				$content	= $res[0][4];

				if (mb_strtoupper($block_word) == $this->block_start_word) {

					$parent_name = implode('.', $block_names);

					// add one level - array("main","table","row")
					$block_names[++$level] = $block_name;

					// make block name (main.table.row)
					$cur_block_name=implode('.', $block_names);

					// build block parsing order (reverse)
					$this->block_parse_order[] = $cur_block_name;

					//add contents. trinary operator eliminates assign error in E_ALL reporting
					$blocks[$cur_block_name] = isset($blocks[$cur_block_name]) ? $blocks[$cur_block_name] . $content : $content;

					// add {_BLOCK_.blockname} string to parent block
					$blocks[$parent_name] .= str_replace('\\', '', $this->tag_start_delim) . '_BLOCK_.' . $cur_block_name . str_replace('\\', '', $this->tag_end_delim);

					// store sub block names for autoresetting and recursive parsing
					$this->sub_blocks[$parent_name][] = $cur_block_name;

					// store sub block names for autoresetting
					$this->sub_blocks[$cur_block_name][] = '';

				} else if (mb_strtoupper($block_word) == $this->block_end_word) {

					unset($block_names[$level--]);

					$parent_name = implode('.', $block_names);

					// add rest of block to parent block
					$blocks[$parent_name] .= $content;
				}
			} else {

				// no block delimiters found
				// Saves doing multiple implodes - less overhead
				$tmp = implode('.', $block_names);

				if ($k) {
					$blocks[$tmp] .= $this->block_start_delim;
				}

				// trinary operator eliminates assign error in E_ALL reporting
				$blocks[$tmp] = isset($blocks[$tmp]) ? $blocks[$tmp] . $v : $v;
			}
		}

		return $blocks;
	}

	/**
     * Sub processing for assign_file method
     *
     * @access private
     * @param string $name
     * @param string $val
     */
	private function _assign_file_sub ($name, $val) {

		if (isset($this->filevar_parent[$name])) {

			if ($val != '') {

				$val = $this->_r_getfile($val);

				foreach($this->filevar_parent[$name] as $parent) {

					if (isset($this->preparsed_blocks[$parent]) && !isset($this->filevars[$name])) {

						$copy = $this->preparsed_blocks[$parent];

					} elseif (isset($this->blocks[$parent])) {

						$copy = $this->blocks[$parent];
					}

					$res = array();

					preg_match_all($this->filevar_delim, $copy, $res, PREG_SET_ORDER);

					if (is_array($res) && isset($res[0])) {

						// Changed as per solution in SF bug ID #1261828
						foreach ($res as $v) {

							// Changed as per solution in SF bug ID #1261828
							if ($v[1] == $name) {

								// Changed as per solution in SF bug ID #1261828
								$copy = preg_replace("/" . preg_quote($v[0]) . "/", "$val", $copy);
								$this->preparsed_blocks = array_merge($this->preparsed_blocks, $this->_maketree($copy, $parent));
								$this->filevar_parent = array_merge($this->filevar_parent, $this->_store_filevar_parents($this->preparsed_blocks));
							}
						}
					}
				}
			}
		}

		$this->filevars[$name] = $val;
	}

	/**
     * store container block's name for file variables
     *
     * @access public - aiming for private
     * @param array $blocks
     * @return array
     */
	public function _store_filevar_parents ($blocks){

		$parents = array();

		foreach ($blocks as $bname => $con) {

			$res = array();

			preg_match_all($this->filevar_delim, $con, $res);

			foreach ($res[1] as $k => $v) {

				$parents[$v][] = $bname;
			}
		}
		return $parents;
	}

	/**
     * Set the error string
     *
     * @access private
     * @param string $str
     */
	private function _set_error ($str)    {

		// JRC: 3/1/2003 Made to append the error messages
		$this->_error .= '* ' . $str . " *\n";
		// JRC: 3/1/2003 Removed trigger error, use this externally if you want it eg. trigger_error($xtpl->get_error())
		//trigger_error($this->get_error());
	}

	/**
     * returns the contents of a file
     *
     * @access protected
     * @param string $file
     * @return string
     */
	protected function _getfile ($file) {

		if (!isset($file)) {
			// JC 19/12/02 added $file to error message
			$this->_set_error('!isset file name!' . $file);

			return '';
		}

		// check if filename is mapped to other filename
		if (isset($this->files)) {

			if (isset($this->files[$file])) {

				$file = $this->files[$file];
			}
		}

		// prepend template dir
		if (!empty($this->tpldir)) {

			/**
			 * Support hierarchy of file locations to search
			 *
			 * @example Supply array of filepaths when instantiating
			 * 			First path supplied that has the named file is prioritised
			 * 			$xtpl = new XTemplate('myfile.xtpl', array('.','/mypath', '/mypath2'));
			 * @since 29/05/2007
			 */
			if (is_array($this->tpldir)) {

				foreach ($this->tpldir as $dir) {

					if (is_readable($dir . DIRECTORY_SEPARATOR . $file)) {
						$file = $dir . DIRECTORY_SEPARATOR . $file;
						break;
					}
				}
			} else {

				$file = $this->tpldir. DIRECTORY_SEPARATOR . $file;
			}
		}

		$file_text = '';

		if (isset($this->filecache[$file])) {

			$file_text .= $this->filecache[$file];

			if ($this->debug) {
				$file_text = '<!-- XTemplate debug cached: ' . realpath($file) . ' -->' . "\n" . $file_text;
			}

		} else {

			if (is_file($file) && is_readable($file)) {

				if (filesize($file)) {

					if (!($fh = fopen($file, 'r'))) {

						$this->_set_error('Cannot open file: ' . realpath($file));
						return '';
					}

					$file_text .= fread($fh,filesize($file));
					fclose($fh);

				}

				if ($this->debug) {
					$file_text = '<!-- XTemplate debug: ' . realpath($file) . ' -->' . "\n" . $file_text;
				}

			} elseif (str_replace('.', '', phpversion()) >= '430' && $file_text = @file_get_contents($file, true)) {
				// Enable use of include path by using file_get_contents
				// Implemented at suggestion of SF Feature Request ID #1529478 michaelgroh
				if ($file_text === false) {
					$this->_set_error("[" . realpath($file) . "] ($file) does not exist");
					$file_text = "<b>__XTemplate fatal error: file [$file] does not exist in the include path__</b>";
				} elseif ($this->debug) {
					$file_text = '<!-- XTemplate debug: ' . realpath($file) . ' (via include path) -->' . "\n" . $file_text;
				}
			} elseif (!is_file($file)) {

				// NW 17 Oct 2002 : Added realpath around the file name to identify where the code is searching.
				$this->_set_error("[" . realpath($file) . "] ($file) does not exist");
				$file_text .= "<b>__XTemplate fatal error: file [$file] does not exist__</b>";

			} elseif (!is_readable($file)) {

				$this->_set_error("[" . realpath($file) . "] ($file) is not readable");
				$file_text .= "<b>__XTemplate fatal error: file [$file] is not readable__</b>";
			}

			$this->filecache[$file] = $file_text;
		}

		return $file_text;
	}

	/**
     * recursively gets the content of a file with {FILE "filename.tpl"} directives
     *
     * @access public - aiming for private
     * @param string $file
     * @return string
     */
	public function _r_getfile ($file) {

		$text = $this->_getfile($file);

		$res = array();

		while (preg_match($this->file_delim,$text,$res)) {

			$text2 = $this->_getfile($res[1]);
			$text = preg_replace("'".preg_quote($res[0])."'",$text2,$text);
		}

		return $text;
	}


	/**
     * add an outer block delimiter set useful for rtfs etc - keeps them editable in word
     *
     * @access private
     */
	private function _add_outer_block () {

		$before = $this->block_start_delim . $this->block_start_word . ' ' . $this->mainblock . ' ' . $this->block_end_delim;
		$after = $this->block_start_delim . $this->block_end_word . ' ' . $this->mainblock . ' ' . $this->block_end_delim;

		$this->filecontents = $before . "\n" . $this->filecontents . "\n" . $after;
	}

	/**
     * Debug function - var_dump wrapped in '<pre></pre>' tags
     *
     * @access private
     * @param multiple var_dumps all the supplied arguments
     */
	private function _pre_var_dump ($args) {

		if ($this->debug) {
			echo '<pre>';
			var_dump(func_get_args());
			echo '</pre>';
		}
	}
} /* end of XTemplate class. */

/**
 * CachingXTemplate
 * Extension to XTemplate to provide block level and whole template caching facilities
 * Needs Web server writable directory
 *
 * @package XTemplate
 * @subpackage CachingXTemplate
 * @uses XTemplate
 * @author Jeremy Coates [cocomp@users.sourceforge.net]
 * @copyright Jeremy Coates / Co-Comp Ltd 2006-2007
 * @see license.txt BSD license
 * @since PHP 5
 * @link $HeadURL: https://xtpl.svn.sourceforge.net/svnroot/xtpl/trunk/caching_xtemplate.class.php $
 * @version $Id: caching_xtemplate.class.php 21 2007-05-29 18:01:15Z cocomp $
 *
 * @example Whole template level caching (e.g. the total parsed output for the file)
 * @example $xtpl = new CachingXTemplate('template.xtpl', '', null, 'main', true, 600, session_id(), './xcache', '.xcache');
 *
 * @example Alternatively (and perhaps more useful in real world):
 * @example Block level caching
 * @example $xtpl = new CachingXTemplate('template.xtpl', '', null, 'main', true, 0, session_id(), './xcache', '.xcache');
 * @example $xtpl->parse('main', 600);
 * @example Bear in mind that because XTemplate uses a reversed parsing tree the innermost blocks need to be parsed
 * @example first, therefore if you cache an outer block, don't be surprised when it's inner content blocks don't update!
 */
class CachingXTemplate extends XTemplate {

	/**
	 * Cache expiry time (seconds)
	 *
	 * @access public
	 * @var int
	 */
	public $cache_expiry	= 0;

	/**
	 * Cache file unique identifier
	 *
	 * @example session_id()
	 * @access public
	 * @var string
	 */
	public $cache_unique	= 'unique';

	/**
	 * Filename extension
	 *
	 * @example .xcache
	 * @access public
	 * @var string
	 */
	public $cache_ext		= '.xcache';

	/**
	 * Path to cache dir
	 * Needs to be writable by webserver
	 *
	 * @example ./xcache
	 * @access public
	 * @var string
	 */
	public $cache_dir		= './xcache';

	/**
	 * Flag showing whether template is cached
	 *
	 * @access private
	 * @var boolean
	 */
	private $_template_is_cached	= false;

	/**
	 * Cache expiry time
	 *
	 * @access private
	 * @var int
	 */
	private $_cache_expiry			= 0;

	/**
	 * File modified time
	 *
	 * @access private
	 * @var int
	 */
	private $_cache_filemtime		= 0;

	/**
	 * Override of parent constructor
	 *
	 * @access public
     * @param string $file Template file to work on
     * @param string $tpldir Location of template files (useful for keeping files outside web server root)
     * @param array $files Filenames lookup
     * @param string $mainblock Name of main block in the template
     * @param boolean $autosetup If true, run setup() as part of constuctor
	 * @param int $cache_expiry Seconds to cache for
	 * @param string $cache_unique Unique file id (e.g. session_id())
	 * @param string $cache_dir Cache folder
	 * @param string $cache_ext Cache file extension
	 */
	public function __construct($file, $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true, $cache_expiry = 0, $cache_unique = '', $cache_dir = './xcache', $cache_ext = '.xcache') {

		$this->restart($file, $tpldir, $files, $mainblock, $autosetup, $this->tag_start_delim, $this->tag_end_delim, $cache_expiry, $cache_unique, $cache_dir, $cache_ext);

	}

	/**
	 * Override of parent restart method
	 *
	 * @access public
	 * @param string $file Template file to work on
	 * @param string $tpldir Location of template files
	 * @param array $files Filenames lookup
	 * @param string $mainblock Name of main block in the template
	 * @param boolean $autosetup If true, run setup() as part of restarting
	 * @param string $tag_start {
	 * @param string $tag_end }
	 * @param int $cache_expiry Seconds to cache for
	 * @param string $cache_unique Unique file id (e.g. session_id())
	 * @param string $cache_dir Cache folder
	 * @param string $cache_ext Cache file extension
	 */
	public function restart ($file, $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true, $tag_start = '{', $tag_end = '}', $cache_expiry = 0, $cache_unique = '', $cache_dir = './xcache', $cache_ext = '.xcache') {

		if ($cache_expiry > 0) {
			$this->cache_expiry = $cache_expiry;
		}

		if (!empty($cache_unique)) {
			if (!preg_match('/^\./', $cache_unique)) {
				$cache_unique = '.' . $cache_unique;
			}
			$this->cache_unique = $cache_unique;
		}

		if (!empty($cache_dir)) {
			$this->cache_dir = $cache_dir;
		}

		if (!empty($cache_ext)) {
			if (!preg_match('/^\./', $cache_ext)) {
				$cache_ext = '.' . $cache_ext;
			}
			$this->cache_ext = $cache_ext;
		}

		// Call parent restart method but don't run setup yet!
		parent::restart($file, $tpldir, $files, $mainblock, false, $tag_start, $tag_end);

		if ($this->cache_expiry > 0) {
			$this->read_template_cache();
		}

		if (!$this->_template_is_cached && $autosetup) {
			$this->setup();
		}
	}

	/**
	 * Override of parent assign method
	 *
	 * @access public
     * @param string $name Variable to assign $val to
     * @param string / array $val Value to assign to $name
	 * @param boolean $magic_quotes
	 */
	public function assign ($name, $val = '', $magic_quotes = false) {

		if (!$this->_template_is_cached) {
			parent::assign($name, $val, $magic_quotes);
		}
	}

	/**
	 * Override of parent assign_file method
	 *
     * @access public
     * @param string $name Variable to assign $val to
     * @param string / array $val Values to assign to $name
	 */
	public function assign_file ($name, $val = '') {

		if (!$this->_template_is_cached) {
			parent::assign_file($name, $val);
		}
	}

	/**
	 * Override of parent parse method
	 *
     * @access public
     * @param string $bname Block name to parse
	 * @param int $cache_expiry Seconds to cache block for
	 */
	public function parse ($bname, $cache_expiry = 0) {

		if (!$this->_template_is_cached) {

			if (!$this->read_block_cache($bname, $cache_expiry)) {

				parent::parse($bname);

				$this->write_block_cache($bname, $cache_expiry);
			}
		}
	}

	/**
	 * Override of parent text method
	 *
     * @access public
     * @param string $bname Block name to return
     * @return string
	 */
	public function text ($bname = '') {

		$text = parent::text($bname);

		if (!$this->_template_is_cached && $this->cache_expiry > 0) {

			$this->write_template_cache();

		} elseif ($this->debug && $this->output_type == 'HTML') {

			$text_header = "<!-- CachingXTemplate debug:\n";

			if ($this->cache_expiry > 0) {

				$filename = $this->_get_filename();

				$file = $this->cache_dir . DIRECTORY_SEPARATOR . $filename . $this->cache_unique . $this->cache_ext;

				$text_header .= 'File: ' . $file . "\nExpires in: " . ($this->_cache_filemtime - $this->_cache_expiry) . " seconds -->\n";
			} else {
				$text_header .= "Template Cache (whole template) disabled -->\n";
			}

			$text = $text_header . $text;
		}

		return $text;
	}

	/**
	 * Read whole template cache file
	 *
	 * @access protected
	 */
	protected function read_template_cache () {

		$filename = $this->_get_filename();

		$file = $this->cache_dir . DIRECTORY_SEPARATOR . $filename . DIRECTORY_SEPARATOR . $this->cache_unique . $this->cache_ext;

		if ($this->cache_expiry > 0 && file_exists($file)) {

			$this->_cache_filemtime = filemtime($file);
			$this->_cache_expiry = time() - $this->cache_expiry;

			if ($this->_cache_filemtime >= $this->_cache_expiry) {
				if ($parsed_blocks = file_get_contents($file)) {
					$this->parsed_blocks = unserialize($parsed_blocks);
					$this->_template_is_cached = true;
				}
			} else {
				// Stale file
				if (is_writable($this->cache_dir) && is_writable($file)) {
					unlink($file);
				}
			}
		}
	}

	/**
	 * Write out whole template cache file
	 *
	 * @access protected
	 */
	protected function write_template_cache () {

		if ($this->cache_expiry > 0 && is_writable($this->cache_dir)) {

			$filename = $this->_get_filename();

			if (!file_exists($this->cache_dir . DIRECTORY_SEPARATOR . $filename)) {
				mkdir($this->cache_dir . DIRECTORY_SEPARATOR . $filename);
			}

			file_put_contents($this->cache_dir . DIRECTORY_SEPARATOR . $filename . DIRECTORY_SEPARATOR . $this->cache_unique . $this->cache_ext, serialize($this->parsed_blocks));
		}
	}

	/**
	 * Read block level cache file
	 *
	 * @access protected
	 * @param string $bname Block name to read from cache
	 * @param ing $cache_expiry Seconds to cache block for
	 * @return boolean
	 */
	protected function read_block_cache ($bname, $cache_expiry = 0) {

		$retval = false;

		$filename = $this->_get_filename();

		$file = $this->cache_dir . DIRECTORY_SEPARATOR . $filename . DIRECTORY_SEPARATOR . $bname . $this->cache_unique . $this->cache_ext;

		if ($cache_expiry > 0 && file_exists($file)) {

			$filemtime = filemtime($file);
			$cache_expiry = time() - $cache_expiry;

			if ($filemtime >= $cache_expiry) {
				if ($block = file_get_contents($file)) {
					$block = unserialize($block);
					if ($this->debug) {
						$block = "<!-- CachingXTemplate debug:\nFile: " . $file . "\nBlock: " . $bname . "\nExpires in: " . ($filemtime - $cache_expiry) . ' seconds -->' . "\n" . $block;
					}
					$this->parsed_blocks[$bname] = $block;
					$retval = true;
				}
			} else {
				// Stale file
				if (is_writable($this->cache_dir) && is_writable($file)) {
					unlink($file);
				}
			}
		}

		return $retval;
	}

	/**
	 * Write out block level cache file
	 *
	 * @access protected
	 * @param string $bname Block name to cache
	 * @param int $cache_expiry Seconds to cache block for
	 */
	protected function write_block_cache ($bname, $cache_expiry = 0) {

		if ($cache_expiry > 0 && is_writable($this->cache_dir)) {

			$filename = $this->_get_filename();

			if (!file_exists($this->cache_dir . DIRECTORY_SEPARATOR . $filename)) {
				mkdir($this->cache_dir . DIRECTORY_SEPARATOR . $filename);
			}

			file_put_contents($this->cache_dir . DIRECTORY_SEPARATOR . $filename . DIRECTORY_SEPARATOR . $bname . $this->cache_unique . $this->cache_ext, serialize($this->parsed_blocks[$bname]));
		}
	}

	/**
	 * Create the main part of the cache filename
	 *
	 * @access private
	 * @return string
	 */
	private function _get_filename () {

		$filename = $this->filename;
		if (!empty($this->tpldir)) {

			$filename = str_replace(DIRECTORY_SEPARATOR, '_', $this->tpldir . DIRECTORY_SEPARATOR) . $this->filename;
		}

		return $filename;
	}
}

?>
