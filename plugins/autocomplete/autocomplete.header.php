<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_HEAD}
[END_COT_EXT]
==================== */

/**
 * Header file for Autocomplete plugin
 *
 * @package autocomplete
 * @version 0.8.0
 * @author esclkm
 * @copyright Copyright (c) Cotonti Team 2010-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');


if ($env['ext'] == 'pm' || ($env['z'] == 'plug' && $e == 'search'))
{
	cot_rc_link_file($cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.js');
	cot_rc_link_file($cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.css');
	cot_rc_embed('
		$(document).ready(function(){
		$(".userinput").autocomplete("plug.php?r=autocomplete", {multiple: true, minChars: 3});
		});
	');
}


?>