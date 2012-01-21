<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Header file for Autocomplete plugin
 *
 * @package autocomplete
 * @version 0.8.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');


if ($cfg['jquery'] && $cfg['turnajax'] && $cfg['plugin']['autocomplete']['autocomplete'] > 0)
{
	cot_rc_add_file($cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.min.js');
	if($cfg['plugin']['autocomplete']['css'])
	{
		cot_rc_add_file($cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.css');
	}
	
	cot_rc_add_embed('autocomplete', '
		$(document).ready(function(){
		 $(".userinput").live("focus", function() {
		$(".userinput").autocomplete("plug.php?r=autocomplete", {multiple: true, minChars: '.$cfg['plugin']['autocomplete']['autocomplete'].'});
		});
		});
	');
}


?>