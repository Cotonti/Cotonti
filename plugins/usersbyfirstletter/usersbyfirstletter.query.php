<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=users.query
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('usersbyfirstletter');

if(mb_strlen($f) == 1)
{
	if($f == "_")
	{
		$title .= "{$cfg['separator']} {$L['by_first_letter']}: %";
		$where['firstletter'] = 'user_name NOT REGEXP("^[a-zA-Z]")';
	}
	else
	{
		$f = strtoupper($f);
		$title .= "{$cfg['separator']} {$L['by_first_letter']}: $f";
		$where['firstletter'] = "UPPER(user_name) LIKE '$f%'";
	}
}

?>