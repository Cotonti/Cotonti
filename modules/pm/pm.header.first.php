<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.first
[END_COT_EXT]
==================== */

declare(strict_types=1);

/**
 * Private Messages
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] <= 0) {
    return;
}

if (Cot::$cfg['pm']['allowPopUpNotifications'] || Cot::$env['ext'] === 'pm') {
    Resources::linkFileFooter(Cot::$cfg['modules_dir'] . '/pm/js/pm.js');

    if (Cot::$cfg['pm']['allowPopUpNotifications']) {
        Resources::addEmbed('window.pmNotifications = true');
    }
}
