<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=users.filters
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('usersbyfirstletter');

for ($i = 0; $i <= 26; $i++)
{
	$char = ($i == 0) ? '_' : chr($i + 64);
	$letter = ($char == '_') ? '%' : $char;
	$t->assign(array(
		'LETTER' => $letter,
		'URL' => cot_url('users', "f=$char")
	));
	$t->parse('MAIN.BYFIRSTLETTER.LETTER');
}
$t->parse('MAIN.BYFIRSTLETTER');

?>