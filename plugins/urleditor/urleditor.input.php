<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
Order=5
[END_COT_EXT]
==================== */

/**
 * Overloads standard cot_url() function and loads URL
 * transformation rules
 *
 * @package URLEditor
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (empty($cot_urltrans) || !is_array($cot_urltrans)) {
	$cot_urltrans = [];
	$urlTransPreset = './datas/urltrans.dat';
	if (
        !empty(Cot::$cfg['plugin']['urleditor']['preset'])
        && !in_array(Cot::$cfg['plugin']['urleditor']['preset'], ['custom', 'none'], true)
    ) {
        $urlTransFileName = './datas/' . Cot::$cfg['plugin']['urleditor']['preset'] . '.dat';
		$urlTransPreset = file_exists($urlTransFileName)
            ? $urlTransFileName
            : Cot::$cfg['plugins_dir'] . '/urleditor/presets/' . Cot::$cfg['plugin']['urleditor']['preset'] . '.dat';
	}

	if (Cot::$cfg['plugin']['urleditor']['preset'] !== 'none' && file_exists($urlTransPreset)) {
		$fp = fopen($urlTransPreset, 'r');
		while ($line = fgets($fp)) {
            $line = trim($line, " \t\r\n");

            if ($line === '' || mb_strpos($line, '#') === 0) {
                continue;
            }

			$parts = preg_split('#\s+#', $line);
			$rule = [];
			$rule['trans'] = $parts[2];
			$parts[1] === '*' ? $rule['params'] = [] : parse_str($parts[1], $rule['params']);
			foreach($rule['params'] as $key => $val) {
				if (mb_strpos($val, '|') !== false) {
					$rule['params'][$key] = explode('|', $val);
				}
			}
			$cot_urltrans[$parts[0]][] = $rule;
		}
		fclose($fp);
	}
	// Fallback rules for standard PHP URLs
	$cot_urltrans_fallback = array(
		'params' => array(),
		'trans' => '{$_area}.php'
	);
	$cot_urltrans['admin'][] = $cot_urltrans_fallback;
	$cot_urltrans['index'][] = $cot_urltrans_fallback;
	$cot_urltrans['login'][] = $cot_urltrans_fallback;
	$cot_urltrans['message'][] = $cot_urltrans_fallback;
	$cot_urltrans['plug'][] = array(
		'params' => array(),
		'trans' => 'index.php'
	);
	$cot_urltrans['*'][] = array(
		'params' => array(),
		'trans' => 'index.php?e={$_area}'
	);
	// $cache && $cache->db->store('cot_urltrans', $cot_urltrans, 'system', 1200);
}

if (!in_array(Cot::$cfg['plugin']['urleditor']['preset'], ['custom', 'none'])) {
	if (
        file_exists('./datas/' . Cot::$cfg['plugin']['urleditor']['preset'] . '.dat')
		&& file_exists('./datas/' . Cot::$cfg['plugin']['urleditor']['preset'] . '.functions.php')
    ) {
		require_once './datas/' . Cot::$cfg['plugin']['urleditor']['preset'] . '.functions.php';

    } elseif (file_exists(Cot::$cfg['plugins_dir'] . '/urleditor/presets/' .
        Cot::$cfg['plugin']['urleditor']['preset'] . '.functions.php')) {
		require_once Cot::$cfg['plugins_dir'] . '/urleditor/presets/' . Cot::$cfg['plugin']['urleditor']['preset'] .
            '.functions.php';
	}
}

require_once cot_incfile('urleditor', 'plug');

cot_apply_rwr();