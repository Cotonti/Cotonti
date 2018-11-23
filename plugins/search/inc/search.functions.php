<?php
/**
 * Search functions
 *
 * @package Search
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') || die('Wrong URL.');

/**
 * Marks defined words within text
 * @param string $text
 * @param array $words Word list
 * @return string Marked text
 */
function cot_clear_mark($text, $words)
{
	global $cfg;
	$text = trim($text);
	if (!empty($text))
	{
		$text = preg_replace("'\r?\n'", " ", $text);
		$text = preg_replace("'\s+'", " ", $text);
		if (cot_plugin_active('bbcode'))
		{// BB
			$text = preg_replace("'\[img.*?/img\]'si", "", $text);
			$text = preg_replace("'\[thumb.*?/thumb\]'si", "", $text);
			$text = preg_replace("'[[^]]*?.*?]'si", "", $text);
		}
		// HTML
		$text = strip_tags($text);
		$text = htmlspecialchars($text, ENT_COMPAT | ENT_HTML401, 'UTF-8', false);

		foreach ($words as $i => $w)
		{
			$p = mb_stripos($text, $w);
			if ($p > 0)
			{
				$p_arr[] = $p;
			}
		}
		if (is_array($p_arr) && count($p_arr))
		{
			sort($p_arr);
			$text_result = '';
			$last_pos = -1;
			$delta = 255 / count($p_arr);
			$text_len = mb_strlen($text);
			foreach ($p_arr as $pos_mid)
			{
				$pos_beg = $pos_mid - $delta;
				if ($pos_beg <= 0)
				{
					$pos_beg = 0;
				}
				else
				{
					while($pos_beg > 0 && mb_substr($text, $pos_beg, 1) != " ")
					{
						$pos_beg--;
					}
				}

				$pos_end = $pos_mid + $delta;
				if ($pos_end > $text_len)
				{
					$pos_end = $text_len;
				}
				else
				{
					while ($pos_end < $text_len && mb_substr($text, $pos_end, 1) != " ")
					{
						$pos_end++;
					}
				}
				if ($pos_beg <= $last_pos)
				{
					$arOtr[count($arOtr)-1][1] = $pos_end;
				}
				else
				{
					$arOtr[] = array($pos_beg, $pos_end);
				}
				$last_pos = $pos_end;
			}

			if (count($arOtr))
			{
				for ($i = 0; $i < count($arOtr); $i++)
				{
					$text_result .= ($arOtr[$i][0] <= 0) ? '' : ' ...';
					$text_result .= mb_substr($text, $arOtr[$i][0], $arOtr[$i][1] - $arOtr[$i][0]);
					$text_result .= ($arOtr[$i][1] >= $text_len) ? '' : ' ... ';
				}
			}
		}

		if (mb_strlen($text_result) < 10)
		{
			$len_cut = 255;
			$len_txt = mb_strlen($text);
			$len_cut = ($len_txt < $len_cut) ? $len_txt : $len_cut;
			$text_result = mb_substr($text, 0, $len_cut);
			$text_result = ($len_cut < $len_txt) ? $text_result.'... ' : $text_result;
		}
		$search_tag = array();
		foreach ($words as $k => $i)
		{
			$search_tag[] = preg_quote($i);
		}
		$text_result = preg_replace('`('.implode('|', $search_tag).')`i', '<span class="search_hl">$1</span>', $text_result);
		return ($text_result);
	}
	return ("");
}
