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
if (!function_exists('ckeditorMaxTimestamp')) {
    function ckeditorMaxTimestamp($dir) {
        $maxtime = 0;
        $dp = opendir($dir);
        while ($f = readdir($dp)) {
            if ($f[0] != '.') {
                $fname = $dir . '/' . $f;
                if (is_dir($fname)) {
                    $mtime = ckeditorMaxTimestamp($fname);
                } else {
                    $mtime = filemtime($fname);
                }
                if ($mtime > $maxtime) {
                    $maxtime = $mtime;
                }
            }
        }
        closedir($dp);
        return $maxtime;
    }
}

global $ckeditorTimestamp;

if (!$ckeditorTimestamp) {
    $ckeditorTimestamps = [
        ckeditorMaxTimestamp(Cot::$cfg['plugins_dir'] . '/ckeditor/lib'),
        ckeditorMaxTimestamp(Cot::$cfg['plugins_dir'] . '/ckeditor/js'),
    ];
    $ckeditorTimestamp = max($ckeditorTimestamps);
}
unset($ckeditorTimestamps);

// Translations
$translationFile = Cot::$cfg['plugins_dir'] . '/ckeditor/lib/translations/' . Cot::$usr['lang'] . '.umd.js';
if (file_exists($translationFile)) {
    Resources::linkFile($translationFile . '?' . $ckeditorTimestamp, 'js');
}

// Main CKEditor files
Resources::linkFile(Cot::$cfg['plugins_dir'] . '/ckeditor/lib/ckeditor5.css?' . $ckeditorTimestamp, 'css');
Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . '/ckeditor/lib/ckeditor5.umd.js?' . $ckeditorTimestamp, 'js');

Resources::linkFileFooter(
    Cot::$cfg['plugins_dir'] . '/ckeditor/js/functions.js?' . $ckeditorTimestamp,
    'js'
);

// Load preset
if (Cot::$usr['id'] > 0) {
    $presetName = 'group_' . Cot::$usr['maingrp'];
    if (!file_exists(Cot::$cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.$presetName.set.js")) {
        $presetName = 'default';
    }
} else {
    $presetName = file_exists(Cot::$cfg['plugins_dir'] . '/ckeditor/presets/ckeditor.group_' . COT_GROUP_GUESTS . '.set.js')
        ? 'group_' . COT_GROUP_GUESTS
        : 'default';
}

Resources::linkFileFooter(
    Cot::$cfg['plugins_dir'] . "/ckeditor/js/presets/ckeditor.$presetName.set.js?" . $ckeditorTimestamp,
    'js'
);

Resources::linkFileFooter(
    Cot::$cfg['plugins_dir'] . '/ckeditor/js/editor.js?' . $ckeditorTimestamp,
    'js'
);

if (empty($ckeditorCssToLoad)) {
    $ckeditorCssToLoad = [];
}

if (!empty($ckeditorCssToLoad) && is_array($ckeditorCssToLoad)) {
	foreach ($ckeditorCssToLoad as $key => $cssFile) {
		if (file_exists($cssFile)) {
            Resources::linkFile($cssFile . '?' . $ckeditorTimestamp, 'css');
        } else {
            unset($ckeditorCssToLoad[$key]);
        }
	}
}