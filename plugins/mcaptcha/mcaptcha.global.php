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
 * @package mcaptcha
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

include_once cot_langfile('mcaptcha', 'plug');

/**
 * Generates new math captcha and returns question output
 *
 * @return string
 */
function mcaptcha_generate()
{
	global $cfg, $L;

	if($cfg['plugin']['mcaptcha']['attempts'] > 0 && $_SESSION['mcaptcha_attempts'] > $cfg['plugin']['mcaptcha']['attempts'])
	{
		// The captcha has been called too much times this session
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
 * @param int $res User result
 * @return bool
 */
function mcaptcha_validate($res)
{
	global $cfg;
	// Check anti-hammer
	if(time() - $_SESSION['mcaptcha_time'] > $cfg['plugin']['mcaptcha']['delay'])
	{
		// Check salt (form-to-session tie)
		if($_POST['mcaptcha_salt'] == $_SESSION['mcaptcha_salt'])
		{
			// Check per-result counter
			if($_SESSION['mcaptcha_count'] == 0)
			{
				// Check the result
				if($res == $_SESSION['mcaptcha_res'])
				{
					return TRUE;
				}
			}
		}
	}
	$_SESSION['mcaptcha_count']++;
	return FALSE;
}

/**
 * JavaScript-based text obfuscator
 *
 * @param string $text Input text
 * @return string
 */
function mcaptcha_obfuscate($text)
{
	$enc_string = '';
	$length = strlen($text);
	for ($i=0; $i < $length; $i++) {
		$inter = ord($text[$i]) + 4;
		$enc_char =  chr($inter);
		$enc_string .= ($enc_char == '\\' ? '\\\\' : $enc_char);
	}
	// get a random string to use as a function name
	srand((float) microtime() * 10000000);
	$letters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	$rnd = $letters[array_rand($letters)] . md5(time());
	// the actual js (in one line to confuse)
	$script = "<span id=\"mcap$rnd\"></span><script type=\"text/javascript\">//<![CDATA[
function $rnd(s){var r='';for(var i=0;i<s.length;i++){var n=s.charCodeAt(i);if(n>=8364){n=128;}r+=String.fromCharCode(n-4);}return r;}document.getElementById('mcap$rnd').appendChild(document.createTextNode($rnd('".$enc_string."')));
//]]></script>";
	return $script;
}

$cot_captcha[] = 'mcaptcha';


?>
