<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Header file for Autocomplete plugin
 *
 * @package Autocomplete
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
		    $( document ).on( "focus", ".userinput", function() {
		        $(".userinput").autocomplete("index.php?r=autocomplete", {multiple: true, minChars: '.$cfg['plugin']['autocomplete']['autocomplete'].'});
		    });
		});
	');
}
