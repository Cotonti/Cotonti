<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Head resources
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

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
