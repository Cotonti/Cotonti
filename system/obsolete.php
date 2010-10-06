<?php
/**
 * Deprecated and obsolete functions library for backwards compatibility
 *
 * @package Cotonti
 * @version 0.7.0
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

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
	return cot_rc_link(cot_url('pm', 'm=send&to='.$user), $R['pm_icon'], array('title' => $L['pm_sendnew']));
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
	trigger_error('cot_cc() is deprecated since Cotonti Genoa, use htmlspecialchars() instead');
	return htmlspecialchars($text);
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
	$text = $max_chars == 0 ? $text : cot_cutstring(strip_tags($text), $max_chars);
	// Fix partial cuttoff
	$text = preg_replace('#\[[^\]]*?$#', '...', $text);
	// Parse the BB-codes or skip them
	if ($parse_bbcodes)
	{
		// Parse it
		$text = cot_parse($text);
	}
	else $text = preg_replace('#\[[^\]]+?\]#', '', $text);
	return $text;
}

// FIXME this function is obsolete, or meta/title generation must be reworked
function sed_htmlmetas()
{
		global $cfg;
		$contenttype = ($cfg['doctypeid']>2 && $cfg['xmlclient']) ? "application/xhtml+xml" : "text/html";
		$result = "<meta http-equiv=\"content-type\" content=\"".$contenttype."; charset=UTF-8\" />
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
	$name = 'a'.cot_unique(8);
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
	return $m[1] . sed_obfuscate('<a href="mailto:'.$m[2].'">'.$m[2].'</a>');
}

/**
 * Renders page navigation bar
 *
 * @deprecated Siena 0.7.0 - 23.01.2010, use cot_pagenav() instead
 * @see cot_pagenav
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
	if (function_exists('cot_pagination_custom'))
	{
		// For custom pagination functions in plugins
		return cot_pagination_custom($url, $current, $entries, $perpage, $characters, $onclick, $object);
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
 * @deprecated Siena 0.7.0 - 23.01.2010, use cot_pagenav() instead
 * @see cot_pagenav()
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
	if (function_exists('cot_pagination_pn_custom'))
	{
		// For custom pagination functions in plugins
		return cot_pagination_pn_custom($url, $current, $entries, $perpage, $res_array, $characters, $onclick, $object);
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
 * Returns XSS protection code
 *
 * @deprecated This function is not needed anymore, use global $sys['xk'] value instead
 * @return string
 */
function sed_sourcekey()
{
	global $sys;
	return $sys['xk'];
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
?>
