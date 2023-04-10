<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=editor
[END_COT_EXT]
==================== */

/**
 * CKEditor connector for Cotonti.
 * Uses direct header output rather than consolidated cache
 * because CKEditor uses dynamic AJAX component loading and
 * does not support consolidation.
 *
 * @package CKEditor
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Calculate editor timestamp
if (!function_exists('ckeditor_max_timestamp'))
{
    function ckeditor_max_timestamp($dir)
    {
        $maxtime = 0;
        $dp = opendir($dir);
        while ($f = readdir($dp))
        {
            if ($f[0] != '.')
            {
                $fname = $dir . '/' . $f;
                if (is_dir($fname))
                    $mtime = ckeditor_max_timestamp($fname);
                else
                    $mtime = filemtime($fname);

                if ($mtime > $maxtime)
                    $maxtime = $mtime;
            }
        }
        closedir($dp);
        return $maxtime;
    }
}

global $ckeditor_timestamp;

if (!$ckeditor_timestamp) {
    $ckeditor_timestamp = ckeditor_max_timestamp(Cot::$cfg['plugins_dir'] . '/ckeditor/lib');
}

// Main CKEditor file
Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . '/ckeditor/lib/ckeditor.js?'.$ckeditor_timestamp, 'js');

// Load preset and connector
if (Cot::$usr['id'] > 0) {
    $preset_name = 'group_' . Cot::$usr['maingrp'];
    if (!file_exists(Cot::$cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.$preset_name.set.js")) {
        $preset_name = 'default';
    }
} else {
    $preset_name = file_exists(Cot::$cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.group_1.set.js") ? 'group_1'
        : 'default';
}
Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.$preset_name.set.js?" .
    $ckeditor_timestamp);

// Default ckeditor content styles
Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . "/ckeditor/lib/styles.js?".$ckeditor_timestamp);

$ckeditor_css_connector = '';

if (!empty($ckeditor_css_to_load) && is_array($ckeditor_css_to_load)) {
	foreach ($ckeditor_css_to_load as $key => $css_file) {
		if (!file_exists($css_file)) unset($ckeditor_css_to_load[$key]);
	}
} else {
    $ckeditor_css_to_load = [
        Cot::$cfg['plugins_dir'].'/ckeditor/presets/contents.default.css?'.$ckeditor_timestamp
    ];
}
if (sizeof($ckeditor_css_to_load)) {
    $ckeditor_css_connector = "CKEDITOR.config.contentsCss = ['".implode("','", $ckeditor_css_to_load)."'];";
}

if ($ckeditor_css_connector !== '') {
    Resources::embedFooter(
        "CKEDITOR.timestamp = $ckeditor_timestamp; CKEDITOR.config.baseHref='" . Cot:: $cfg['mainurl']. "/'; " .
        $ckeditor_css_connector
    );
}