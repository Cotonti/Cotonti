<?php

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
		return $text.' <a href="http://www.icq.com/'.$text.'#pager"><img src="http://web.icq.com/whitepages/online?icq='.$text.'&amp;img=5" alt="" /></a>';
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

?>
