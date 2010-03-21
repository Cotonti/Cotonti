<?php
/**
 * Main function library.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

// System requirements check
if (!defined('SED_INSTALL'))
{
	(function_exists('version_compare') && version_compare(PHP_VERSION, '5.1.0', '>=')) or die('Cotonti system requirements: PHP 5.1 or above.'); // TODO: Need translate
	extension_loaded('mbstring') or die('Cotonti system requirements: mbstring PHP extension must be loaded.'); // TODO: Need translate
}

// Group constants
define('COT_GROUP_DEFAULT', 0);
define('COT_GROUP_GUESTS', 1);
define('COT_GROUP_INACTIVE', 2);
define('COT_GROUP_BANNED', 3);
define('COT_GROUP_MEMBERS', 4);
define('COT_GROUP_SUPERADMINS', 5);

/* ======== Pre-sets ========= */

$out = array();
$plu = array();
$sys = array();
$usr = array();

$i = explode(' ', microtime());
$sys['starttime'] = $i[1] + $i[0];

//unset ($warnings, $moremetas, $morejavascript, $error_string,  $sed_cat, $sed_smilies, $sed_acc, $sed_catacc, $sed_rights, $sed_config, $sql_config, $sed_usersonline, $sed_plugins, $sed_groups, $rsedition, $rseditiop, $rseditios, $tcount, $qcount)

$cfg['svnrevision'] = '$Rev$'; //DO NOT MODIFY this is set by SVN automatically
$cfg['version'] = '0.7.0';
$cfg['dbversion'] = '0.7.0';

// Set default file permissions if not present in config
if (!isset($cfg['file_perms']))
{
	$cfg['file_perms'] = 0664;
}
if (!isset($cfg['dir_perms']))
{
	$cfg['dir_perms'] = 0777;
}

// For compatibility with PHP < 5.2

if (PHP_VERSION < '5.2.0')
{
	function mb_stripos($haystack, $needle, $offset = 0)
	{
		return stripos($haystack, $needle, $offset);
	}

	function mb_stristr($haystack, $needle)
	{
		return stristr($haystack, $needle);
	}

	function mb_strripos($haystack, $needle, $offset = 0)
	{
		return strripos($haystack, $needle, $offset);
	}

	function mb_strstr($haystack, $needle)
	{
		return strstr($haystack, $needle);
	}
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
function sed_auth($area, $option, $mask = 'RWA')
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

	foreach ($masks as $k => $ml)
	{
		if (empty($mn[$ml]))
		{
			$sys['auth_log'][] = $area.'.'.$option.'.'.$ml.'=0';
			$res[] = FALSE;
		}
		elseif ($option == 'any')
		{
			$cnt = 0;

			if (is_array($usr['auth'][$area]))
			{
				foreach ($usr['auth'][$area] as $k => $g)
				{
					$cnt += (($g & $mn[$ml]) == $mn[$ml]);
				}
			}
			$cnt = ($cnt == 0 && $usr['auth']['admin']['a'] && $ml == 'A') ? 1 : $cnt;

			$sys['auth_log'][] = ($cnt > 0) ? $area.'.'.$option.'.'.$ml.'=1' : $area.'.'.$option.'.'.$ml.'=0';
			$res[] = ($cnt > 0) ? TRUE : FALSE;
		}
		else
		{
			$sys['auth_log'][] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? $area.'.'.$option.'.'.$ml.'=1' : $area.'.'.$option.'.'.$ml.'=0';
			$res[] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? TRUE : FALSE;
		}
	}
	return (count($res) == 1) ? $res[0] : $res;
}

/**
 * Builds Access Control List (ACL) for a specific user
 *
 * @param int $userid User ID
 * @param int $maingrp User main group
 * @return array
 */
function sed_auth_build($userid, $maingrp = 0)
{
	global $db_auth, $db_groups_users;

	$groups = array();
	$authgrid = array();
	$tmpgrid = array();

	if ($userid == 0 || $maingrp == 0)
	{
		$groups[] = 1;
	}
	else
	{
		$groups[] = $maingrp;
		$sql = sed_sql_query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid=$userid");

		while ($row = sed_sql_fetcharray($sql))
		{
			$groups[] = $row['gru_groupid'];
		}
	}

	$sql_groups = implode(',', $groups);
	$sql = sed_sql_query("SELECT auth_code, auth_option, auth_rights FROM $db_auth WHERE auth_groupid IN (".$sql_groups.") ORDER BY auth_code ASC, auth_option ASC");

	while ($row = sed_sql_fetcharray($sql))
	{
		$authgrid[$row['auth_code']][$row['auth_option']] |= $row['auth_rights'];
	}

	return $authgrid;
}

/**
 * Block user if he is not allowed to access the page
 *
 * @param bool $allowed Authorization result
 * @return bool
 */
function sed_block($allowed)
{
	if (!$allowed)
	{
		global $sys;
		sed_redirect(sed_url('message', 'msg=930&'.$sys['url_redirect'], '', true));
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

	if ($usr['id'] < 1)
	{
		sed_redirect(sed_url('message', "msg=930&".$sys['url_redirect'], '', true));
	}
	return FALSE;
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
 * @param string $mode Parsing mode, on of the following: 'str' (str_replace), 'pcre' (preg_replace) and 'callback' (preg_replace_callback)
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
	if ($priority >= 0 && $priority < 256)
	{
		$bbc['priority'] = (int) $priority;
	}
	if (!empty($plug))
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
	if ($id > 0)
	{
		return sed_sql_delete($db_bbcode, "bbc_id = $id") == 1;
	}
	elseif (!empty($plug))
	{
		return sed_sql_delete($db_bbcode, "bbc_plug = '".sed_sql_prep($plug)."'");
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
 * @param string $mode Parsing mode, on of the following: 'str' (str_replace), 'pcre' (preg_replace) and 'callback' (preg_replace_callback)
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
	if (!empty($name))
	{
		$bbc['name'] = $name;
	}
	if (!empty($mode))
	{
		$bbc['mode'] = $mode;
	}
	if (!empty($pattern))
	{
		$bbc['pattern'] = $pattern;
	}
	if (!empty($replacement))
	{
		$bbc['replacement'] = $replacement;
	}
	if ($priority >= 0 && $priority < 256)
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
	$sed_bbcodes = array();
	$sed_bbcodes_post = array();
	$sed_bbcode_containers = ''; // required for auto-close
	$bbc_cntr = array();
	$i = 0;
	$j = 0;
	$res = sed_sql_query("SELECT * FROM $db_bbcode WHERE bbc_enabled = 1 ORDER BY bbc_priority");
	while ($row = sed_sql_fetchassoc($res))
	{
		if ($row['bbc_postrender'] == 1)
		{
			foreach ($row as $key => $val)
			{
				$sed_bbcodes_post[$j][str_replace('bbc_', '', $key)] = $val;
			}
			$j++;
		}
		else
		{
			foreach ($row as $key => $val)
			{
				$sed_bbcodes[$i][str_replace('bbc_', '', $key)] = $val;
			}
			$i++;
		}
		if ($row['bbc_container'] == 1 && !isset($bbc_cntr[$row['bbc_name']]))
		{
			$sed_bbcode_containers .= $row['bbc_name'].'|';
			$bbc_cntr[$row['bbc_name']] = 1;
		}
	}
	sed_sql_freeresult($res);
	if (!empty($sed_bbcode_containers))
	{
		$sed_bbcode_containers = mb_substr($sed_bbcode_containers, 0, -1);
	}
}

/**
 * Clears bbcode cache
 */
function sed_bbcode_clearcache()
{
	global $cot_cache;
	$cot_cache->db->remove('sed_bbcodes', 'system');
	$cot_cache->db->remove('sed_bbcodes_post', 'system');
	$cot_cache->db->remove('sed_bbcode_containers', 'system');
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

	// BB auto-close
	$bbc = array();
	if (!$post && preg_match_all('#\[(/)?('.$sed_bbcode_containers.')(=[^\]]*)?\]#i', $text, $mt, PREG_SET_ORDER))
	{
		$cdata = '';
		// Count all unclosed bbcode entries
		for ($i = 0, $cnt = count($mt); $i < $cnt; $i++)
		{
				$bb = mb_strtolower($mt[$i][2]);
				if ($mt[$i][1] == '/')
				{
					if (empty($cdata))
					{
						// Protect from "[/foo] [/bar][foo][bar]" trick
						if ($bbc[$bb] > 0) $bbc[$bb]--;
						// else echo 'ERROR: invalid closing bbcode detected';
					}
					elseif ($bb == $cdata)
					{
						$bbc[$bb]--;
						$cdata = '';
					}
				}
				elseif (empty($cdata))
				{
					// Count opening tag in
					$bbc[$bb]++;
					if ($bb == 'code' || $bb == 'highlight')
					{
						// Ignore bbcodes in constant data
						$cdata = $bb;
					}
				}
		}
		// Close all unclosed tags. Produces non XHTML-compliant output
		// (doesn't take tag order and semantics into account) but fixes the layout
		if (count($bbc) > 0)
		{
			foreach($bbc as $bb => $c)
			{
				$text .= str_repeat("[/$bb]", $c);
			}
		}
	}
	// Done, ready to parse bbcodes
	$cnt = $post ? count($sed_bbcodes_post) : count($sed_bbcodes);
	for ($i = 0; $i < $cnt; $i++)
	{
		$bbcode = ($post) ? $sed_bbcodes_post[$i] : $sed_bbcodes[$i];
		switch($bbcode['mode'])
		{
			case 'str':
				$text = str_ireplace($bbcode['pattern'], $bbcode['replacement'], $text);
			break;

			case 'pcre':
				$text = preg_replace('`'.$bbcode['pattern'].'`mis', $bbcode['replacement'], $text);
			break;

			case 'callback':
				$code = 'global $cfg, $sys, $usr, $L, $skin, $sed_groups;'.$bbcode['replacement'];
				$text = preg_replace_callback('`'.$bbcode['pattern'].'`mis', create_function('$input', $code), $text);
			break;
		}
	}

	return $text;
}

/**
 * Neutralizes bbcodes in text
 *
 * @param string $text Source text
 * @return string
 */
function sed_bbcode_cdata($text)
{
	$res = $text;
	//$res = preg_replace('`&(?!amp;)`i', '&amp;$1', $res);
	$res = str_replace('[', '&#091;', $res);
	$res = str_replace(']', '&#093;', $res);
	return $res;
}

/**
 * Takes an UTF-8 string and returns an array of ints representing the
 * Unicode characters. Astral planes are supported ie. the ints in the
 * output can be > 0xFFFF. Occurrances of the BOM are ignored. Surrogates
 * are not allowed.
 *
 * Returns false if the input string isn't a valid UTF-8 octet sequence.
 *
 * @author Henri Sivonen
 * @license Mozilla Public License (MPL)
 * @copyright (c) 2003 Henri Sivonen
 * @param string $str Unicode string
 * @return array
 */
function utf8ToUnicode(&$str)
{
	$mState = 0; // cached expected number of octets after the current octet
	// until the beginning of the next UTF8 character sequence
	$mUcs4  = 0; // cached Unicode character
	$mBytes = 1; // cached expected number of octets in the current sequence

	$out = array();

	$len = strlen($str);
	for ($i = 0; $i < $len; $i++)
	{
		$in = ord($str{$i});
		if (0 == $mState)
		{
			// When mState is zero we expect either a US-ASCII character or a
			// multi-octet sequence.
			if (0 == (0x80 & ($in)))
			{
				// US-ASCII, pass straight through.
				$out[] = $in;
				$mBytes = 1;
			}
			elseif (0xC0 == (0xE0 & ($in)))
			{
				// First octet of 2 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x1F) << 6;
				$mState = 1;
				$mBytes = 2;
			}
			elseif (0xE0 == (0xF0 & ($in)))
			{
				// First octet of 3 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x0F) << 12;
				$mState = 2;
				$mBytes = 3;
			}
			elseif (0xF0 == (0xF8 & ($in)))
			{
				// First octet of 4 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x07) << 18;
				$mState = 3;
				$mBytes = 4;
			}
			elseif (0xF8 == (0xFC & ($in)))
			{
				/* First octet of 5 octet sequence.
				 *
				 * This is illegal because the encoded codepoint must be either
				 * (a) not the shortest form or
				 * (b) outside the Unicode range of 0-0x10FFFF.
				 * Rather than trying to resynchronize, we will carry on until the end
				 * of the sequence and let the later error handling code catch it.
				 */
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x03) << 24;
				$mState = 4;
				$mBytes = 5;
			}
			elseif (0xFC == (0xFE & ($in)))
			{
				// First octet of 6 octet sequence, see comments for 5 octet sequence.
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 1) << 30;
				$mState = 5;
				$mBytes = 6;
			}
			else
			{
				/* Current octet is neither in the US-ASCII range nor a legal first
				 * octet of a multi-octet sequence.
				 */
				return false;
			}
		}
		else
		{
			// When mState is non-zero, we expect a continuation of the multi-octet
			// sequence
			if (0x80 == (0xC0 & ($in)))
			{
				// Legal continuation.
				$shift = ($mState - 1) * 6;
				$tmp = $in;
				$tmp = ($tmp & 0x0000003F) << $shift;
				$mUcs4 |= $tmp;

				if (0 == --$mState)
				{
					/* End of the multi-octet sequence. mUcs4 now contains the final
					 * Unicode codepoint to be output
					 *
					 * Check for illegal sequences and codepoints.
					 */

					// From Unicode 3.1, non-shortest form is illegal
					if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
						((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
						((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
						(4 < $mBytes) ||
						// From Unicode 3.2, surrogate characters are illegal
						(($mUcs4 & 0xFFFFF800) == 0xD800) ||
						// Codepoints outside the Unicode range are illegal
						($mUcs4 > 0x10FFFF))
					{
						return false;
					}
					if (0xFEFF != $mUcs4)
					{
						// BOM is legal but we don't want to output it
						$out[] = $mUcs4;
					}
					//initialize UTF8 cache
					$mState = 0;
					$mUcs4  = 0;
					$mBytes = 1;
				}
			}
			else
			{
				/* ((0xC0 & (*in) != 0x80) && (mState != 0))
				 *
				 * Incomplete multi-octet sequence.
				 */
				return false;
			}
		}
	}
	return $out;
}

/**
 * JavaScript HTML obfuscator to protect some parts (like email) from bots
 *
 * @param string $text Source text
 * @return string
 */
function sed_obfuscate($text)
{
	$enc_string = '[';
	$ut = utf8ToUnicode($text);
	$length = count($ut);
	for ($i = 0; $i < $length; $i++)
	{
		$enc_string .= $ut[$i].',';
	}
	$enc_string = substr($enc_string, 0, -1).']';
	$name = 'a'.sed_unique(8);
	$script = '<script type="text/javascript">var '.$name.' = '.$enc_string.','.$name.'_d = ""; for (var ii = 0; ii < '.$name.'.length; ii++) { var c = '.$name.'[ii]; '.$name.'_d += String.fromCharCode(c); } document.write('.$name.'_d)</script>';
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

	if ($cfg['parser_custom'] && function_exists('sed_custom_parse'))
	{
		$text = sed_custom_parse($text, $parse_bbcodes, $parse_smilies, $parse_newlines);
	}

	if (!$cfg['parser_disable'])
	{
		$code = array();
		$unique_seed = $sys['unique'];
		$ii = 10000;

		$text = sed_parse_autourls($text);

		if ($parse_smilies && is_array($sed_smilies))
		{
			foreach($sed_smilies as $k => $v)
			{
				$ii++;
				$key = '**'.$ii.$unique_seed.'**';
				$code[$key]= '<img class="aux smiley" src="./images/smilies/'.$v['file'].'" alt="'.htmlspecialchars($v['code']).'" />';
				$text = preg_replace('#(^|\s)'.preg_quote($v['code']).'(\s|$)#', '$1'.$key.'$2', $text);
				if (htmlspecialchars($v['code']) != $v['code'])
				{
					// Fix for cc inserts
					$text = preg_replace('#(^|\s)'.preg_quote(htmlspecialchars($v['code'])).'(\s|$)#', '$1'.$key.'$2', $text);
				}
			}
		}

		if ($parse_bbcodes)
		{
			$text = sed_bbcode_parse($text);
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
			$text = preg_replace('#<(/?)(p|hr|ul|ol|li|blockquote|table|tr|td|th|div|h1|h2|h3|h4|h5)(.*?)>(\s*)<br />#', '<$1$2$3>', $text);
			$text = preg_replace_callback('#<pre[^>]*>(.+?)</pre>#sm', 'sed_parse_pre', $text);
		}
	}
	return $text;
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
	if ($cfg['parser_custom'] && function_exists('sed_custom_post_parse'))
	{
		$text = sed_custom_post_parse($text, $area);
	}

	if (!$cfg['parser_disable'] && (empty($area) || $cfg["parsebbcode$area"]))
	{
		$text = sed_bbcode_parse($text, true);
	}
	return $text;
}

/**
 * Cuts the page after 'more' tag or after the first page (if multipage)
 *
 * @param string ptr $html Page body
 * @return bool
 */
function sed_cut_more(&$html)
{
	$cutted = false;
	$mpos = mb_strpos($html, '<!--more-->');
	if ($mpos === false)
	{
		$mpos = mb_strpos($html, '[more]');
	}
	if ($mpos !== false)
	{
		$html = mb_substr($html, 0, $mpos);
		$cutted = true;
	}
	$mpos = mb_strpos($html, '[newpage]');
	if ($mpos !== false)
	{
		$html = mb_substr($html, 0, $mpos);
		$cutted = true;
	}
	if (mb_strpos($html, '[title]'))
	{
		$html = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $html);
	}
	return $cutted;
}

/**
 * Truncates text.
 *
 * Cuts a string to the length of $length
 *
 * @param string  ptr $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param boolean $considerhtml If true, HTML tags would be handled correctly *
 * @param boolean $exact If false, $text will not be cut mid-word
 * @return boolean true if string is trimmed.
 */
function sed_string_truncate(&$html, $length = 100, $considerhtml = true, $exact = false)
{
	if ($considerhtml)
	{
		// if the plain text is shorter than the maximum length, return the whole text
		if (mb_strlen(preg_replace('/<.*?>/', '', $html)) <= $length)
		{
			return false;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $html, $lines, PREG_SET_ORDER);

		$total_length = 0;
		$open_tags = array();
		$truncate = '';

		foreach ($lines as $line_matchings)
		{
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1]))
			{
				// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]))
				{
					// do nothing
					// if tag is a closing tag (f.e. </b>)
				}
				elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings))
				{
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false)
					{
						unset($open_tags[$pos]);
					}
					// if tag is an opening tag (f.e. <b>)
				}
				elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings))
				{
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, mb_strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}

			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length)
			{
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE))
				{
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity)
					{
						if ($entity[1]+1-$entities_length <= $left)
						{
							$left--;
							$entities_length += mb_strlen($entity[0]);
						}
						else
						{
							// no more characters left
							break;
						}
					}
				}
				$truncate .= mb_substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			}
			else
			{
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}

			// if the maximum length is reached, get off the loop
			if ($total_length >= $length)
			{
				break;
			}
		}
	}
	else
	{
		if (mb_strlen($html) <= $length)
		{
			return false;
		}
		else
		{
			$truncate = mb_substr($html, 0, $length);
		}
	}

	if (!$exact)
	{
		// ...search the last occurance of a space...
		if (mb_strrpos($truncate, ' ')>0)
		{
			$pos1 = mb_strrpos($truncate, ' ');
			$pos2 = mb_strrpos($truncate, '>');
			$spos = ($pos2 < $pos1) ? $pos1 : ($pos2+1);
			if (isset($spos))
			{
				// ...and cut the text in this position
				$truncate = mb_substr($truncate, 0, $spos);
			}
		}
	}
	if ($considerhtml)
	{
		// close all unclosed html-tags
		foreach ($open_tags as $tag)
		{
			$truncate .= '</'.$tag.'>';
		}
	}
	$html =  $truncate;
	return true;

}

/*
 * =========================== Output forming functions ===========================
 */

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

/**
 * Builds category path
 *
 * @param string $cat Category code
 * @param string $mask Format mask
 * @return string
 */
function sed_build_catpath($cat, $mask = 'link_catpath')
{
	global $sed_cat, $cfg;
	$mask = str_replace('%1$s', '{$url}', $mask);
	$mask = str_replace('%2$s', '{$title}', $mask);
	if ($cfg['homebreadcrumb'])
	{
		$tmp[] = sed_rc('link_catpath', array(
			'url' => $cfg['mainurl'],
			'title' => htmlspecialchars($cfg['maintitle'])
		));
	}
	$pathcodes = explode('.', $sed_cat[$cat]['path']);
	$last = count($pathcodes) - 1;
	$list = defined('SED_LIST');
	foreach ($pathcodes as $k => $x)
	{
		$tmp[] = ($list && $k === $last) ? htmlspecialchars($sed_cat[$x]['title'])
			: sed_rc($mask, array(
				'url' =>sed_url('list', 'c='.$x),
				'title' => htmlspecialchars($sed_cat[$x]['title'])
			));
	}
	return implode(' '.$cfg['separator'].' ', $tmp);
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
	return '<a href="'.sed_url('users', 'f=country_'.$flag).'">'.$sed_countries[$flag].'</a>';
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
	if ($hide)
	{
		return $L['Hidden'];
	}
	elseif (!empty($email) && preg_match('#^\w[\._\w\-]+@[\w\.\-]+\.[a-z]+$#', $email))
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
	global $sed_countries;
	$flag = (empty($flag)) ? '00' : $flag;
	return '<a href="'.sed_url('users', 'f=country_'.$flag).'" title="'.$sed_countries[$flag].'"><img class="flag" src="images/flags/'.$flag.'.png" alt="'.$flag.'" /></a>';
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
			return '<a href="'.sed_url('users', 'gm='.$grpid).'">'.$sed_groups[$grpid]['title'].'</a> ('.$L['Hidden'].')';
		}
		else
		{
			return $L['Hidden'];
		}
	}
	else
	{
		return '<a href="'.sed_url('users', 'gm='.$grpid).'">'.$sed_groups[$grpid]['title'].'</a>';
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
		$readonly = (!$edit || $usr['level'] < $sed_groups[$k]['level'] || $k==COT_GROUP_GUESTS || $k==COT_GROUP_INACTIVE || $k==COT_GROUP_BANNED || ($k==COT_GROUP_SUPERADMINS && $userid==1)) ? "disabled=\"disabled\"" : '';
		$readonly_maingrp = (!$edit || $usr['level'] < $sed_groups[$k]['level'] || $k==COT_GROUP_GUESTS || ($k==COT_GROUP_INACTIVE && $userid==1) || ($k==COT_GROUP_BANNED && $userid==1)) ? "disabled=\"disabled\"" : '';

		if ($member[$k] || $edit)
		{
			if (!($sed_groups[$k]['hidden'] && !sed_auth('users', 'a', 'A')))
			{
				$res .= "<input type=\"radio\" class=\"radio\" name=\"rusermaingrp\" value=\"$k\" ".$checked_maingrp." ".$readonly_maingrp." /> \n";
				$res .= "<input type=\"checkbox\" class=\"checkbox\" name=\"rusergroupsms[$k]\" ".$checked." $readonly />\n";
				$res .= ($k == COT_GROUP_GUESTS) ? $sed_groups[$k]['title'] : "<a href=\"".sed_url('users', 'gm='.$k)."\">".$sed_groups[$k]['title']."</a>";
				$res .= ($sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
				$res .= "<br />";
			}
		}
	}

	return $res;
}

/**
 * Returns IP Search link
 *
 * @param string $ip IP mask
 * @return string
 */
function sed_build_ipsearch($ip)
{
	global $sys;
	if (!empty($ip))
	{
		return '<a href="'.sed_url('admin', 'm=tools&p=ipsearch&a=search&id='.$ip.'&x='.$sys['xk']).'">'.$ip.'</a>'; // TODO - to resorses
	}
	return '';
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
// TODO this function should be replaced with some hook-based integration
function sed_build_pm($user)
{
	global $usr, $L, $R;
	return '<a href="'.sed_url('pm', 'm=send&to='.$user).'" title="'.$L['pm_sendnew'].'">'.$R['pm_icon'].'</a>';
}

/**
 * Builds ratings for an item
 *
 * @param $code Item code
 * @param $url Base url
 * @param $display Display available for edit
 * @return array
 */
function sed_build_ratings($code, $url, $display)
{
	global $db_ratings, $db_rated, $db_users, $cfg, $usr, $sys, $L, $R;
	static $called = false;

	list($usr['auth_read_rat'], $usr['auth_write_rat'], $usr['isadmin_rat']) = sed_auth('ratings', 'a');

	if ($cfg['disable_ratings'] || !$usr['auth_read_rat'])
	{
		return (array('', ''));
	}

	if (SED_AJAX)
	{
		$rcode = sed_import('rcode', 'G', 'ALP');
		if (!empty($rcode))
		{
			$code = $rcode;
		}
	}

	$sql = sed_sql_query("SELECT * FROM $db_ratings WHERE rating_code='$code' LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{
		$rating_average = $row['rating_average'];
		$yetrated = TRUE;
		if ($rating_average<1)
		{
			$rating_average = 1;
		}
		elseif ($rating_average>10)
		{
			$rating_average = 10;
		}
		$rating_cntround = round($rating_average, 0);
	}
	else
	{
		$yetrated = FALSE;
		$rating_average = 0;
		$rating_cntround = 0;
	}

	if (SED_AJAX && !empty($rcode))
	{
		ob_clean();
		echo $rating_cntround;
		ob_flush();
		exit;
	}

	$rating_fancy =  '';
	for ($i = 1; $i <= 10; $i++)
	{
		$star_class = ($i <= $rating_cntround) ? 'star-rating star-rating-on' : 'star-rating star-rating-readonly';
		$star_margin = (in_array(($i / 2), array(1, 2, 3, 4, 5))) ? '-8' : '0';
		$rating_fancy .= '<div style="width: 8px;" class="'.$star_class.'"><a style="margin-left: '.$star_margin.'px;" title="'.$L['rat_choice'.$i].'">'.$i.'</a></div>';
	}
	if (!$display)
	{
		return array($rating_fancy, '');
	}

	$sep = (mb_strpos($url, '?') !== false) ? '&amp;' : '?';

	$inr = sed_import('inr', 'G', 'ALP');
	$newrate = sed_import('rate_'.$code,'P', 'INT');

	$newrate = (!empty($newrate)) ? $newrate : 0;

	if (!$cfg['ratings_allowchange'])
	{
		$alr_rated = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM ".$db_rated." WHERE rated_userid=".$usr['id']." AND rated_code = '".sed_sql_prep($code)."'"), 0, 'COUNT(*)');
	}
	else
	{
		$alr_rated = 0;
	}

	if ($inr == 'send' && $newrate >= 0 && $newrate <= 10 && $usr['auth_write_rat'] && $alr_rated <= 0)
	{
		/* == Hook for the plugins == */
		$extp = sed_getextplugins('ratings.send.first');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$sql = sed_sql_query("DELETE FROM $db_rated WHERE rated_code='".sed_sql_prep($code)."' AND rated_userid='".$usr['id']."' ");

		if (!$yetrated)
		{
			$sql = sed_sql_query("INSERT INTO $db_ratings (rating_code, rating_state, rating_average, rating_creationdate, rating_text) VALUES ('".sed_sql_prep($code)."', 0, ".(int)$newrate.", ".(int)$sys['now_offset'].", '') ");
		}

		$sql = ($newrate) ? sed_sql_query("INSERT INTO $db_rated (rated_code, rated_userid, rated_value) VALUES ('".sed_sql_prep($code)."', ".(int)$usr['id'].", ".(int)$newrate.")") : '';
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code'");
		$rating_voters = sed_sql_result($sql, 0, "COUNT(*)");
		if ($rating_voters > 0)
		{
			$ratingnewaverage = sed_sql_result(sed_sql_query("SELECT AVG(rated_value) FROM $db_rated WHERE rated_code='$code'"), 0, "AVG(rated_value)");
			$sql = sed_sql_query("UPDATE $db_ratings SET rating_average='$ratingnewaverage' WHERE rating_code='$code'");
		}
		else
		{
			$sql = sed_sql_query("DELETE FROM $db_ratings WHERE rating_code='$code' ");
		}

		/* == Hook for the plugins == */
		$extp = sed_getextplugins('ratings.send.done');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		sed_redirect($url);
	}

	if ($usr['id'] > 0)
	{
		$sql1 = sed_sql_query("SELECT rated_value FROM $db_rated WHERE rated_code='$code' AND rated_userid='".$usr['id']."' LIMIT 1");

		if ($row1 = sed_sql_fetcharray($sql1))
		{
			$alreadyvoted = ($cfg['ratings_allowchange']) ? FALSE : TRUE;
			$rating_uservote = $L['rat_alreadyvoted']." (".$row1['rated_value'].")";
		}
	}

	$t = new XTemplate(sed_skinfile('ratings'));

	if (!$called && $usr['id'] > 0 && !$alreadyvoted)
	{
		// Link JS and CSS
		$sep = (mb_strpos($url, '?') !== false) ? '&' : '?';
		$t->assign('RATINGS_AJAX_REQUEST', $url.$sep.'ajax=1');
		$t->parse('RATINGS.RATINGS_INCLUDES');
		$called = true;
	}
	/* == Hook for the plugins == */
	$extp = sed_getextplugins('ratings.main');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sep = (mb_strpos($url, '?') !== false) ? '&amp;' : '?';

	if ($yetrated)
	{
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code' ");
		$rating_voters = sed_sql_result($sql, 0, "COUNT(*)");
		$rating_average = $row['rating_average'];
		$rating_since = $L['rat_since']." ".date($cfg['dateformat'], $row['rating_creationdate'] + $usr['timezone'] * 3600);
		if ($rating_average<1)
		{
			$rating_average = 1;
		}
		elseif ($ratingaverage > 10)
		{
			$rating_average = 10;
		}

		$rating = round($rating_average,0);
		$rating_averageimg = sed_rc('icon_rating_stars', array('val' => $rating));
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code' ");
		$rating_voters = sed_sql_result($sql, 0, "COUNT(*)");
	}
	else
	{
		$rating_voters = 0;
		$rating_since = '';
		$rating_average = 0;
		$rating_averageimg = '';
	}

	$t->assign(array(
		'RATINGS_CODE' => $code,
		'RATINGS_AVERAGE' => $rating_average,
		'RATINGS_RATING' => $rating,
		'RATINGS_AVERAGEIMG' => $rating_averageimg,
		'RATINGS_VOTERS' => $rating_voters,
		'RATINGS_SINCE' => $rating_since,
		'RATINGS_FANCYIMG' => $rating_fancy,
		'RATINGS_USERVOTE' => $rating_uservote
	));

	/* == Hook for the plugins == */
	$extp = sed_getextplugins('ratings.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$vote_block = ($usr['id'] > 0 && !$alreadyvoted) ? 'NOTVOTED.' : 'VOTED.';
	for ($i = 1; $i <= 10; $i++)
	{
		$checked = ($i == $rating_cntround) ? 'checked="checked"' : '';
		$t->assign(array(
			'RATINGS_ROW_VALUE' => $i,
			'RATINGS_ROW_TITLE' => $L['rat_choice'.$i],
			'RATINGS_ROW_CHECKED' => $checked,
		));
		$t->parse('RATINGS.'.$vote_block.'RATINGS_ROW');
	}
	if ($vote_block == 'NOTVOTED.')
	{
		$t->assign("RATINGS_FORM_SEND", $url.$sep.'inr=send');
		$t->parse('RATINGS.NOTVOTED');
	}
	else
	{
		$t->parse('RATINGS.VOTED');
	}
	$t->parse('RATINGS');
	$res = $t->text('RATINGS');

	return array($res, '', $rating_average);
}

/**
 * Returns stars image for user level
 *
 * @param int $level User level
 * @return unknown
 */
function sed_build_stars($level)
{
	global $skin, $R;

	if($level>0 and $level<100)
	{
		$stars = floor($level / 10) + 1;
		return sed_rc('icon_stars', array('val' => $stars));
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
	global $Ls;

	$gap = $t2 - $t1;

	if ($gap<=0 || !$t2 || $gap>94608000)
	{
		$result = '';
	}
	elseif ($gap<60)
	{
		$result = sed_declension($gap,$Ls['Seconds']);
	}
	elseif ($gap<3600)
	{
		$gap = floor($gap/60);
		$result = sed_declension($gap,$Ls['Minutes']);
	}
	elseif ($gap<86400)
	{
		$gap1 = floor($gap/3600);
		$gap2 = floor(($gap-$gap1*3600)/60);
		$result = sed_declension($gap1,$Ls['Hours']).' ';
		if ($gap2>0)
		{
			$result .= sed_declension($gap2,$Ls['Minutes']);
		}
	}
	else
	{
		$gap = floor($gap/86400);
		$result = sed_declension($gap,$Ls['Days']);
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

	$result .= sed_declension($tz,$Ls['Hours']);

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

	if (!empty($text))
	{
		if (mb_strpos($text, 'http://') !== 0)
		{
			$text='http://'. $text;
		}
		$text = htmlspecialchars($text);
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

	if ($id == 0 && !empty($user))
	{
		return $user;
	}
	elseif ($id == 0)
	{
		return '';
	}
	else
	{
		return (!empty($user)) ? '<a href="'.sed_url('users', 'm=details&id='.$id.'&u='.$user).'">'.$user.'</a>' : '?';
	}
}

/**
 * Returns user avatar image
 *
 * @param string $image Image src
 * @return string
 */
function sed_build_userimage($image, $type='none')
{
	if ($type == 'avatar')
	{
		if (empty($image))
		{
			$image = 'datas/defaultav/blank.png';
		}
		return '<img src="'.$image.'" alt="" class="avatar" />';
	}
	elseif ($type == 'photo')
	{
		if (!empty($image))
		{
			return '<img src="'.$image.'" alt="" class="photo" />';
		}

	}
	elseif ($type == 'sig')
	{
		if (!empty($image))
		{
			return '<img src="'.$image.'" alt="" class="signature" />';
		}
	}
	else
	{
		if (!empty($image))
		{
			return '<img src="'.$image.'" alt="" />';
		}
	}
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
	if (!$cfg['usertextimg'])
	{
		$bbcodes_img = array(
			'\[img\]([^\[]*)\[/img\]' => '',
			'\[thumb=([^\[]*)\[/thumb\]' => '',
			'\[t=([^\[]*)\[/t\]' => '',
			'\[list\]' => '',
			'\[style=([^\[]*)\]' => '',
			'\[quote' => '',
			'\[code' => ''
		);

		foreach($bbcodes_img as $bbcode => $bbcodehtml)
		{
			$text = preg_replace("#$bbcode#i", $bbcodehtml, $text);
		}
	}
	return sed_parse($text, $cfg['parsebbcodeusertext'], $cfg['parsesmiliesusertext'], 1);
}

/**
 * Makes HTML sequences safe
 *
 * @deprecated
 * @param string $text Source string
 * @return string
 */
function sed_cc($text)
{
	/*$text = str_replace(
	array('{', '<', '>' , '$', '\'', '"', '\\', '&amp;', '&nbsp;'),
	array('&#123;', '&lt;', '&gt;', '&#036;', '&#039;', '&quot;', '&#92;', '&amp;amp;', '&amp;nbsp;'), $text);
	return $text;*/
	trigger_error('sed_cc() is deprecated since Cotonti Genoa, use htmlspecialchars() instead');
	return htmlspecialchars($text);
}

/**
 * Checks GET anti-XSS parameter
 *
 * @return bool
 */
function sed_check_xg()
{
	if (isset($_GET['x']))
	{
		return true;
	}

	sed_redirect(sed_url('message', 'msg=950', '', true));
}

/**
 * Checks POST anti-XSS parameter
 *
 * @return bool
 */
function sed_check_xp()
{
	return (defined('SED_NO_ANTIXSS') || defined('SED_AUTH')) ?
		($_SERVER['REQUEST_METHOD'] == 'POST') : isset($_POST['x']);
}

/**
 * Truncates a post and makes sure parsing is correct
 *
 * @param string $text Post text
 * @param int $max_chars Max. length
 * @param bool $parse_bbcodes Parse bbcodes
 * @return unknown
 */
function sed_cutpost($text, $max_chars, $parse_bbcodes = true)
{
	$text = $max_chars == 0 ? $text : sed_cutstring(strip_tags($text), $max_chars);
	// Fix partial cuttoff
	$text = preg_replace('#\[[^\]]*?$#', '...', $text);
	// Parse the BB-codes or skip them
	if ($parse_bbcodes)
	{
		// Parse it
		$text = sed_parse($text);
	}
	else $text = preg_replace('#\[[^\]]+?\]#', '', $text);
	return $text;
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
	if (mb_strlen($res)>$l)
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
			$source = imagecreatefromgif ($img_big);
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
		sed_redirect(sed_url('message', "msg=950", '', true));
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

	if (defined('SED_DEBUG') && SED_DEBUG)
	{
		echo '<br /><pre>';
		debug_print_backtrace();
		echo '</pre>';
	}

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
		sed_redirect(sed_url('message', "msg=940", '', true));
	}
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
	global $sed_plugins, $usr, $cfg, $cot_cache;

	$extplugins = array();

	if (is_array($sed_plugins[$hook]))
	{
		foreach($sed_plugins[$hook] as $k)
		{
			if (sed_auth('plug', $k['pl_code'], $cond))
			{
				$extplugins[] = $cfg['plugins_dir'].'/'.$k['pl_code'].'/'.$k['pl_file'].'.php';
			}
		}
	}

	// Trigger cache handlers
	$cot_cache && $cot_cache->trigger($hook);

	return $extplugins;
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
			$v = (isset($_GET[$name])) ? $_GET[$name] : NULL;
			$log = TRUE;
			break;

		case 'P':
			$v = (isset($_POST[$name])) ? $_POST[$name] : NULL;
			$log = TRUE;
			if ($filter=='ARR') { return($v); }
			break;

		case 'R':
			$v = (isset($_REQUEST[$name])) ? $_REQUEST[$name] : NULL;
			$log = TRUE;
			break;

		case 'C':
			$v = (isset($_COOKIE[$name])) ? $_COOKIE[$name] : NULL;
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

	if ($v=='' || $v == NULL)
	{
		return($v);
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
			if (is_numeric($v))
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
			if ($v == $f)
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
			if ($v == $f)
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
			if ($v == '1' || $v == 'on')
			{
				$pass = TRUE;
				$v = '1';
			}
			elseif ($v=='0' || $v=='off')
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
			if (is_numeric($v) && $v >= 0 && $v <= 100 && floor($v)==$v)
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
	if ($pass)
	{
		return($v);
	}
	else
	{
		if ($log)
		{
			sed_log_sed_import($source, $filter, $name, $v);
		}
		if ($dieonerror)
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
 * Returns path to include file
 *
 * @param string $name Name of the API or the part
 * @param mixed $module Module name or FALSE if it is a core API file
 * @return string File path
 */
function sed_incfile($name, $module = false)
{
	global $cfg;
	if ($module)
	{
		if ($module == 'admin' || $module == 'users' || $module == 'message')
		{
			return $cfg['system_dir']."/$module/$module.$name.php";
		}
		else
		{
			return $cfg['modules_dir']."/$module/$module.$name.php";
		}
	}
	else
	{
		return $cfg['system_dir']."/$name.php";
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

	if ($fp = @fopen($file, 'r'))
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
	global $cfg, $lang;
	if ($cfg['jquery'])
	{
		$result .= '<script type="text/javascript" src="js/jquery.js"></script>';
		if ($cfg['turnajax'])
		{
			$result .= '<script type="text/javascript" src="js/jquery.history.js"></script>';
			$more .= empty($more) ? 'ajaxEnabled = true;' : "\najaxEnabled = true;";
		}
	}
	$result .= '<script type="text/javascript" src="js/base.js"></script>';
	if (!empty($more))
	{
	$result .= '<script type="text/javascript">
//<![CDATA[
'.$more.'
//]]>
</script>';
	}
	return $result;
}

/**
 * Returns a language file path for a plugin or FALSE on error.
 *
 * @param string $name Plugin name
 * @param bool $type Langfile type: 'plug', 'module' or 'core'
 * @param mixed $default Default (fallback) language code
 * @return bool
 */
function sed_langfile($name, $type = 'plug', $default = 'en')
{
	global $cfg, $lang;
	if ($type == 'module')
	{
		if (@file_exists($cfg['modules_dir']."/$name/lang/$name.$lang.lang.php"))
		{
			return $cfg['modules_dir']."/$name/lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['modules_dir']."/$name/lang/$name.$default.lang.php";
		}
	}
	elseif ($type == 'core')
	{
		if (@file_exists($cfg['lang_dir']."/$lang/$name.$lang.lang.php"))
		{
			return $cfg['lang_dir']."/$lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['lang_dir']."/$default/$name.$default.lang.php";
		}
	}
	else
	{
		if (@file_exists($cfg['plugins_dir']."/$name/lang/$name.$lang.lang.php"))
		{
			return $cfg['plugins_dir']."/$name/lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['plugins_dir']."/$name/lang/$name.$default.lang.php";
		}
	}
}

/**
 * Load smilies from current pack
 */
function sed_load_smilies()
{
	global $sed_smilies;
	$sed_smilies = array();
	if (!file_exists('./images/smilies/set.js')) return;

	// A simple JSON parser and decoder
	$json = '';
	$started = false;
	$fp = fopen('./images/smilies/set.js', 'r');
	$i = -1;
	$prio = array();
	$code = array();
	$file = array();
	while (!feof($fp))
	{
		$line = fgets($fp);
		if ($line == '];') break;
		if ($started)
		{
			$line = trim($line, " \t\r\n");
			if ($line == '{')
			{
				$i++;
			}
			elseif ($line != '},')
			{
				if (preg_match('#^(\w+)\s*:\s*"?(.+?)"?,?$#', $line, $m))
				{
					switch($m[1])
					{
						case 'prio':
							$prio[$i] = intval($m[2]);
							break;
						case 'code':
							$code[$i] = str_replace('\\\\', '\\', $m[2]);
							break;
						case 'file':
							$file[$i] = $m[2];
							break;
					}
				}
			}
		}
		elseif (mb_strpos($line, 'smileSet') !== false)
		{
			$started = true;
		}
	}
	fclose($fp);

	// Sort the result
	array_multisort($prio, SORT_ASC, $code, $file);
	$cnt = count($code);
	for ($i = 0; $i < $cnt; $i++)
	{
		$sed_smilies[$i] = array(
				'code' => $code[$i],
				'file' => $file[$i]
		);
	}
}

/**
 * Loads comlete category structure into array
 */
function sed_load_structure()
{
	global $db_structure, $db_extra_fields, $cfg, $L, $sed_cat, $sed_extrafields;
	$sed_cat = array();
	$sql = sed_sql_query("SELECT * FROM $db_structure ORDER BY structure_path ASC");

	while ($row = sed_sql_fetcharray($sql))
	{
		if (!empty($row['structure_icon']))
		{
			$row['structure_icon'] = '<img src="'.$row['structure_icon'].'" alt="'.htmlspecialchars($row['structure_title']).'" title="'.htmlspecialchars($row['structure_title']).'" />'; // TODO - to resorses
		}

		$path2 = mb_strrpos($row['structure_path'], '.');

		$row['structure_tpl'] = (empty($row['structure_tpl'])) ? $row['structure_code'] : $row['structure_tpl'];

		if ($path2 > 0)
		{
			$path1 = mb_substr($row['structure_path'], 0, ($path2));
			$path[$row['structure_path']] = $path[$path1].'.'.$row['structure_code'];
			$tpath[$row['structure_path']] = $tpath[$path1].' '.$cfg['separator'].' '.$row['structure_title'];
			$row['structure_tpl'] = ($row['structure_tpl'] == 'same_as_parent') ? $parent_tpl : $row['structure_tpl'];
		}
		else
		{
			$path[$row['structure_path']] = $row['structure_code'];
			$tpath[$row['structure_path']] = $row['structure_title'];
		}

		$order = explode('.', $row['structure_order']);
		$parent_tpl = $row['structure_tpl'];

		$sed_cat[$row['structure_code']] = array(
			'path' => $path[$row['structure_path']],
			'tpath' => $tpath[$row['structure_path']],
			'rpath' => $row['structure_path'],
			'tpl' => $row['structure_tpl'],
			'title' => $row['structure_title'],
			'desc' => $row['structure_desc'],
			'icon' => $row['structure_icon'],
			'group' => $row['structure_group'],
			'ratings' => $row['structure_ratings'],
			'order' => $order[0],
			'way' => $order[1]
		);

		if (is_array($sed_extrafields['structure']))
		{
			foreach ($sed_extrafields['structure'] as $row_c)
			{
				$sed_cat[$row['structure_code']][$row_c['field_name']] = $row['structure_'.$row_c['field_name']];
			}
		}

		/* == Hook == */
		$extp = sed_getextplugins('structure');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */
	}
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
<meta name=\"generator\" content=\"Cotonti http://www.cotonti.com\" />
<meta http-equiv=\"expires\" content=\"Fri, Apr 01 1974 00:00:00 GMT\" />
<meta http-equiv=\"pragma\" content=\"no-cache\" />
<meta http-equiv=\"cache-control\" content=\"no-cache\" />
<meta http-equiv=\"last-modified\" content=\"".gmdate("D, d M Y H:i:s")." GMT\" />
<link rel=\"shortcut icon\" href=\"favicon.ico\" />
";
		return ($result);
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
	if ($hour === false)  $hour  = date ('G');
	if ($minute === false) $minute = date ('i');
	if ($second === false) $second = date ('s');
	if ($month === false)  $month  = date ('n');
	if ($date === false)  $date  = date ('j');
	if ($year === false)  $year  = date ('Y');

	return mktime ((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $date, (int) $year);
}

/**
 * Converts MySQL date into UNIX timestamp
 *
 * @param string $date Date in MySQL format
 * @return int UNIX timestamp
 */
function sed_date2stamp($date)
{
	if ($date == '0000-00-00') return 0;
	preg_match('#(\d{4})-(\d{2})-(\d{2})#', $date, $m);
	return mktime(0, 0, 0, (int) $m[2], (int) $m[3], (int) $m[1]);
}

/**
 * Converts UNIX timestamp into MySQL date
 *
 * @param int $stamp UNIX timestamp
 * @return string MySQL date
 */
function sed_stamp2date($stamp)
{
	return date('Y-m-d', $stamp);
}

/**
 * Updates online users table
 * @global array $cfg
 * @global array $sys
 * @global array $usr
 * @global array $out
 * @global string $db_online
 * @global Cache $cot_cache
 * @global array $sed_usersonline
 * @global string $location Location string
 */
function sed_online_update()
{
	global $cfg, $sys, $usr, $out, $db_online, $cot_cache, $sed_usersonline, $location, $Ls;
	if (!$cfg['disablewhosonline'])
	{
		if ($location != $sys['online_location']
			|| !empty($sys['sublocaction']) && $sys['sublocaction'] != $sys['online_subloc'])
		{
			if ($usr['id'] > 0)
			{
				if (empty($sys['online_location']))
				{
					sed_sql_query("INSERT INTO $db_online (online_ip, online_name, online_lastseen, online_location, online_subloc, online_userid, online_shield, online_hammer)
						VALUES ('".$usr['ip']."', '".sed_sql_prep($usr['name'])."', ".(int)$sys['now'].", '".sed_sql_prep($location)."',  '".sed_sql_prep($sys['sublocation'])."', ".(int)$usr['id'].", 0, 0)");
				}
				else
				{
					sed_sql_query("UPDATE $db_online SET online_lastseen='".$sys['now']."', online_location='".sed_sql_prep($location)."', online_subloc='".sed_sql_prep($sys['sublocation'])."', online_hammer=".(int)$sys['online_hammer']." WHERE online_userid=".$usr['id']);
				}
			}
			else
			{
				if (empty($sys['online_location']))
				{
					sed_sql_query("INSERT INTO $db_online (online_ip, online_name, online_lastseen, online_location, online_subloc, online_userid, online_shield, online_hammer)
						VALUES ('".$usr['ip']."', 'v', ".(int)$sys['now'].", '".sed_sql_prep($location)."', '".sed_sql_prep($sys['sublocation'])."', -1, 0, 0)");
				}
				else
				{
					sed_sql_query("UPDATE $db_online SET online_lastseen='".$sys['now']."', online_location='".$location."', online_subloc='".sed_sql_prep($sys['sublocation'])."', online_hammer=".(int)$sys['online_hammer']." WHERE online_ip='".$usr['ip']."'");
				}
			}
		}
		if ($cot_cache && $cot_cache->mem && $cot_cache->mem->exists('whosonline', 'system'))
		{
			$whosonline_data = $cot_cache->mem->get('whosonline', 'system');
			$sys['whosonline_vis_count'] = $whosonline_data['vis_count'];
			$sys['whosonline_reg_count'] = $whosonline_data['reg_count'];
			$out['whosonline_reg_list'] = $whosonline_data['reg_list'];
			unset($whosonline_data);
		}
		else
		{
			$online_timedout = $sys['now'] - $cfg['timedout'];
			sed_sql_query("DELETE FROM $db_online WHERE online_lastseen < $online_timedout");
			$sys['whosonline_vis_count'] = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_online WHERE online_name='v'"), 0, 0);
			$sql_o = sed_sql_query("SELECT DISTINCT o.online_name, o.online_userid FROM $db_online o WHERE o.online_name != 'v' ORDER BY online_name ASC");
			$sys['whosonline_reg_count'] = sed_sql_numrows($sql_o);
			$ii_o = 0;
			while ($row_o = sed_sql_fetcharray($sql_o))
			{
				$out['whosonline_reg_list'] .= ($ii_o > 0) ? ', ' : '';
				$out['whosonline_reg_list'] .= sed_build_user($row_o['online_userid'], htmlspecialchars($row_o['online_name']));
				$sed_usersonline[] = $row_o['online_userid'];
				$ii_o++;
			}
			sed_sql_freeresult($sql_o);
			unset($ii_o, $sql_o, $row_o);
			if ($cot_cache && $cot_cache->mem)
			{
				$whosonline_data = array(
					'vis_count' => $sys['whosonline_vis_count'],
					'reg_count' => $sys['whosonline_reg_count'],
					'reg_list' => $out['whosonline_reg_list']
				);
				$cot_cache->mem->store('whosonline', $whosonline_data, 'system', 30);
			}
		}
		$sys['whosonline_all_count'] = $sys['whosonline_reg_count'] + $sys['whosonline_vis_count'];
		$out['whosonline'] = ($cfg['disablewhosonline']) ? '' : sed_declension($sys['whosonline_reg_count'], $Ls['Members']).', '.sed_declension($sys['whosonline_vis_count'], $Ls['Guests']);
	}
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
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ==== */

	$output = str_ireplace('</form>', sed_xp().'</form>', $output);

	return($output);
}

/**
 * Page navigation (pagination) builder. Uses URL transformation and resource strings,
 * returns an associative array, containing:
 * ['prev'] - first and previous page buttons
 * ['main'] - buttons with page numbers, including current
 * ['next'] - next and last page buttons
 * ['last'] - last page with number
 *
 * @param string $module Site area or script name
 * @param mixed $params URL parameters as array or parameter string
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param string $characters It is symbol for parametre which transfer pagination
 * @param string $hash Hash part of the url (including #)
 * @param bool $ajax Add AJAX support
 * @param string $target_div Target div ID if $ajax is true
 * @param string $ajax_module Site area name for ajax if different from $module
 * @param string $ajax_params URL parameters for ajax if $ajax_module is not empty
 * @return array
 */
function sed_pagenav($module, $params, $current, $entries, $perpage, $characters = 'd', $hash = '',
	$ajax = false, $target_div = '', $ajax_module = '', $ajax_params = array())
{
	if (function_exists('sed_pagenav_custom'))
	{
		// For custom pagination functions in plugins
		return sed_pagenav_custom($module, $params, $current, $entries, $perpage, $characters, $hash,
			$ajax, $target_div, $ajax_module, $ajax_params);
	}

	if ($entries <= $perpage)
	{
		return '';
	}

	global $L, $R;

	$each_side = 3; // Links each side

	is_array($params) ? $args = $params : parse_str($params, $args);
	if ($ajax)
	{
		$ajax_rel = !empty($ajax_module);
		$ajax_rel && is_string($ajax_params) ? parse_str($ajax_params, $ajax_args) : $ajax_args = $ajax_params;
		$event = ' class="ajax"';
		if (empty($target_div))
		{
			$base_rel = $ajax_rel ? ' rel="get;' : '';
		}
		else
		{
			$base_rel = $ajax_rel ? ' rel="get-'.$target_div.';' : ' rel="get-'.$target_div.'"';
		}
	}
	else
	{
		$ajax_rel = false;
		$event = '';
	}
	$rel = '';

	$totalpages = ceil($entries / $perpage);
	$currentpage = floor($current / $perpage) + 1;
	$cur_left = $currentpage - $each_side;
	if ($cur_left < 1) $cur_left = 1;
	$cur_right = $currentpage + $each_side;
	if ($cur_right > $totalpages) $cur_right = $totalpages;

	// Main block

	$before = '';
	$pages = '';
	$after = '';
	$i = 1;
	$n = 0;
	while ($i < $cur_left)
	{
		$args[$characters] = ($i - 1) * $perpage;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$before .= sed_rc('link_pagenav_main', array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $i
		));
		if ($i < $cur_left - 2)
		{
			$before .= $R['link_pagenav_gap'];
		}
		elseif ($i == $cur_left - 2)
		{
			$args[$characters] = $i * $perpage;
			if ($ajax_rel)
			{
				$ajax_args[$characters] = $args[$characters];
				$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
			}
			else
			{
				$rel = $base_rel;
			}
			$before .= sed_rc('link_pagenav_main', array(
					'url' => sed_url($module, $args, $hash),
					'event' => $event,
					'rel' => $rel,
					'num' => $i + 1
			));
		}
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	for ($j = $cur_left; $j <= $cur_right; $j++)
	{
		$args[$characters] = ($j - 1) * $perpage;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$rc = $j == $currentpage ? 'current' : 'main';
		$pages .= sed_rc('link_pagenav_'.$rc, array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $j
		));
	}
	while ($i <= $cur_right)
	{
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	while ($i < $totalpages)
	{
		if ($i > $cur_right + 2)
		{
			$after .= $R['link_pagenav_gap'];
		}
		elseif ($i == $cur_right + 2)
		{
			$args[$characters] = ($i - 2 ) * $perpage;
			if ($ajax_rel)
			{
				$ajax_args[$characters] = $args[$characters];
				$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
			}
			else
			{
				$rel = $base_rel;
			}
			$after .= sed_rc('link_pagenav_main', array(
					'url' => sed_url($module, $args, $hash),
					'event' => $event,
					'rel' => $rel,
					'num' => $i - 1
			));
		}
		$args[$characters] = ($i - 1) * $perpage;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$after .= sed_rc('link_pagenav_main', array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $i
		));
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	$pages = $before.$pages.$after;

	// Previous/next

	if ($current > 0)
	{
		$prev_n = $current - $perpage;
		if ($prev_n < 0) { $prev_n = 0; }
		$args[$characters] = $prev_n;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$prev = sed_rc('link_pagenav_prev', array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $prev_n + 1
		));
		$args[$characters] = 0;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$first = sed_rc('link_pagenav_first', array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => 1
		));
	}

	if (($current + $perpage) < $entries)
	{
		$next_n = $current + $perpage;
		$args[$characters] = $next_n;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$next = sed_rc('link_pagenav_next', array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $next_n + 1
		));
		$last_n = ($totalpages - 1) * $perpage;
		$args[$characters] = $last_n;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', sed_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$last = sed_rc('link_pagenav_last', array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $last_n + 1
		));
   		$lastn  = (($last +  $perpage)<$totalpages) ?
		sed_rc('link_pagenav_main', array(
			'url' => sed_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $last_n / $perpage + 1
		)): FALSE;
	}

	return array(
		'prev' => $first.$prev,
		'main' => $pages,
		'next' => $next.$last,
		'last' => $lastn
	);
}

/**
 * Renders page navigation bar
 *
 * @deprecated Siena 0.7.0 - 23.01.2010, use sed_pagenav() instead
 * @see sed_pagenav
 * @param string $url Basic URL
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param string $characters It is symbol for parametre which transfer pagination
 * @param bool $ajax Add AJAX support
 * @param string $target_div Target div ID if $ajax is true
 * @return string
 */
function sed_pagination($url, $current, $entries, $perpage, $characters = 'd', $ajax = false, $target_div = '')
{
	if (function_exists('sed_pagination_custom'))
	{
		// For custom pagination functions in plugins
		return sed_pagination_custom($url, $current, $entries, $perpage, $characters, $onclick, $object);
	}

	if ($entries <= $perpage)
	{
		return '';
	}
	$each_side = 3; // Links each side
	$address = $url.((mb_strpos($url, '?') !== false) ? '&amp;' : '?').$characters.'=';

	$totalpages = ceil($entries / $perpage);
	$currentpage = floor($current / $perpage) + 1;
	$cur_left = $currentpage - $each_side;
	if ($cur_left < 1) $cur_left = 1;
	$cur_right = $currentpage + $each_side;
	if ($cur_right > $totalpages) $cur_right = $totalpages;

	$event = $ajax ? ' class="ajax"' : '';
	$name = $ajax && !empty($target_div) ? ' name="'.$taget_div.'"' : '';

	$before = '';
	$pages = '';
	$after = '';
	$i = 1;
	$n = 0;
	while ($i < $cur_left)
	{
		$k = ($i - 1) * $perpage;
		$before .= '<span class="pagenav_pages"><a href="'.$address.$k.'"'.$event.$name.'>'.$i.'</a></span>';
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	for ($j = $cur_left; $j <= $cur_right; $j++)
	{
		$k = ($j - 1) * $perpage;
		$class = $j == $currentpage ? 'current' : 'pages';
		$pages .= '<span class="pagenav_'.$class.'"><a href="'.$address.$k.'"'.$event.$name.'>'.$j.'</a></span>';
	}
	while ($i <= $cur_right)
	{
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	while ($i < $totalpages)
	{
		$k = ($i - 1) * $perpage;
		$after .= '<span class="pagenav_pages"><a href="'.$address.$k.'"'.$event.$name.'>'.$i.'</a></span>';
		$i *= ($n % 2) ? 5 : 2;
		$n++;
	}
	$pages = $before.$pages.$after;

	return $pages;
}

/**
 * Renders page navigation previous/next buttons
 *
 * @deprecated Siena 0.7.0 - 23.01.2010, use sed_pagenav() instead
 * @see sed_pagenav()
 * @param string $url Basic URL
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param bool $res_array Return results as array
 * @param string $characters It is symbol for parametre which transfer pagination
 * @param bool $ajax Add AJAX support
 * @param string $target_div Target div ID if $ajax is true
 * @return mixed
 */
function sed_pagination_pn($url, $current, $entries, $perpage, $res_array = FALSE, $characters = 'd', $ajax = false, $target_div = '')
{
	if (function_exists('sed_pagination_pn_custom'))
	{
		// For custom pagination functions in plugins
		return sed_pagination_pn_custom($url, $current, $entries, $perpage, $res_array, $characters, $onclick, $object);
	}

	global $L;

	$address = $url.((mb_strpos($url, '?') !== false) ? '&amp;' : '?').$characters.'=';
	$totalpages = ceil($entries / $perpage);
	$currentpage = floor($current / $perpage) + 1;

	$event = $ajax ? ' class="ajax"' : '';
	$name = $ajax && !empty($target_div) ? ' name="'.$taget_div.'"' : '';

	if ($current > 0)
	{
		$prev_n = $current - $perpage;
		if ($prev_n < 0) { $prev_n = 0; }
		$prev = '<span class="pagenav_prev"><a href="'.$address.$prev_n.'"'.$event.$name.'>'.$L['pagenav_prev'].'</a></span>';
		$first = '<span class="pagenav_first"><a href="'.$address.'0"'.$event.$name.'>'.$L['pagenav_first'].'</a></span>';
	}

	if (($current + $perpage) < $entries)
	{
		$next_n = $current + $perpage;
		$next = '<span class="pagenav_next"><a href="'.$address.$next_n.'"'.$event.$name.'>'.$L['pagenav_next'].'</a></span>';
		$last_n = ($totalpages - 1) * $perpage;
		$last = '<span class="pagenav_last"><a href="'.$address.$last_n.'"'.$event.$name.'>'.$L['pagenav_last'].'</a></span>';
	}

	$res_l = $first.$prev;
	$res_r = $next.$last;
	return $res_array ? array($res_l, $res_r) : $res_l.' '.$res_r;
}

/**
 * Resource string formatter function. Takes a string with predefined variable substitution, e.g.
 * 'My {$pet} likes {$food}. And {$pet} is hungry!' and an assotiative array of substitution values, e.g.
 * array('pet' => 'rabbit', 'food' => 'carrots') and assembles a formatted result. If {$var} cannot be found
 * in $args, it will be taken from global scope. You can also use parameter strings instead of arrays, e.g.
 * 'pet=rabbit&food=carrots'. Or omit the second parameter in case all substitutions are globals.
 *
 * @global array $R Resource strings
 * @param string $name Name of the $R item or a resource string itself
 * @param array $params Associative array of arguments or a parameter string
 * @return string Assembled resource string
 */
function sed_rc($name, $params = array())
{
	global $R;
	$res = isset($R[$name]) ? $R[$name] : $name;
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	if (preg_match_all('#\{\$(.+?)\}#', $res, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			$var = $m[1];
			$res = str_replace($m[0], (isset($args[$var]) ? $args[$var] : $GLOBALS[$var]), $res);
		}
	}
	return $res;
}

/**
 * Quick link resource pattern
 *
 * @param string $url Link href
 * @param string $text Tag contents
 * @param mixed $attrs Additional attributes as a string or an associative array
 * @return string HTML link
 */
function sed_rc_link($url, $text, $attrs = '')
{
	if (is_array($attrs))
	{
		foreach ($attrs as $key => $val)
		{
			$link_attrs .= ' '.$key.'="'.$val.'"';
		}
	}
	else $link_attrs = $attrs;
	return '<a href="'.$url.'"'.$link_attrs.'>'.$text.'</a>';
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
		<meta http-equiv=\"content-type\" content=\"text/html; charset=".$cfg['charset']."\" />
		<meta http-equiv=\"refresh\" content=\"0; url=".SED_ABSOLUTE_URL.$url."\" />
		<title>Redirecting...</title></head>
		<body>Redirecting to <a href=\"".SED_ABSOLUTE_URL.$url."\">".$cfg['mainurl']."/".$url."</a>
		</body>
		</html>";
		header("Refresh: 0; URL=".SED_ABSOLUTE_URL.$url);
		echo($output);
		exit;
	}
	else
	{
		header('Location: '.SED_ABSOLUTE_URL.$url);
		exit;
	}
}

/**
 * Performs actions required right before shutdown
 */
function sed_shutdown()
{
	global $cot_cache;
	while (ob_get_level() > 0)
	{
		ob_end_flush();
	}
	$cot_cache = null; // Need to destroy before DB connection is lost
	sed_sql_close();
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
		$result .= "<option value=\"$x\" $selected>".htmlspecialchars($x)."</option>";
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

	if (!$sed_countries) include_once sed_langfile('countries', 'core');

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
 * @param int $max_year Max. year possible
 * @return string
 */
function sed_selectbox_date($utime, $mode, $ext='', $max_year = 2030)
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
	for ($i = 1902; $i < $max_year; $i++)
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
 * Returns language selection dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_lang($check, $name)
{
	global $sed_languages, $sed_countries, $cfg;

	$handle = opendir($cfg['lang_dir'].'/');
	while ($f = readdir($handle))
	{
		if ($f[0] != '.')
		{ $langlist[] = $f; }
	}
	closedir($handle);
	sort($langlist);

	$result = "<select name=\"$name\" size=\"1\">";
	while (list($i,$x) = each($langlist))
	{
		$selected = ($x==$check) ? "selected=\"selected\"" : '';
		$lng = (empty($sed_languages[$x])) ? $sed_countries[$x] : $sed_languages[$x];
		$result .= "<option value=\"$x\" $selected>".$lng." (".$x.")</option>";
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
	$handle = opendir('./skins/');
	while ($f = readdir($handle))
	{
		if (mb_strpos($f, '.') === FALSE && is_dir('./skins/'.$f))
		{ $skinlist[] = $f; }
	}
	closedir($handle);
	sort($skinlist);

	$result = '<select name="'.$name.'" size="1">';
	while (list($i,$x) = each($skinlist))
	{
		$selected = ($x==$check) ? 'selected="selected"' : '';
		$skininfo = "./skins/$x/$x.php";
		if (file_exists($skininfo))
		{
			$info = sed_infoget($skininfo);
			$result .= (!empty($info['Error'])) ? '<option value="'.$x.'" '.$selected.'>'.$x.' ('.$info['Error'].')' : '<option value="'.$x.'" '.$selected.'>'.$info['Name'];
		}
		else
		{
			$result .= '<option value="'.$x.'" $selected>'.$x;
		}
		$result .= '</option>';
	}
	$result .= '</select>';

	return $result;
}

/**
 * Returns skin selection dropdown
 *
 * @param string $skinname Skin name
 * @param string $name Dropdown name
 * @param string $theme Selected theme
 * @return string
 */
function sed_selectbox_theme($skinname, $name, $theme)
{
	global $skin_themes;

	if (empty($skin_themes))
	{
		if (file_exists("./skins/$skinname/$skinname.css"))
		{
			$skin_themes = array($skinname => $skinname);
		}
		else
		{
			$skin_themes = array('style' => $skinname);
		}
	}

	$result = '<select name="'.$name.'" size="1">';
	foreach($skin_themes as $x => $tname)
	{
		$selected = ($x==$theme) ? 'selected="selected"' : '';
		$result .= '<option value="'.$x.'" '.$selected.'>'.$tname.'</option>';
	}
	$result .= '</select>';

	return $result;
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
	header('Content-Type: '.$contenttype.'; charset='.$cfg['charset']);
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
		if ($hammer>$cfg['shieldzhammer'])
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
 * @param mixed $base Item name (string), or base names (array)
 * @param mixed $plug Plugin flag (bool), or '+' (string) to probe plugin
 * @return string
 */
function sed_skinfile($base, $plug = false)
{
	global $usr, $cfg;

	if (is_string($base) && mb_strpos($base, '.') !== false)
	{
		$base = explode('.', $base);
	}
	if (!is_array($base))
	{
		$base = array($base);
	}

	$basename = $base[0];

	if ((defined('SED_ADMIN')
		|| defined('SED_MESSAGE') && $_SESSION['s_run_admin']))
	{
		$admn = true;
	}

	if ($plug === '+')
	{
		$plug = false;
		if (defined('SED_PLUG'))
		{
			global $e;

			if (!empty($e))
			{
				$plug = true;
				$basename = $e;
				if ($cfg['enablecustomhf'])
				{
					$base[] = $e;
				}
			}
		}
	}

	if ($plug)
	{
		$scan_prefix[] = './skins/'.$usr['skin'].'/plugins/';
		if ($usr['skin'] != $cfg['defaultskin'])
		{
			$scan_prefix[] = './skins/'.$cfg['defaultskin'].'/plugins/';
		}
		$scan_prefix[] = $cfg['plugins_dir'].'/'.$basename.'/tpl/';
	}
	else
	{
		$scan_prefix[] = './skins/'.$usr['skin'].'/'.$basename.'/';
		if ($usr['skin'] != $cfg['defaultskin'])
		{
			$scan_prefix[] = './skins/'.$cfg['defaultskin'].'/'.$basename.'/';
		}
		if ($admn)
		{
			$scan_prefix[] = $cfg['system_dir'].'/admin/tpl/';
		}
		else
		{
			$scan_prefix[] = $cfg['modules_dir'].'/'.$basename.'/tpl/';
		}
	}
	$scan_prefix[] = './skins/'.$usr['skin'].'/';
	if ($usr['skin'] != $cfg['defaultskin'])
	{
		$scan_prefix[] = './skins/'.$cfg['defaultskin'].'/';
	}

	$base_depth = count($base);
	for ($i = $base_depth; $i > 0; $i--)
	{
		$levels = array_slice($base, 0, $i);
		$skinfile = implode('.', $levels).'.tpl';
		foreach ($scan_prefix as $pfx)
		{
			if (file_exists($pfx.$skinfile))
			{
				return $pfx.$skinfile;
			}
		}
	}

//	throw new Exception('Template file <em>'.implode('.', $base).'.tpl</em> was not found. Please check your skin.');
	return '';
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
	global $sys;
	return $sys['xk'];
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

	sed_sql_query("INSERT INTO $db_stats (stat_name, stat_value) VALUES ('".sed_sql_prep($name)."', 1)");
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
	return (sed_sql_numrows($sql) > 0) ? (int) sed_sql_result($sql, 0, 'stat_value') : FALSE;
}

/**
 * Increments stats
 *
 * @param string $name Parameter name
 * @param int $value Increment step
 */
function sed_stat_inc($name, $value = 1)
{
	global $db_stats;
	sed_sql_query("UPDATE $db_stats SET stat_value=stat_value+$value WHERE stat_name='$name'");
}

/**
 * Inserts new stat or increments value if it already exists
 *
 * @param string $name Parameter name
 * @param int $value Increment step
 */
function sed_stat_update($name, $value = 1)
{
	global $db_stats;
	sed_sql_query("INSERT INTO $db_stats (stat_name, stat_value)
		VALUES ('".sed_sql_prep($name)."', 1)
		ON DUPLICATE KEY UPDATE stat_value=stat_value+$value");
}

/*
 * ==========================================================================================
 */

/**
 * Tries to detect and fetch a user theme or returns FALSE on error.
 *
 * @global array $usr User object
 * @global array $cfg Configuration
 * @global array $out Output vars
 * @return mixed
 */
function sed_themefile()
{
	global $usr, $cfg, $out;

	if (file_exists('./skins/'.$usr['skin'].'/'.$usr['theme'].'.css'))
	{
		return './skins/'.$usr['skin'].'/'.$usr['theme'].'.css';
	}
	elseif (file_exists('./skins/'.$usr['skin'].'/css/'))
	{
		if (file_exists('./skins/'.$usr['skin'].'/css/'.$usr['theme'].'.css'))
		{
			return './skins/'.$usr['skin'].'/css/'.$usr['theme'].'.css';
		}
		elseif (file_exists('./skins/'.$usr['skin'].'/css/'.$cfg['defaulttheme'].'.css'))
		{
			$out['notices'] .= $L['com_themefail'];
			$usr['theme'] = $cfg['defaulttheme'];
			return './skins/'.$usr['skin'].'/css/'.$cfg['defaulttheme'].'.css';
		}
	}
	elseif (file_exists('./skins/'.$usr['skin']))
	{
		if (file_exists('./skins/'.$usr['skin'].'/'.$cfg['defaulttheme'].'.css'))
		{
			$out['notices'] .= $L['com_themefail'];
			$usr['theme'] = $cfg['defaulttheme'];
			return './skins/'.$usr['skin'].'/'.$cfg['defaulttheme'].'.css';
		}
		elseif (file_exists('./skins/'.$usr['skin'].'/'.$usr['skin'].'.css'))
		{
			$out['notices'] .= $L['com_themefail'];
			$usr['theme'] = $usr['skin'];
			return './skins/'.$usr['skin'].'/'.$usr['skin'].'.css';
		}
		elseif (file_exists('./skins/'.$usr['skin'].'/style.css'))
		{
			$out['notices'] .= $L['com_themefail'];
			$usr['theme'] = 'style';
			return './skins/'.$usr['skin'].'/style.css';
		}
	}

	$out['notices'] .= $L['com_themefail'];
	if (file_exists('./skins/'.$cfg['defaultskin'].'/'.$cfg['defaulttheme'].'.css'))
	{
		$usr['skin'] = $cfg['defaultskin'];
		$usr['theme'] = $cfg['defaulttheme'];
		return './skins/'.$cfg['defaultskin'].'/'.$cfg['defaulttheme'].'.css';
	}
	elseif (file_exists('./skins/'.$cfg['defaultskin'].'/css/'.$cfg['defaulttheme'].'.css'))
	{
		$usr['skin'] = $cfg['defaultskin'];
		$usr['theme'] = $cfg['defaulttheme'];
		return './skins/'.$cfg['defaultskin'].'/css/'.$cfg['defaulttheme'].'.css';
	}
	else
	{
		return false;
	}
}

/**
 * Generates a title string by replacing submasks with assigned values
 *
 * @param string $area Area maskname or actual mask
 * @param array $params An associative array of available parameters
 * @return string
 */
function sed_title($mask, $params = array())
{
	global $cfg;
	$res = (!empty($cfg[$mask])) ? $cfg[$mask] : $mask;
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	if (preg_match_all('#\{(.+?)\}#', $res, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			$var = $m[1];
			$res = str_replace($m[0], htmlspecialchars($args[$var], ENT_COMPAT, 'UTF-8', false), $res);
		}
	}
	return $res;
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
 * Loads URL Transformation Rules
 */
function sed_load_urltrans()
{
	global $sed_urltrans;
	$sed_urltrans = array();
	$fp = fopen('./datas/urltrans.dat', 'r');
	// Rules
	while ($line = trim(fgets($fp), " \t\r\n"))
	{
		$parts = explode("\t", $line);
		$rule = array();
		$rule['trans'] = $parts[2];
		$parts[1] == '*' ? $rule['params'] = array() : mb_parse_str($parts[1], $rule['params']);
		foreach($rule['params'] as $key => $val)
		{
			if (mb_strpos($val, '|') !== false)
			{
				$rule['params'][$key] = explode('|', $val);
			}
		}
		$sed_urltrans[$parts[0]][] = $rule;
	}
	fclose($fp);
}

/**
 * Transforms parameters into URL by following user-defined rules
 *
 * @param string $name Site area or script name
 * @param mixed $params URL parameters as array or parameter string
 * @param string $tail URL postfix, e.g. anchor
 * @param bool $header Set this TRUE if the url will be used in HTTP header rather than body output
 * @return string
 */
function sed_url($name, $params = '', $tail = '', $header = false)
{
	global $cfg, $sed_urltrans;
	// Preprocess arguments
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	$area = empty($sed_urltrans[$name]) ? '*' : $name;
	// Find first matching rule
	$url = $sed_urltrans['*'][0]['trans']; // default rule
	$rule = array();
	if (!empty($sed_urltrans[$area]))
	{
		foreach($sed_urltrans[$area] as $rule)
		{
			$matched = true;
			foreach($rule['params'] as $key => $val)
			{
				if (empty($args[$key])
					|| (is_array($val) && !in_array($args[$key], $val))
					|| ($val != '*' && $args[$key] != $val))
				{
					$matched = false;
					break;
				}
			}
			if ($matched)
			{
				$url = $rule['trans'];
				break;
			}
		}
	}
	// Some special substitutions
	$mainurl = parse_url($cfg['mainurl']);
	$spec['_area'] = $name;
	$spec['_host'] = $mainurl['host'];
	$spec['_rhost'] = $_SERVER['HTTP_HOST'];
	$spec['_path'] = SED_SITE_URI;
	// Transform the data into URL
	if (preg_match_all('#\{(.+?)\}#', $url, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			if ($p = mb_strpos($m[1], '('))
			{
				// Callback
				$func = mb_substr($m[1], 0, $p);
				$url = str_replace($m[0], $func($args, $spec), $url);
			}
			elseif (mb_strpos($m[1], '!$') === 0)
			{
				// Unset
				$var = mb_substr($m[1], 2);
				$url = str_replace($m[0], '', $url);
				unset($args[$var]);
			}
			else
			{
				// Substitute
				$var = mb_substr($m[1], 1);
				if (isset($spec[$var]))
				{
					$url = str_replace($m[0], urlencode($spec[$var]), $url);
				}
				elseif (isset($args[$var]))
				{
					$url = str_replace($m[0], urlencode($args[$var]), $url);
					unset($args[$var]);
				}
				else
				{
					$url = str_replace($m[0], urlencode($GLOBALS[$var]), $url);
				}
			}
		}
	}
	// Append query string if needed
	if (!empty($args))
	{
		$qs = '?';
		$sep = $header ? '&' : '&amp;';
		$sep_len = strlen($sep);
		foreach($args as $key => $val)
		{
			// Exclude static parameters that are not used in format,
			// they should be passed by rewrite rule (htaccess)
			if ($rule['params'][$key] != $val)
			{
				$qs .= $key .'='.urlencode($val).$sep;
			}
		}
		$qs = substr($qs, 0, -$sep_len);
		$url .= $qs;
	}
	// Almost done
	$url .= $tail;
	$url = str_replace('&amp;amp;', '&amp;', $url);
	return $url;
}

/**
 * Encodes a string for use in URLs
 *
 * @param string $str Source string
 * @param bool $translit Transliterate non-English characters
 * @return string
 */

function sed_urlencode($str, $translit = false)
{
	global $lang, $sed_translit;
	if ($translit && $lang != 'en' && is_array($sed_translit))
	{
		// Apply transliteration
		$str = strtr($str, $sed_translit);
	}
	return urlencode($str);
}

/**
 * Decodes a string that has been previously encoded with sed_urlencode()
 *
 * @param string $str Encoded string
 * @param bool $translit Transliteration of non-English characters was used
 * @return string
 */
function sed_urldecode($str, $translit = false)
{
	global $lang, $sed_translitb;
	if ($translit && $lang != 'en' && is_array($sed_translitb))
	{
		// Apply transliteration
		$str = strtr($str, $sed_translitb);
	}
	return urldecode($str);
}

/**
 * Store URI-redir to session
 *
 * @global $sys
 */
function sed_uriredir_store()
{
	global $sys;

	$script = basename($_SERVER['SCRIPT_NAME']);

	if ($_SERVER['REQUEST_METHOD'] != 'POST' // not form action/POST
		&& empty($_GET['x']) // not xg, hence not form action/GET and not command from GET
		&& !empty($script)
		&& $script != 'message.php' // not message location
		&& ($script != 'users.php' // not login/logout location
			|| empty($_GET['m'])
			|| !in_array($_GET['m'], array('auth', 'logout', 'register'))
			)
		)
	{
		$_SESSION['s_uri_redir'] = $sys['uri_redir'];
	}
}

/**
 * Apply URI-redir that stored in session
 *
 * @param bool $cfg_redir Configuration of redirect back
 * @global $redirect
 */
function sed_uriredir_apply($cfg_redir = true)
{
	global $redirect;

	if ($cfg_redir && empty($redirect) && !empty($_SESSION['s_uri_redir']))
	{
		$redirect = $_SESSION['s_uri_redir'];
	}
}

/**
 * Checks URI-redir for xg before redirect
 *
 * @param string $uri Target URI
 */
function sed_uriredir_redirect($uri)
{
	if (mb_strpos($uri, '&x=') !== false || mb_strpos($uri, '?x=') !== false)
	{
		$uri = sed_url('index'); // xg, not redirect to form action/GET or to command from GET
	}
	sed_redirect($uri);
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
	global $sys;
	return ('x='.$sys['xk']);
}

/**
 * Returns XSS protection field for POST forms
 *
 * @return string
 */
function sed_xp()
{
	global $sys;
	return '<div style="display:inline;margin:0;padding:0"><input type="hidden" name="x" value="'.$sys['xk'].'" /></div>';
}

/**
 * Set cookie with optional HttpOnly flag
 * @param string $name The name of the cookie
 * @param string $value The value of the cookie
 * @param int $expire The time the cookie expires in unixtime
 * @param string $path The path on the server in which the cookie will be available on.
 * @param string $domain The domain that the cookie is available.
 * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection. When set to TRUE, the cookie will only be set if a secure connection exists.
 * @param bool $httponly HttpOnly flag
 * @return bool
 */
function sed_setcookie($name, $value, $expire, $path, $domain, $secure = false, $httponly = false)
{
	if (strpos($domain, '.') === FALSE)
	{
		// Some browsers don't support cookies for local domains
		$domain = '';
	}

	if ($domain != '')
	{
		// Make sure www. is stripped and leading dot is added for subdomain support on some browsers
		if (strtolower(substr($domain, 0, 4)) == 'www.')
		{
			$domain = substr($domain, 4);
		}
		if ($domain[0] != '.')
		{
			$domain = '.'.$domain;
		}
	}

	if (version_compare(PHP_VERSION, '5.2.0', '>='))
	{
		return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}

	if (!$httponly)
	{
		return setcookie($name, $value, $expire, $path, $domain, $secure);
	}

	if (trim($domain) != '')
	{
		$domain .= ($secure ? '; secure' : '').($httponly ? '; httponly' : '');
	}
	return setcookie($name, $value, $expire, $path, $domain);
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

/**
 * Makes correct plural forms of words
 *
 * @global string $lang Current language
 * @param int $digit Numeric value
 * @param string $expr Word or expression
 * @param bool $onlyword Return only words, without numbers
 * @param bool $canfrac - Numeric value can be Decimal Fraction
 * @return string
 */
function sed_declension($digit, $expr, $onlyword = false, $canfrac = false)
{
	global $lang;

	if (!is_array($expr))
	{
		return trim(($onlyword ? '' : "$digit ").$expr);
	}

	if ($canfrac)
	{
		$is_frac = floor($digit) != $digit;
		$i = $digit;
	}
	else
	{
		$is_frac = false;
		$i = preg_replace('#\D+#', '', $digit);
	}

	$plural = sed_get_plural($i, $lang, $is_frac);
	$cnt = count($expr);
	return trim(($onlyword ? '' : "$digit ").(($cnt > 0 && $plural < $cnt) ? $expr[$plural] : ''));
}

/**
 * Used in sed_declension to get rules for concrete languages
 *
 * @param int $plural Numeric value
 * @param string $lang Target language code
 * @param bool $is_frac true if numeric value is fraction, otherwise false
 * @return int
 */
function sed_get_plural($plural, $lang, $is_frac = false)
{
	switch ($lang)
	{
		case 'en':
		case 'de':
		case 'nl':
		case 'se':
		case 'us':
			return ($plural == 1) ? 1 : 0;

		case 'fr':
			return ($plural > 1) ? 0 : 1;

		case 'ru':
		case 'ua':
			if ($is_frac)
			{
				return 1;
			}
			$plural %= 100;
			return (5 <= $plural && $plural <= 20) ? 2 : ((1 == ($plural %= 10)) ? 0 : ((2 <= $plural && $plural <= 4) ? 1 : 2));

		default:
			return 0;
	}
}

if ($cfg['customfuncs'])
{
	require_once($cfg['system_dir'].'/functions.custom.php');
}

?>