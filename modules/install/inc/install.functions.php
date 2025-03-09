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
 * The name of the file that is used to prevent the installer from running twice
 * @return string
 */
function cot_installProcessFile()
{
    $processFileDir = isset(Cot::$cfg['cache_dir']) ? Cot::$cfg['cache_dir'] : './datas/cache';
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
 * @param string $file_contents Config file contents (by reference)
 * @param string $config_name   Config option name, e.g. 'mysqlpassword'
 * @param string $config_value  Actual value to set, e.g. user-supplied password
 * @return void
 */
function cot_installConfigReplace(&$file_contents, $config_name, $config_value)
{
    // Burada özel karakterleri kaçışlıyoruz:
    $config_value_escaped = str_replace(
        ['\\', '$', "'"],
        ['\\\\', '\\$', "\\'"],
        $config_value
    );

    $pattern = "#^\\\$cfg\\['$config_name'\\]\\s*=\\s*'.*?';#m";
    $replacement = "\$cfg['$config_name'] = '$config_value_escaped';";

    $file_contents = preg_replace($pattern, $replacement, $file_contents);
}

/**
 * Parses extensions selection section
 *
 * @param string $ext_type      'Module' veya 'Plugin'
 * @param array  $default_list  Tavsiye edilen eklentiler (varsayılan işaretli)
 * @param array  $selected_list Kullanıcı tarafından seçili eklentiler
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
                    // Önceki kategoriyi çıkartalım
                    $t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
                }
                // Yeni kategori
                $prev_cat = $info['Category'];
                $catTitle = !empty($L['ext_cat_' . $info['Category']])
                    ? $L['ext_cat_' . $info['Category']]
                    : $info['Category'];

                $t->assign('PLUGIN_CAT_TITLE', $catTitle);
            }

            // Requires
            if (!empty($info['Requires_modules']) || !empty($info['Requires_plugins'])) {
                $modules_list = empty($info['Requires_modules'])
                    ? $L['None']
                    : implode(', ', explode(',', $info['Requires_modules']));

                $plugins_list = empty($info['Requires_plugins'])
                    ? $L['None']
                    : implode(', ', explode(',', $info['Requires_plugins']));

                $requires = cot_rc('install_code_requires', [
                    'modules_list' => $modules_list,
                    'plugins_list' => $plugins_list
                ]);
            } else {
                $requires = '';
            }

            // Recommends
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

            // Hangi eklentiler varsayılan seçili veya kullanıcı seçimi
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

            // Parse et
            $t->parse("MAIN.STEP_4.$block_name");
        }
    }
    if ($ext_type_lc == 'plugin' && $prev_cat != '') {
        // Son kategoriyi parse et
        $t->parse("MAIN.STEP_4.{$ext_type_uc}_CAT");
    }
}

/**
 * Sorts selected extensions by their setup order if present
 *
 * @param array $selected_extensions Eklenti isimleri (sıralanmamış)
 * @param bool  $is_module TRUE ise modüller, FALSE ise eklentiler
 * @return array Sıralı liste
 */
function cot_installSortExtensions($selected_extensions, $is_module = false)
{
    global $cfg;
    $path = $is_module ? $cfg['modules_dir'] : $cfg['plugins_dir'];
    $ret = [];

    // Order değerine göre gruplara ayır
    $extensions = [];
    foreach ($selected_extensions as $name) {
        $info = cot_infoget("$path/$name/$name.setup.php", 'COT_EXT');
        $order = isset($info['Order']) ? (int) $info['Order'] : COT_PLUGIN_DEFAULT_ORDER;

        // Kategori 'post-install' ise ve order < 999 ise 999 yap
        if (isset($info['Category']) && $info['Category'] == 'post-install' && $order < 999) {
            $order = 999;
        }
        $extensions[$order][] = $name;
    }

    // Grupları sırayla yeniden birleştir
    foreach ($extensions as $grp) {
        foreach ($grp as $name) {
            $ret[] = $name;
        }
    }

    return $ret;
}
