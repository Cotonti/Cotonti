<?php
/**
 * English langfile for mcaptcha
 *
 * @package mcaptcha
 * @author Cotonti Team
 */

defined('COT_CODE') or die('Wrong URL');

$L['info_desc'] = 'Protects website from spam bots with simple arithmetic tasks (requires JavaScript)';

$L['mcaptcha_error'] = 'Error: too many attempts, please come back later.';
$L['captcha_verification_failed'] = 'The sum was not solved correctly';

$L['cfg_delay'] = array('Anti hammering delay', 'sec');
$L['cfg_attempts'] = array('Max attempts per second', '0 - unlimited');

?>