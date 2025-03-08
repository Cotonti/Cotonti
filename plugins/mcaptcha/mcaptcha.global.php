<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
Order=10
[END_COT_EXT]
==================== */

/**
 * mCAPTCHA functions
 *
 * @package MathCaptcha
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

include_once cot_langfile('mcaptcha', 'plug');

function mcaptcha_generate(): string
{
	global $cfg, $L;

	if (!isset($_SESSION['mcaptcha_attempts'])) {
		$_SESSION['mcaptcha_attempts'] = 0;
	}

	if (
		$cfg['plugin']['mcaptcha']['attempts'] > 0
		&& $_SESSION['mcaptcha_attempts'] >= $cfg['plugin']['mcaptcha']['attempts']
	) {
		return $L['mcaptcha_error'];
	}

	$n1 = mt_rand(1, 99);
	$n2 = mt_rand(1, 99);
	$salt = md5(mt_rand());

	$_SESSION['mcaptcha_res'] = $n1 + $n2;
	$_SESSION['mcaptcha_time'] = time();
	$_SESSION['mcaptcha_salt'] = $salt;
	$_SESSION['mcaptcha_count'] = 0;
	$_SESSION['mcaptcha_attempts']++;

	$html = $n1 . ' + ' . $n2 . ' = ?';

	return mcaptcha_obfuscate($html) . '<input type="hidden" name="mcaptcha_salt" value="' . $salt . '" />';
}

/**
 * Validates captcha input
 *
 * @param int $res User submitted result
 * @return bool
 */
function mcaptcha_validate($res)
{
	global $cfg;

	if (
		!empty($_SESSION['mcaptcha_time']) 
		&& !empty($_SESSION['mcaptcha_salt'])
		&& (time() - $_SESSION['mcaptcha_time'] > $cfg['plugin']['mcaptcha']['delay'])
	) {
		if (cot_import('mcaptcha_salt', 'P', 'ALP') === $_SESSION['mcaptcha_salt']) {
			if ($_SESSION['mcaptcha_count'] === 0) {
				$_SESSION['mcaptcha_count']++;
				return (int)$res === (int)$_SESSION['mcaptcha_res'];
			}
		}
	}

	$_SESSION['mcaptcha_count']++;
	return false;
}

/**
 * JavaScript-based text obfuscator
 *
 * @param string $text
 * @return string
 */
function mcaptcha_obfuscate($text)
{
	$enc_string = '';
	$length = strlen($text);
	for ($i = 0; $i < $length; $i++) {
		$inter = ord($text[$i]) + 4;
		$enc_char = chr($inter);
		$enc_string .= ($enc_char == '\\' ? '\\\\' : $enc_char);
	}

	srand((int)((float)microtime()));
	$letters = range('a', 'z');
	$rnd = $letters[array_rand($letters)] . md5(time());

	$script = "<span id=\"mcap$rnd\"></span><script type=\"text/javascript\">//<![CDATA[
	function $rnd(s){var r='';for(var i=0;i<s.length;i++){var n=s.charCodeAt(i);if(n>=8364){n=128;}r+=String.fromCharCode(n-4);}return r;}
	document.getElementById('mcap$rnd').appendChild(document.createTextNode($rnd('".$enc_string."')));
//]]></script>";

	return $script;
}

$cot_captcha[] = 'mcaptcha';
?>
