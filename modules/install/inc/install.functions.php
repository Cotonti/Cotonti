<?php
/**
 * Installer functions
 *
 * @package Install
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsHelper;

/**
 * Returns the name (path) of the file that prevents the installer from running twice.
 *
 * @return string
 */
function cot_installProcessFile()
{
    $processFileDir = isset(Cot::$cfg['cache_dir']) ? Cot::$cfg['cache_dir'] : './datas/cache';
    return $processFileDir . '/install';
}

/**
 * Redirects to a given URL after deleting the process file if it exists.
 *
 * @param string $url Target URL
 * @return void
 */
function cot_installRedirect($url)
{
    $processFile = cot_installProcessFile();
    if (file_exists($processFile)) {
        unlink($processFile);
    }

    // Clear session data for selected extensions
    if (isset($_SESSION['selected_modules'])) {
        unset($_SESSION['selected_modules']);
    }
    if (isset($_SESSION['selected_plugins'])) {
        unset($_SESSION['selected_plugins']);
    }

    cot_redirect($url);
}

/**
 * Reads a configuration file and returns an array of $cfg and $db_* variables.
 *
 * @param string $file Path to the config file
 * @return array [ array of $cfg options, array of $db_* variables ]
 */
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
 * Replaces a specific config line in $file_contents with a new value,
 * properly escaping special characters to avoid issues with preg_replace.
 *
 * @param string &$fileContents The full config file content (by reference)
 * @param string $configName The name/key of the config option (e.g., 'mysqlpassword')
 * @param mixed $configValue The new value (e.g., user-supplied DB password)
 */
function cot_installConfigReplace(&$fileContents, $configName, $configValue): void
{
    // Escape special characters (\, $, and ') to prevent regex backreference or syntax errors
    $configValuePrepared = is_string($configValue)
        ? "'" . str_replace(['\\', '$', "'"], ['\\\\', '\\$', "\\'"],  $configValue) . "'"
        : var_export($configValue, true);

    $pattern = "/^\\\$cfg\\['$configName'\\]\\s*=\\s*'?.*?'?;/m";
    $replacement = "\$cfg['$configName'] = " . $configValuePrepared . ';';

    $fileContents = preg_replace($pattern, $replacement, $fileContents);
}

/**
 * Renders a list of extensions (modules or plugins) on the installer page,
 * allowing the user to select which ones to install.
 *
 * @param string $ext_type      'Module' or 'Plugin'
 * @param array  $default_list  Recommended extensions that are checked by default
 * @param array  $selected_list Extensions previously selected by the user
 */
function cot_installParseExtensions($ext_type, $default_list = [], $selected_list = [])
{
    global $t, $cfg, $L;
    $ext_type_lc = strtolower($ext_type);
    $ext_type_uc = strtoupper($ext_type);

    $ext_list = cot_extension_list_info($cfg["{$ext_type_lc}s_dir"]);

    $ext_type_lc == 'plugin' ? uasort($ext_list, 'cot_extension_catcmp') : ksort($ext_list);

    $prev_cat = '';
    $block_name = $ext_type_lc == 'plugin' 
        ? "{$ext_type_uc}_CAT.{$ext_type_uc}_ROW"
        : "{$ext_type_uc}_ROW";

    $extensionHelper = ExtensionsHelper::getInstance();

    foreach ($ext_list as $f => $info) {
        if (is_array($info)) {
            $code = $f;
            if ($ext_type_lc == 'plugin' && $prev_cat != $info['Category']) {
                if ($prev_cat != '') {
                    // Parse the previous category section
                    $t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
                }
                // Assign the new category
                $prev_cat = $info['Category'];
                $catTitle = !empty($L['ext_cat_' . $info['Category']])
                    ? $L['ext_cat_' . $info['Category']]
                    : $info['Category'];

                $t->assign('PLUGIN_CAT_TITLE', $catTitle);
            }

            // Requires dependencies
            if (!empty($info['Requires_modules']) || !empty($info['Requires_plugins'])) {
                $modules_list = empty($info['Requires_modules'])
                    ? $L['None']
                    : implode(', ', explode(',', $info['Requires_modules']));

                $plugins_list = empty($info['Requires_plugins'])
                    ? $L['None']
                    : implode(', ', explode(',', $info['Requires_plugins']));
                    
                $requires = cot_rc('install_code_requires', [
                        'modules_list' => $modules_list,
                        'plugins_list' => $plugins_list,
                    ]);

            } else {
                $requires = '';
            }

            // Recommended dependencies
            if (!empty($info['Recommends_modules']) || !empty($info['Recommends_plugins'])) {
                $modules_list = empty($info['Recommends_modules'])
                    ? $L['None']
                    : implode(', ', explode(',', $info['Recommends_modules']));

                $plugins_list = empty($info['Recommends_plugins'])
                    ? $L['None']
                    : implode(', ', explode(',', $info['Recommends_plugins']));

                $recommends = cot_rc('install_code_recommends', [
                    'modules_list' => $modules_list,
                    'plugins_list' => $plugins_list
                ]);
            } else {
                $recommends = '';
            }

            // Determine if the extension is checked by default or selected by the user
            if ((is_array($selected_list) && count($selected_list)) > 0) {
                $checked = in_array($code, $selected_list);
            } else {
                $checked = in_array($code, $default_list);
            }

            $extensionType = $ext_type === 'Module'
                ? ExtensionsDictionary::TYPE_MODULE
                : ExtensionsDictionary::TYPE_PLUGIN;

            $t->assign([
                "{$ext_type_uc}_ROW_CHECKBOX" => cot_checkbox($checked, "install_{$ext_type_lc}s[$code]"),
                "{$ext_type_uc}_ROW_TITLE" => $extensionHelper->getTitle($code, $extensionType),
                "{$ext_type_uc}_ROW_DESCRIPTION" => $extensionHelper->getDescription($code, $extensionType),
                "{$ext_type_uc}_ROW_REQUIRES" => $requires,
                "{$ext_type_uc}_ROW_RECOMMENDS" => $recommends,
            ]);

            // Render each extension row
            $t->parse("MAIN.STEP_4.$block_name");
        }
    }
    if ($ext_type_lc == 'plugin' && $prev_cat != '') {
        // Render the last category
        $t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
    }
}

/**
 * Sorts selected extensions based on their 'Order' property if provided.
 * @param array $selected_extensions List of extension names (unsorted)
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @return array Sorted list of extension names
 */
function cot_installSortExtensions($selected_extensions, $is_module = false)
{
    global $cfg;
    $path = $is_module ? $cfg['modules_dir'] : $cfg['plugins_dir'];
    $ret = [];

    // Group extensions by their 'Order' value
    $extensions = [];
    foreach ($selected_extensions as $name) {
        $info = cot_infoget("$path/$name/$name.setup.php", 'COT_EXT');
        $order = isset($info['Order']) ? (int) $info['Order'] : COT_PLUGIN_DEFAULT_ORDER;

        if (isset($info['Category']) && $info['Category'] == 'post-install' && $order < 999) {
            $order = 999;
        }
        $extensions[$order][] = $name;
    }

    // Merge groups in ascending order of 'Order'
    foreach ($extensions as $grp) {
        foreach ($grp as $name) {
            $ret[] = $name;
        }
    }

    return $ret;
}
