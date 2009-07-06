<?PHP
/**
 * Search functions
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Boss
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') || die('Wrong URL.');

function hw_clear_mark($text, $type=0, $words)
{
	$text = trim($text);

	if(strlen($text))
	{
	// BB
		if($type == 0 || $type == '')
		{
		//
			$text = preg_replace("'\[img.*?/img\]'si", "", $text);
			$text = preg_replace("'\[thumb.*?/thumb\]'si", "", $text);
			$text = preg_replace("'[[^]]*?.*?]'si", "", $text);
		}

		// HTML
		elseif($type == 1)
		{
		// HTML.
			$text = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $text);
		}
		else
		{ return(""); }

		// .
		$text = preg_replace("'.\n'", " ", $text);
		$text = preg_replace("'.\t'", " ", $text);
		$text = preg_replace("' +'", " ", $text);
		$text = trim($text);
		$text = htmlspecialchars($text);

		// .
		foreach($words as $i => $w)
		{
		// .
			$p = stripos($text, $w);

			// .
			if($p > 0)
			{ $p_arr[] = $p; }
		}

		// .
		if(count($p_arr))
		{
			sort($p_arr);

			$text_result = "";
			$last_pos = -1;
			$delta = 255/count($p_arr);
			$text_len = mb_strlen($text);

			// .
			foreach($p_arr as $pos_mid)
			{
			// .
				$pos_beg = $pos_mid - $delta;
				if($pos_beg <= 0)
				{
					$pos_beg = 0;
				}
				else
				{
				// .
					while($pos_beg > 0 && substr($text, $pos_beg, 1) != " ")
					{ $pos_beg--; }
				}

				// .
				$pos_end = $pos_mid + $delta;
				if($pos_end > $text_len)
				{
					$pos_end = $text_len;
				}
				else
				{
				// .
					while($pos_end < $text_len && substr($text, $pos_end, 1) != " ")
					{ $pos_end++; }
				}

				// .
				if($pos_beg <= $last_pos)
				{
				// .
					$arOtr[count($arOtr)-1][1] = $pos_end;
				}
				else
				{
				// .
					$arOtr[] = Array($pos_beg, $pos_end);
				}

				// .
				$last_pos = $pos_end;
			}

			if(count($arOtr))
			{
				for($i=0; $i<count($arOtr); $i++)
				{
					$text_result .= $arOtr[$i][0] <= 0 ? "" : " ...";
					$text_result .= mb_substr($text, $arOtr[$i][0], $arOtr[$i][1]-$arOtr[$i][0]);
					$text_result .= $arOtr[$i][1] >= $text_len ? "" : "... ";
				}
			}
		}

		//
		if(strlen($text_result) < 10)
		{
			$len_cut = 255; // .
			$len_txt = mb_strlen($text); // .
			$len_cut = $len_txt < $len_cut ? $len_txt : $len_cut;
			$text_result = mb_substr($text, 0, $len_cut); // .
			$text_result = $len_cut < $len_txt ? $text_result."... " : $text_result;
		}

		// .
		foreach($words as $k => $i)
		{
			$text_result = str_ireplace($i, "<b>".$i."</b>", $text_result);
		}

		return($text_result);
	}

	return("");
}

?>