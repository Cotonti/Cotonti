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

use cot\extensions\ExtensionsService;

defined('COT_CODE') or die('Wrong URL');

if (ExtensionsService::getInstance()->isPluginActive('autocomplete')) {
    if (Cot::$cfg['jquery'] && Cot::$cfg['turnajax'] && Cot::$cfg['plugin']['autocomplete']['autocomplete'] > 0) {
        Resources::addEmbed(
            '$(document).ready(function() { '
            . ' if ($.fn.autocomplete !== undefined) { '
                . '$(".autotags").autocomplete("'
                . cot_url('plug','r=tags') . '", '
                . '{multiple: true, minChars: ' . Cot::$cfg['plugin']['autocomplete']['autocomplete'] . '}) '
            . '} '
            .'});',
            'js',
            50,
            'global',
            'tags.autocomplete'
        );
    }
}
if (Cot::$cfg['plugin']['tags']['css']) {
    Resources::addFile(Cot::$cfg['plugins_dir'] . '/tags/tpl/tags.css', 'css');
}
