<?php
/**
 * Installer functions
 *
 * @package Install
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

/**
 * The name of the file that is used to prevent the installer from running twice
 * @return string
 */
function cot_installProcessFile()
{
    $processFileDir =  isset(Cot::$cfg['cache_dir']) ? Cot::$cfg['cache_dir'] : './datas/cache';
    return $processFileDir . '/install';
}

/**
 * Same as cot_redirect() but with process file deletion
 * @param $url
 * @return void
 * @see cot_redirect()
 */
function cot_installRedirect($url)
{
    $processFile = cot_installProcessFile();
    if (file_exists($processFile)) {
        unlink($processFile);
    }

    cot_redirect($url);
}

function cot_get_config($file)
{
    include $file;

    $vars = get_defined_vars();
    $db_vars = [];
    foreach ($vars as $key => $val) {
        if (preg_match('#^db_#', $key)) {
            $db_vars[$key] = $val;
        }
    }

    return [
        !empty($cfg) ? $cfg : [],
        $db_vars,
    ];
}

/**
 * Replaces a sample config with its actual value
 *
 * @param string $file_contents Config file contents
 * @param string $config_name Config option name
 * @param string $config_value Config value to set
 * @return string Modified file contents
 */
function cot_installConfigReplace(&$file_contents, $config_name, $config_value)
{
    $file_contents = preg_replace("#^\\\$cfg\['$config_name'\]\s*=\s*'.*?';#m",
        "\$cfg['$config_name'] = '$config_value';", $file_contents);
}

/**
 * Parses extensions selection section
 *
 * @param string $ext_type Extension type: 'Module' or 'Plugin'
 * @param array $default_list A list of recommended extensions (checked by default)
 * @param array $selected_list A list of previously selected extensions
 */
function cot_installParseExtensions($ext_type, $default_list = array(), $selected_list = array())
{
    global $t, $cfg, $L;
    $ext_type_lc = strtolower($ext_type);
    $ext_type_uc = strtoupper($ext_type);

    $ext_list = cot_extension_list_info($cfg["{$ext_type_lc}s_dir"]);

    $ext_type_lc == 'plugin' ? uasort($ext_list, 'cot_extension_catcmp') : ksort($ext_list);

    $prev_cat = '';
    $block_name = $ext_type_lc == 'plugin' ? "{$ext_type_uc}_CAT.{$ext_type_uc}_ROW" : "{$ext_type_uc}_ROW";
    foreach ($ext_list as $f => $info) {
        if (is_array($info)) {
            $code = $f;
            if ($ext_type_lc == 'plugin' && $prev_cat != $info['Category']) {
                if ($prev_cat != '') {
                    // Render previous category
                    $t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
                }
                // Assign a new one
                $prev_cat = $info['Category'];
                $catTitle = !empty($L['ext_cat_' . $info['Category']]) ?
                    $L['ext_cat_' . $info['Category']] : $info['Category'];

                $t->assign('PLUGIN_CAT_TITLE', $catTitle);
            }

            if (!empty($info['Requires_modules']) || !empty($info['Requires_plugins'])) {
                $modules_list = empty($info['Requires_modules']) ? $L['None']
                    : implode(', ', explode(',', $info['Requires_modules']));
                $plugins_list = empty($info['Requires_plugins']) ? $L['None']
                    : implode(', ', explode(',', $info['Requires_plugins']));
                $requires = cot_rc('install_code_requires',
                    array('modules_list' => $modules_list, 'plugins_list' => $plugins_list));
            } else {
                $requires = '';
            }

            if (!empty($info['Recommends_modules']) || !empty($info['Recommends_plugins']))
            {
                $modules_list = empty($info['Recommends_modules']) ? $L['None']
                    : implode(', ', explode(',', $info['Recommends_modules']));
                $plugins_list = empty($info['Recommends_plugins']) ? $L['None']
                    : implode(', ', explode(',', $info['Recommends_plugins']));
                $recommends = cot_rc('install_code_recommends',
                    array('modules_list' => $modules_list, 'plugins_list' => $plugins_list));
            }
            else
            {
                $recommends = '';
            }
            if ((is_array($selected_list) && count($selected_list)) > 0)
            {
                $checked = in_array($code, $selected_list);
            }
            else
            {
                $checked = in_array($code, $default_list);
            }
            $type = $ext_type == 'Module' ? 'module' : 'plug';
            $L['info_name'] = '';
            $L['info_desc'] = '';
            if (file_exists(cot_langfile($code, $type)))
            {
                include cot_langfile($code, $type);
            }
            $t->assign(array(
                "{$ext_type_uc}_ROW_CHECKBOX" => cot_checkbox($checked, "install_{$ext_type_lc}s[$code]"),
                "{$ext_type_uc}_ROW_TITLE" => empty($L['info_name']) ? $info['Name'] : $L['info_name'],
                "{$ext_type_uc}_ROW_DESCRIPTION" => empty($L['info_desc']) ? $info['Description'] : $L['info_desc'],
                "{$ext_type_uc}_ROW_REQUIRES" => $requires,
                "{$ext_type_uc}_ROW_RECOMMENDS" => $recommends
            ));
            $t->parse("MAIN.STEP_4.$block_name");
        }
    }
    if ($ext_type_lc == 'plugin' && $prev_cat != '')
    {
        // Render last category
        $t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
    }
}

/**
 * Sorts selected extensions by their setup order if present
 *
 * @global array $cfg
 * @param array $selected_extensions Unsorted list of extension names
 * @param bool $is_module TRUE if sorting modules, FALSE if sorting plugins
 * @return array Sorted list of extension names
 */
function cot_installSortExtensions($selected_extensions, $is_module = FALSE)
{
    global $cfg;
    $path = $is_module ? $cfg['modules_dir'] : $cfg['plugins_dir'];
    $ret = array();

    // Split into groups by Order value
    $extensions = array();
    foreach ($selected_extensions as $name) {
        $info = cot_infoget("$path/$name/$name.setup.php", 'COT_EXT');
        $order = isset($info['Order']) ? (int) $info['Order'] : COT_PLUGIN_DEFAULT_ORDER;
        if (isset($info['Category']) && $info['Category'] == 'post-install' && $order < 999) {
            $order = 999;
        }
        $extensions[$order][] = $name;
    }

    // Merge back into a single array
    foreach ($extensions as $grp) {
        foreach ($grp as $name) {
            $ret[] = $name;
        }
    }

    return $ret;
}