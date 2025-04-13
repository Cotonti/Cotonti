<?php
/**
 * Comments system for Cotonti
 * Delete comment action
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\controllers\actions;

use Cot;
use cot\controllers\BaseAction;
use cot\exceptions\ForbiddenHttpException;
use cot\exceptions\NotFoundHttpException;
use cot\plugins\comments\controllers\IndexController;
use cot\plugins\comments\inc\CommentsControlService;
use cot\plugins\comments\inc\CommentsRepository;

/**
 * @property-read IndexController $controller
 */
class DeleteAction extends BaseAction
{
    public function run(): void
    {
        cot_check_xg();

        [$auth['read'], $auth['write'], $auth['admin']] = cot_auth('plug', 'comments');

        $id = cot_import('id', 'G', 'INT');
        if (empty($id)) {
            throw new NotFoundHttpException();
        }

        $comment = CommentsRepository::getInstance()->getById($id);
        if ($comment === null) {
            throw new NotFoundHttpException();
        }

        // Try to fetch $force_admin from session
        if (
            isset($_SESSION['cot_comments_force_admin'][$comment['com_area']][$comment['com_code']])
            && $_SESSION['cot_comments_force_admin'][$comment['com_area']][$comment['com_code']]
            && $auth['read']
            && $auth['write']
        ) {
            $auth['admin'] = true;
        }

        if (!$auth['admin']) {
            throw new ForbiddenHttpException();
        }

        // Come back url params
        $cb = cot_import('cb', 'G', 'TXT');

        // Comeback url params
        $cbExtensionCode = $cbUrlParams = null;
        if (!empty($cb)) {
            $cb = unserialize(base64_decode($cb));
            $cbExtensionCode = $cb[0];
            $cbUrlParams = $cb[1];
        }

        // Url params to clear static cache
        $ciExtensionCode = $ciUrlParams = null;
        if ($cbExtensionCode !== null && $cbExtensionCode !== 'admin') {
            $ciExtensionCode = $cbExtensionCode;
            $ciUrlParams = $cbUrlParams;
        }

        if (CommentsControlService::getInstance()->delete($id, $ciExtensionCode, $ciUrlParams)) {
            cot_message(Cot::$L['comments_deleted']);
        }

        cot_redirect(cot_url($cbExtensionCode, $cbUrlParams, '#comments', true));
    }
}