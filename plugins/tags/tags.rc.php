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

if (cot_plugin_active('autocomplete')) {
    if (cot::$cfg['jquery'] && cot::$cfg['turnajax'] && cot::$cfg['plugin']['autocomplete']['autocomplete'] > 0) {
        Resources::addEmbed(
            '$(document).ready(function() { 
$(".autotags").autocomplete("' . cot_url('plug','r=tags') . '", {multiple: true, minChars: ' .
            cot::$cfg['plugin']['autocomplete']['autocomplete'] . '});
});', 'js',50,'global','tags.autocomplete');
    }
}
if(cot::$cfg['plugin']['tags']['css']) {
    Resources::addFile(cot::$cfg['plugins_dir'] . '/tags/tpl/tags.css', 'css');
}
