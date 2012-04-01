<?php
/**
 * English langfile for the Shield
 *
 * @package shield
 * @author Cotonti Team
 */

defined('COT_CODE') or die('Wrong URL');

$L['info_desc'] = 'Anti-hammering protection';

$L['cfg_shieldtadjust'] = array('Adjust Shield timers (in %)', 'The higher, the harder to spam');
$L['cfg_shieldzhammer'] = array('Anti-hammer after * fast hits', 'The smaller, the faster the auto-ban 3 minutes happens');

$L['shield_protect'] = 'Shield protection activated, please retry in {$sec} seconds...<br />After this duration, you can refresh the current page to continue.<br />Last action was : {$action}';

?>
