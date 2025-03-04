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

use cot\extensions\ExtensionsDictionary;
use cot\serverEvents\ServerEventsDictionary;

defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] <= 0) {
    return;
}

if (Cot::$cfg['pm']['allowPopUpNotifications'] || Cot::$env['ext'] === 'pm') {
    if (empty(Cot::$R['pm_newMessageSound'])) {
        require_once cot_incfile('pm', ExtensionsDictionary::TYPE_MODULE, 'resources');
    }

    Resources::linkFileFooter(Cot::$cfg['modules_dir'] . '/pm/js/pm.js');

    if (
        (Cot::$cfg['serverEvents'] ?? ServerEventsDictionary::DRIVER_DISABLED) !== ServerEventsDictionary::DRIVER_DISABLED
        && Cot::$cfg['pm']['allowPopUpNotifications']
    ) {
        Resources::embedFooter(
            "cot.pm.notificationSound = '" . Cot::$R['pm_newMessageSound'] . "'; cot.pm.initNotificationHandler();"
        );
    }
}
