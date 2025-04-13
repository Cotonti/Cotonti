<?php
/**
 * Comments system for Cotonti
 * Display comments via ajax action
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

namespace cot\plugins\comments\controllers\actions;

use cot\controllers\BaseAction;
use cot\exceptions\NotFoundHttpException;
use cot\plugins\comments\inc\CommentsService;
use cot\plugins\comments\inc\CommentsWidget;

defined('COT_CODE') or die('Wrong URL');

class DisplayAction extends BaseAction
{
    public function run(): string
    {
        $source = cot_import('source', 'G', 'ALP');
        $sourceId = cot_import('source-id', 'G', 'TXT');
        $extensionCode = cot_import('ext', 'G', 'ALP');
        $categoryCode = cot_import('cat', 'G', 'TXT');

        if (empty($source) || empty($sourceId)) {
            throw new NotFoundHttpException();
        }

        // Check if comments are enabled for specific category/item
        if (!empty($extensionCode)) {
            cot_block(
                CommentsService::getInstance()->isEnabled($extensionCode, $categoryCode)
            );
        }

        cot_sendheaders();

        return (new CommentsWidget(
            [
                'source' => $source,
                'sourceId' => $sourceId,
                'extensionCode' => 'page',
                'categoryCode' => $categoryCode,
            ]
        ))->run();
    }
}