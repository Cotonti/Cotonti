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


if (Cot::$cfg['jquery'] && Cot::$cfg['turnajax'] && Cot::$cfg['plugin']['autocomplete']['autocomplete'] > 0) {
    Resources::addFile(Cot::$cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.min.js');

	if(Cot::$cfg['plugin']['autocomplete']['css']) {
        Resources::addFile(Cot::$cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.css');
	}

    Resources::addEmbed(
        '$(document).ready(function(){
		    $( document ).on( "focus", ".userinput", function() {
		        $(".userinput").autocomplete("index.php?r=autocomplete", {multiple: true, minChars: ' .
                    Cot::$cfg['plugin']['autocomplete']['autocomplete'] . '});
		    });
		});',
        'js',
        50,
        'global',
        'autocomplete'
    );
}
