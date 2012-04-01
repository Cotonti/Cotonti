<?php
/**
 * Russian langfile for the Shield
 *
 * @package shield
 * @author Cotonti Team
 */

defined('COT_CODE') or die('Wrong URL');

$L['info_desc'] = 'Защита от hammering-атак';

$L['cfg_shieldtadjust'] = array('Настройка таймеров защиты (в %)', 'Чем выше, тем сильнее защита против спама');
$L['cfg_shieldzhammer'] = array('Анти-хаммер после * хитов', 'Чем меньше, тем короче срок автоблокировки пользователя');

$L['shield_protect'] = 'Анти-хаммеринг активирован, попробуйте снова через {$sec} секунд...<br />После этого промежутка времени вы сможете обновить данную страницу и продолжить.<br />Последнее действие: {$action}';

?>
