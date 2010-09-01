<?php
/**
 * Text parsing API
 * Note: user-defined parsers have been moved to parser.custom.php
 *
 * @package Cotonti
 * @version 0.7.0
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

if (file_exists($cfg['system_dir'] . '/parser.custom.php'))
{
	sed_require_api('parser.custom');
}

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
	return sed_sql_update($db_bbcode, $bbc, "bbc_id = $id", 'bbc_') == 1;
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
 * Load smilies from current pack
 */
function sed_load_smilies()
{
	global $sed_smilies;

	function sed_smcp($sm1, $sm2)
	{
		if ($sm1['prio'] == $sm2['prio']) return 0;
		else return $sm1['prio'] > $sm2['prio'] ? 1 : -1;
	}

	
	$sed_smilies = array();

	if (file_exists('./images/smilies/set.js')
		&& preg_match('#var\s*smileSet\s*=\s*(\[.*?\n\]);#s', file_get_contents('./images/smilies/set.js'), $mt))
	{
		$js = str_replace(array("\r", "\n"), '', $mt[1]);
		$js = preg_replace('#(smileL\.\w+)#', '"$1"', $js);
		$sed_smilies = json_decode($js, true);
		usort($sed_smilies, 'sed_smcp');
	}
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

if (!$cfg['parser_disable'])
{
	if (!is_array($sed_smilies))
	{
		sed_load_smilies();
		$cot_cache && $cot_cache->db->store('sed_smilies', $sed_smilies, 'system');
	}
	if (!is_array($sed_bbcodes))
	{
		sed_bbcode_load();
		if ($cot_cache)
		{
			$cot_cache->db->store('sed_bbcodes', $sed_bbcodes, 'system');
			$cot_cache->db->store('sed_bbcodes_post', $sed_bbcodes_post, 'system');
			$cot_cache->db->store('sed_bbcode_containers', $sed_bbcode_containers, 'system');
		}
	}
}

?>