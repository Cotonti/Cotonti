<?php
/**
 * Comments system for Cotonti
 * Add new comment action
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
use cot\plugins\comments\inc\CommentsDictionary;
use cot\plugins\comments\inc\CommentsNotificationService;
use cot\plugins\comments\inc\CommentsService;
use cot\users\UsersHelper;

defined('COT_CODE') or die('Wrong URL');

/**
 * @property-read IndexController $controller
 */
class CreateAction extends BaseAction
{
    public function run(): string
    {
        global $cot_captcha;

        [$auth['read'], $auth['write'], $auth['admin']] = cot_auth('plug', 'comments');

        if (!$auth['write']) {
            throw new ForbiddenHttpException();
        }

        cot_shield_protect();

        $source = cot_import('source', 'P', 'ALP');
        $sourceId = cot_import('source-id', 'P', 'TXT');
        $extensionCode = cot_import('extension', 'P', 'ALP');
        $categoryCode = cot_import('category', 'P', 'TXT');
        $ci = cot_import('ci', 'P', 'TXT');

        // Commented Item url params
        $ciExtensionCode = $ciUrlParams = null;
        if (!empty($ci)) {
            $ci = unserialize(base64_decode($ci));
            $ciExtensionCode = $ci[0];
            $ciUrlParams = $ci[1];
            if (empty($extensionCode)) {
                $extensionCode = $ciExtensionCode;
            }
        }

        if (empty($source) || empty($sourceId)) {
            throw new NotFoundHttpException();
        }

        // Check if comments are enabled for specific category/item
        if (!empty($extensionCode)) {
            cot_block(
                CommentsService::getInstance()->isEnabled($extensionCode, $categoryCode)
            );
        }

        $comment = [
            'com_area' => $source,
            'com_code' => $sourceId,
            'com_author' => Cot::$usr['id'] === 0
                ? cot_import('comment_author', 'P', 'TXT')
                : UsersHelper::getInstance()->getFullName(Cot::$usr['profile']),
            'com_authorid' => Cot::$usr['id'],
            'com_text' => cot_import('comment_text', 'P', 'HTM'),
        ];

        // Extra fields
        if (!empty(Cot::$extrafields[Cot::$db->com])) {
            foreach (Cot::$extrafields[Cot::$db->com] as $extraField) {
                $comment['com_' . $extraField['field_name']] = cot_import_extrafields(
                    'comment_' . $extraField['field_name'],
                    $extraField,
                    'P',
                    '',
                    'comments_'
                );
            }
        }

        /* == Hook == */
        foreach (cot_getextplugins('comments.add.first') as $pl) {
            include $pl;
        }
        /* ===== */

        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.26
            /* == Hook == */
            foreach (cot_getextplugins('comments.send.first') as $pl) {
                include $pl;
            }
            /* ===== */
        }

        $service = CommentsService::getInstance();
        $service->validateWithMessages($comment);

        if (empty($comment['com_author']) && Cot::$usr['id'] === 0) {
            cot_error(Cot::$L['comments_authorTooShort'], 'comment_author');
        }

        if (Cot::$usr['id'] === 0 && !empty($cot_captcha)) {
            $rverify = cot_import('rverify', 'P', 'TXT');
            if (!cot_captcha_validate($rverify)) {
                cot_error(Cot::$L['captcha_verification_failed'], 'rverify');
            }
        }

        /* == Hook == */
        foreach (cot_getextplugins('comments.add.validate') as $pl) {
            include $pl;
        }
        /* ===== */

        $errors = $this->controller->getErrors();
        if ($errors !== []) {
            return $this->controller->errorResult($errors);
        }

        $id = CommentsControlService::getInstance()->save(null, $comment, $ciExtensionCode, $ciUrlParams);

        $comment['com_id'] = $id;

        $_SESSION['cot_comments_edit'][$id] = Cot::$sys['now'];

        /* == Hook == */
        foreach (cot_getextplugins('comments.add.done') as $pl) {
            include $pl;
        }
        /* ===== */

        if (Cot::$cfg['plugin']['comments']['mail']) {
            CommentsNotificationService::getInstance()->notifyAdmins(
                $comment,
                CommentsDictionary::EVENT_CREATE,
                $ciExtensionCode,
                $ciUrlParams
            );
        }

        cot_message(Cot::$L['comments_added']);

        cot_shield_update(20, 'New comment');

        return $this->controller->successResult(Cot::$L['comments_added']);
    }
}