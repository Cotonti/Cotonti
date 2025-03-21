<?php
/**
 * Admin function library.
 *
 * @package API - Administration
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsHelper;
use cot\extensions\ExtensionsService;

defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('forms');
require_once cot_incfile('extensions');

/* ======== Defaulting the admin variables ========= */
$adminTitle = $adminMain = $adminHelp = $pluginBody = '';
$adminPath = [];

Cot::$usr['admin_config'] = cot_auth('admin', 'a', 'A');
Cot::$usr['admin_structure'] = cot_auth('structure', 'a', 'A');
Cot::$usr['admin_users'] = cot_auth('users', 'a', 'A')
    || in_array(COT_GROUP_SUPERADMINS, Cot::$usr['groups'], true);

/**
 * Returns $url as an HTML link if $cond is TRUE or just plain $text otherwise
 * @param string $url Link URL
 * @param string $text Link text
 * @param bool $cond Condition
 * @return string
 */
function cot_linkif($url, $text, $cond)
{
	if ($cond) {
		$res = '<a href="'.$url.'">'.$text.'</a>';
	} else {
		$res = $text;
	}

	return $res;
}

/**
 * Returns group selection dropdown code
 *
 * @param string 	$chosen Seleced value
 * @param string 	$name Dropdown name
 * @param array 	$skip Hidden groups
 * @param bool 		$add_empty Allow empty choice
 * @param mixed 	$attrs Additional attributes as an associative array or a string
 * @param string 	$custom_rc Custom resource string name
 * @return string
 */
function cot_selectbox_groups($chosen, $name, $skip = null, $add_empty = false, $attrs = '', $custom_rc = '')
{
	global $cot_groups;

	$opts = [];
	if (empty($skip)) $skip = [];
	if (!is_array($skip)) $skip = [$skip];
	foreach ($cot_groups as $k => $i) {
		if (!$i['skiprights'] && !in_array($k, $skip)) {
			$opts[$k] = $cot_groups[$k]['name'];
		}
	}

	return cot_selectbox($chosen, $name, array_keys($opts), array_values($opts), $add_empty, $attrs, $custom_rc);
}

/**
* Returns a list of time zone names used for setting default time zone
*/
function cot_config_timezones()
{
	global $L;
	$timezonelist = cot_timezone_list(true, false);
	foreach ($timezonelist as $timezone) {
		$names[] = $timezone['identifier'];
		$titles[] = $timezone['description'];
	}
	$L['cfg_defaulttimezone_params'] = $titles;
	return $names;
}

/**
 * Returns substring position in file
 *
 * @param string $file File path
 * @param string $str Needle
 * @param int $maxsize Search limit
 * @return int
 */
function cot_stringinfile($file, $str, $maxsize = 32768)
{
	if ($fp = @fopen($file, 'r')) {
		$data = fread($fp, $maxsize);
		$pos = mb_strpos($data, $str);
		$result = !($pos === FALSE);
	} else {
		$result = FALSE;
	}
	@fclose($fp);
	return $result;
}

/**
 * @param $code
 * @param $is_module
 * @return array{name: string, desc: string, notes: string, icon: string}
 */
function cot_get_extensionparams($code, $is_module = false)
{
	global $L, $Ls, $cfg, $R; // We are including lang files. So we need it global.
    global $cot_modules, $cot_plugins_enabled;

	$dir = $is_module ? Cot::$cfg['modules_dir'] : Cot::$cfg['plugins_dir'];

    $notes = '';

    $typeKey = $is_module ? 'module' : 'plug';

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        /** @deprecated For backward compatibility. Will be removed in future releases */
        $legacyIcon = '';
    }

    $icon = '';
	$key = 'icon_' . $typeKey . '_' . $code;
	if (!empty(Cot::$R[$key])) {
		$icon = Cot::$R[$key];
	} elseif (!empty(Cot::$R['admin_icon_extension_default'])) {
		$icon = Cot::$R['admin_icon_extension_default'];
	} else {
		$fileNames = [
			Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons'] . '/' . $typeKey . '_' . $code . '.png',
			$dir . '/' . $code . '/' . $code . '.png'
		];
		foreach ($fileNames as $fileName) {
			if (file_exists($fileName)) {
                $icon = cot_rc('img_none', ['src' => $fileName]);
                if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                    $legacyIcon = $fileName;
                }
			}
		}
    }

    if (empty($icon) && !empty($R['admin_icon_extension'])) {
        $icon = $R['admin_icon_extension'];
    }

    if (empty($icon)) {
        $fileNames = [
            Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons'] . '/default.png',
            Cot::$cfg['icons_dir'] . '/default/default.png',
        ];
        foreach ($fileNames as $fileName) {
            if (file_exists($fileName)) {
                $icon = cot_rc('img_none', ['src' => $fileName]);
                if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                    $legacyIcon = $fileName;
                }
            }
        }
    }

    $extensionService = ExtensionsService::getInstance();
    $extensionHelper = ExtensionsHelper::getInstance();

    $langFile = cot_langfile(
        $code, $is_module ? ExtensionsDictionary::TYPE_MODULE : ExtensionsDictionary::TYPE_PLUGIN
    );
    if (!empty($langFile) && file_exists($langFile)) {
        $L['info_notes'] = '';
        include $langFile;
        // We are including lang file, so we should use $L, not Cot::$L
        if (!empty($L['info_notes'])) {
            $notes = $L['info_notes'];
        }
    }

	$result = [
        'name' => $extensionHelper->getTitle(
            $code,
            $is_module ? ExtensionsDictionary::TYPE_MODULE : ExtensionsDictionary::TYPE_PLUGIN
        ),
		'desc' => $extensionHelper->getDescription(
            $code,
            $is_module ? ExtensionsDictionary::TYPE_MODULE : ExtensionsDictionary::TYPE_PLUGIN
        ),
        'notes' => $notes,
		'icon' => $icon,
	];

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        $result['legacyIcon'] = $legacyIcon;
    }

    return $result;
}

/**
 * Returns short info about Cotonti core
 *
 * @param string $tag_prefix Prefix for tags
 *
 * @return array
 */
function cot_generate_infotags($tag_prefix = '')
{
	global $db_plugins, $db_updates;

	$sql = Cot::$db->query("SHOW TABLES");
	foreach ($sql->fetchAll(PDO::FETCH_NUM) as $row) {
		$table_name = $row[0];
		$status = Cot::$db->query("SHOW TABLE STATUS LIKE '$table_name'");
		$status1 = $status->fetch();
		$status->closeCursor();
		$tables[] = $status1;
	}

	$total_length = 0;
	$total_rows = 0;
	$total_index_length = 0;
	$total_data_length = 0;
	foreach ($tables as $dat) {
		$table_length = $dat['Index_length'] + $dat['Data_length'];
		$total_length += $table_length;
		$total_rows += $dat['Rows'];
		$total_index_length += $dat['Index_length'];
		$total_data_length += $dat['Data_length'];
	}

	$totalplugins = Cot::$db->query("SELECT DISTINCT(pl_code) FROM $db_plugins WHERE 1 GROUP BY pl_code")->rowCount();
	$totalhooks = Cot::$db->query("SELECT COUNT(*) FROM $db_plugins")->fetchColumn();

	$temp_array = [
		'DB_TOTAL_ROWS' => $total_rows,
		'DB_INDEXSIZE' => number_format(($total_index_length / 1024), 1, '.', ' '),
		'DB_DATASSIZE' => number_format(($total_data_length / 1024), 1, '.', ' '),
		'DB_TOTALSIZE' => number_format(($total_length / 1024), 1, '.', ' '),
		'TOTALPLUGINS' => $totalplugins,
		'TOTALHOOKS' => $totalhooks,
		'VERSION' => Cot::$cfg['version'],
		'DB_VERSION' => htmlspecialchars(Cot::$db->query("SELECT upd_value FROM $db_updates WHERE upd_param = 'revision'")->fetchColumn())
	];

	$returnArray = [];
	foreach ($temp_array as $key => $val) {
        $returnArray[$tag_prefix . $key] = $val;
	}

	return $returnArray;
}
