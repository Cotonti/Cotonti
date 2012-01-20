<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Head resources
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

//cot_headrc_load_file($cfg['plugins_dir'] . '/tags/style.css', 'global', 'css');
if ($cfg['jquery'] && $cfg['turnajax'] && $cfg['plugin']['autocomplete']['autocomplete'] > 0)
{
	cot_rc_add_embed('tags.autocomplete', '$(document).ready(function(){
$(".autotags").autocomplete("'.cot_url('plug', 'r=tags').'", {multiple: true, minChars: '.$cfg['plugin']['autocomplete']['autocomplete'].'});
});');
}
if($cfg['plugin']['tags']['css'])
{
	cot_rc_add_file($cfg['plugins_dir'] . '/tags/tpl/tags.css');
}
?>
