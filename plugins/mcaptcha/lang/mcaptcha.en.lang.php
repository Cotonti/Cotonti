<?php
/**
 * English langfile for mcaptcha
 *
 * @package MathCaptcha
 */

defined('COT_CODE') or die('Wrong URL');

$L['info_desc'] = 'Protects website from spam bots with simple arithmetic tasks (requires JavaScript)';

$L['mcaptcha_error'] = 'Error: too many attempts, please come back later.';
$L['captcha_verification_failed'] = 'The sum was not solved correctly';

$L['cfg_delay'] = 'Anti hammering delay';
$L['cfg_delay_hint'] = 'sec';
$L['cfg_attempts'] = 'Max attempts per second';
$L['cfg_attempts_hint'] = '0 - unlimited';
