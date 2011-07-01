<?php
/**
 * BBcode parsing and management API
 *
 * @package bbcode
 * @version 0.9.0
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

global $cfg, $db_x, $db_bbcode;
$db_bbcode = isset($db_bbcode) ? $db_bbcode : $db_x . 'bbcode';

cot_bbcode_load();
if ($cfg['plugin']['bbcode']['smilies'])
{
	cot_smilies_load();
}

/**
 * Registers a new bbcode in database.
 * In 'callback' mode $replacement is normal PHP function body (without declaration) which
 * takes $input array of matches as parameter and must return a replacement string. These
 * variables are also imported as globals in callback function: $cfg, $sys, $usr, $L, $theme, $cot_groups
 *
 * @global $db, $db_bbcode;
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
function cot_bbcode_add($name, $mode, $pattern, $replacement, $container = true, $priority = 128, $plug = '', $postrender = false)
{
	global $db, $db_bbcode;
	$bbc['bbc_name'] = $name;
	$bbc['bbc_mode'] = $mode;
	$bbc['bbc_pattern'] = $pattern;
	$bbc['bbc_replacement'] = $replacement;
	$bbc['bbc_container'] = empty($container) ? 0 : 1;
	if ($priority >= 0 && $priority < 256)
	{
		$bbc['bbc_priority'] = (int) $priority;
	}
	if (!empty($plug))
	{
		$bbc['bbc_plug'] = $plug;
	}
	$bbc['bbc_postrender'] = empty($postrender) ? 0 : 1;
	return $db->insert($db_bbcode, $bbc) == 1;
}

/**
 * Removes a bbcode from parser database.
 *
 * @global $db_bbcode
 * @param int $id BBCode ID or 0 to remove all (use carefully)
 * @param string $plug Remove all bbcodes that belong to this plug
 * @return bool
 */
function cot_bbcode_remove($id = 0, $plug = '')
{
	global $db, $db_bbcode;
	if ($id > 0)
	{
		return $db->delete($db_bbcode, "bbc_id = $id") == 1;
	}
	elseif (!empty($plug))
	{
		return $db->delete($db_bbcode, "bbc_plug = '".$db->prep($plug)."'");
	}
	else
	{
		return $db->delete($db_bbcode) > 0;
	}
}

/**
 * Updates bbcode data in parser database.
 *
 * @global $db, $db_bbcode;
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
function cot_bbcode_update($id, $enabled, $name, $mode, $pattern, $replacement, $container, $priority = 128, $postrender = false)
{
	global $db, $db_bbcode;
	$bbc['enabled'] = empty($enabled) ? 0 : 1;
	if (!empty($name))
	{
		$bbc['bbc_name'] = $name;
	}
	if (!empty($mode))
	{
		$bbc['bbc_mode'] = $mode;
	}
	if (!empty($pattern))
	{
		$bbc['bbc_pattern'] = $pattern;
	}
	if (!empty($replacement))
	{
		$bbc['bbc_replacement'] = $replacement;
	}
	if ($priority >= 0 && $priority < 256)
	{
		$bbc['bbc_priority'] = $priority;
	}
	$bbc['bbc_container'] = empty($container) ? 0 : 1;
	$bbc['bbc_postrender'] = empty($postrender) ? 0 : 1;
	return $db->update($db_bbcode, $bbc, 'bbc_id = ?', array($id)) == 1;
}

/**
 * Loads bbcodes from database if they havent been already loaded.
 *
 * @global $cot_bbcodes
 * @global $db_bbcode
 */
function cot_bbcode_load()
{
	global $db, $db_bbcode, $cot_bbcodes, $cot_bbcode_containers;
	if (is_array($cot_bbcodes))
	{
		// Loaded from cache, exit
		return;
	}
	$cot_bbcodes = array();
	$cot_bbcode_containers = ''; // required for auto-close
	$bbc_cntr = array();
	$i = 0;
	$j = 0;
	$res = $db->query("SELECT * FROM $db_bbcode WHERE bbc_enabled = 1 ORDER BY bbc_priority");
	while ($row = $res->fetch())
	{
		if ($row['bbc_postrender'] == 1)
		{
			foreach ($row as $key => $val)
			{
				$cot_bbcodes_post[$j][str_replace('bbc_', '', $key)] = $val;
			}
			$j++;
		}
		else
		{
			foreach ($row as $key => $val)
			{
				$cot_bbcodes[$i][str_replace('bbc_', '', $key)] = $val;
			}
			$i++;
		}
		if ($row['bbc_container'] == 1 && !isset($bbc_cntr[$row['bbc_name']]))
		{
			$cot_bbcode_containers .= $row['bbc_name'].'|';
			$bbc_cntr[$row['bbc_name']] = 1;
		}
	}
	$res->closeCursor();
	if (!empty($cot_bbcode_containers))
	{
		$cot_bbcode_containers = mb_substr($cot_bbcode_containers, 0, -1);
	}
	if ($cache)
	{
		$cache->db->store('cot_bbcodes', $cot_bbcodes, 'system');
		$cache->db->store('cot_bbcode_containers', $cot_bbcode_containers, 'sysem');
	}
}

/**
 * Clears bbcode cache
 */
function cot_bbcode_clearcache()
{
	global $cache;
	$cache->db->remove('cot_bbcodes', 'system');
	$cache->db->remove('cot_bbcode_containers', 'system');
}

/**
 * Parses bbcodes in text.
 *
 * @global $cot_bbcodes
 * @param string $text Text body
 * @return string
 */
function cot_parse_bbcode($text)
{
	global $cfg, $cot_bbcodes, $cot_bbcode_containersm, $sys, $cot_smilies, $L, $usr;

	$code = array();
	$unique_seed = $sys['unique'];
	$ii = 10000;

	$text = htmlspecialchars($text);
	$text = cot_parse_autourls($text);

	$parse_smilies = $cfg['plugin']['bbcode']['smilies'];

	if ($parse_smilies && is_array($cot_smilies))
	{
		foreach($cot_smilies as $k => $v)
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

	// BB auto-close
	$bbc = array();
	if (preg_match_all('#\[(/)?('.$cot_bbcode_containers.')(=[^\]]*)?\]#i', $text, $mt, PREG_SET_ORDER))
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
	$cnt = count($cot_bbcodes);
	for ($i = 0; $i < $cnt; $i++)
	{
		$bbcode = $cot_bbcodes[$i];
		switch($bbcode['mode'])
		{
			case 'str':
				$text = str_ireplace($bbcode['pattern'], $bbcode['replacement'], $text);
			break;

			case 'pcre':
				$text = preg_replace('`'.$bbcode['pattern'].'`mis', $bbcode['replacement'], $text);
			break;

			case 'callback':
				$phpcode = 'global $cfg, $sys, $usr, $L, $theme, $cot_groups;'.$bbcode['replacement'];
				$text = preg_replace_callback('`'.$bbcode['pattern'].'`mis', create_function('$input', $phpcode), $text);
			break;
		}
	}

	$text = nl2br($text);
	$text = str_replace("\r", '', $text);
	// Strip extraneous breaks
	$text = preg_replace('#<(/?)(p|hr|ul|ol|li|blockquote|table|tr|td|th|div|h1|h2|h3|h4|h5)(.*?)>(\s*)<br />#', '<$1$2$3>', $text);
	$text = preg_replace_callback('#<pre[^>]*>(.+?)</pre>#sm', 'cot_bbcode_parse_pre', $text);

	foreach ($code as $x => $y)
	{
		$text = str_replace($x, $y, $text);
	}

	return $text;
}

/**
 * Supplimentary br stripper callback
 *
 * @param array $m PCRE entries
 * @return string
 */
function cot_bbcode_parse_pre($m)
{
	return str_replace('<br />', '', $m[0]);
}

/**
 * Neutralizes bbcodes in text
 *
 * @param string $text Source text
 * @return string
 */
function cot_bbcode_cdata($text)
{
	$res = $text;
	//$res = preg_replace('`&(?!amp;)`i', '&amp;$1', $res);
	$res = str_replace('[', '&#091;', $res);
	$res = str_replace(']', '&#093;', $res);
	return $res;
}

/**
 * Parses smiles in text
 *
 * @param string $res Source text
 * @return string
 */
function cot_smilies($res)
{
	global $cot_smilies;

	if (is_array($cot_smilies))
	{
		foreach($cot_smilies as $k => $v)
		{
			$res = str_replace($v['code'],
				sed_rc('img_smilie', array('src' => 'images/smilies/' . $v['file'], 'name' => $v['lang'])), $res);
		}
	}
	return $res;
}

/**
 * Load smilies from current pack
 */
function cot_smilies_load()
{
	global $cot_smilies, $lang, $cache;

	if (is_array($cot_smilies))
	{
		return;
	}

	function cot_smcp($sm1, $sm2)
	{
		if ($sm1['prio'] == $sm2['prio']) return 0;
		else return $sm1['prio'] > $sm2['prio'] ? 1 : -1;
	}


	$cot_smilies = array();

	if (file_exists('./images/smilies/set.js')
		&& preg_match('#var\s*smileSet\s*=\s*(\[.*?\n\]);#s', file_get_contents('./images/smilies/set.js'), $mt))
	{
		$js = str_replace(array("\r", "\n"), '', $mt[1]);
		$js = preg_replace('#(smileL\.\w+)#', '"$1"', $js);
		$cot_smilies = json_decode($js, true);
		usort($cot_smilies, 'cot_smcp');
		if (file_exists("./images/smilies/lang/$lang.lang.js"))
		{
			$sm_lang = "./images/smilies/lang/$lang.lang.js";
		}
		elseif (file_exists('./images/smilies/lang/en.lang.js'))
		{
			$sm_lang = './images/smilies/lang/en.lang.js';
		}
		else
		{
			$sm_lang = false;
		}
		if ($sm_lang && preg_match('#var\s*smileL\s*=\s*(\[.*?\n\]);#s', file_get_contents($sm_lang), $mt))
		{
			$js = str_replace(array("\r", "\n"), '', $mt[1]);
			$js = preg_replace('#(smileL\.\w+)#', '"$1"', $js);
			$smileL = json_decode($js, true);
			foreach ($cot_smilies as $key => $val)
			{
				if (empty($val['lang']))
				{
					$cot_smilies[$key]['lang'] = $val['name'];
				}
				elseif (preg_match('#^smileL\.(.+)$#', $val['lang'], $mt))
				{
					$cot_smilies[$key]['lang'] = $smileL[$mt[1]];
				}
			}
		}
	}
	$cache && $cache->db->store('cot_smilies', $cot_smilies, 'system');
}

?>
