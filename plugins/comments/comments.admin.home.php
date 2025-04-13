<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.mainpanel
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 * Widget for admin panel home page
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

use cot\extensions\ExtensionsDictionary;
use cot\plugins\comments\inc\RecentCommentsWidget;

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$commentsLimit = (int) Cot::$cfg['plugin']['comments']['adminHomeCount'];

if (
    !cot_auth('plug', 'comments', 'R')
    || $commentsLimit < 1
) {
    return;
}

require_once cot_incfile('comments', ExtensionsDictionary::TYPE_PLUGIN);

$line =  (new RecentCommentsWidget(
    [
        'limit' => $commentsLimit
    ]
))->run();
