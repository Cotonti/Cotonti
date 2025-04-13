<?php
/**
 * Comments system for Cotonti
 * Notification Service
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use Cot;
use cot\dto\ItemDto;
use cot\extensions\ExtensionsDictionary;
use cot\traits\GetInstanceTrait;
use cot\users\UsersHelper;
use cot\users\UsersRepository;

class CommentsNotificationService
{
    use GetInstanceTrait;

    /**
     * Notify administrators about a new/edited comment
     *
     * @param array $comment Comment data
     * @param string $event The event we notify the administrators about
     * @param ?string $ciExtensionCode Commented item extension code
     * @param ?array $ciUrlParams Commented item url params
     */
    public function notifyAdmins(
        array $comment,
        string $event = CommentsDictionary::EVENT_CREATE,
        ?string $ciExtensionCode = null,
        ?array $ciUrlParams = null
    ): void {
        $admins = UsersRepository::getInstance()->getByGroup(COT_GROUP_SUPERADMINS);
        foreach ($admins as $key => $admin) {
            if (Cot::$usr['id'] === $admin['user_id']) {
                unset($admins[$key]);
            }
        }
        if (empty($admins)) {
            return;
        }

        $commentDto = CommentsDtoRepository::getInstance()->getById($comment['com_id'], true);
        if (empty($commentDto)) {
            return;
        }

        if (empty($commentDto->url) && !empty($ciExtensionCode)) {
            $commentDto->url = cot_url($ciExtensionCode, $ciUrlParams, '#com' . $comment['com_id']);
        }

        if (!cot_url_check($commentDto->url)) {
            $commentDto->url = COT_ABSOLUTE_URL . $commentDto->url;
        }

        foreach ($admins as $admin) {
            $this->sendNotification($commentDto, $admin, $event);
        }
    }

    private function sendNotification(ItemDto $comment, array $user, string $event): void
    {
        $userLang = $user['user_lang'];

        $tmpLang = null;
        if (!Cot::$cfg['forcedefaultlang'] && Cot::$cfg['defaultlang'] !== $userLang) {
            $tmpLang = Cot::$L;
            $langFile = cot_langfile(
                'comments',
                ExtensionsDictionary::TYPE_PLUGIN,
                Cot::$cfg['defaultlang'],
                Cot::$cfg['defaultlang']
            );
            if (!$langFile)  {
                $langFile = cot_langfile('comments', ExtensionsDictionary::TYPE_PLUGIN, 'en', 'en');
            }
            if ($langFile)  {
                include $langFile;
                Cot::$L = array_merge(Cot::$L, $L);
            }
        }

        switch ($event) {
            case CommentsDictionary::EVENT_CREATE:
                $emailSubject = Cot::$L['comments_newCommentNotificationSubject'];
                $emailBody =  Cot::$L['comments_newCommentNotification'];
                break;

            default:
                // Edit event
                $emailSubject = Cot::$L['comments_editedCommentNotificationSubject'];
                $emailBody =  Cot::$L['comments_editedCommentNotification'];
        }

        $itemTitle = $comment->getTitleHtml();
        if (empty($itemTitle)) {
            $itemTitle = $comment->title;
        }

        $usersHelper = UsersHelper::getInstance();

        $userName = Cot::$usr['id'] === 0
            ? preg_replace('#[^\w\p{L}]#u', '', $comment->data['com_author'])
            : cot_rc_link(
                $usersHelper->getUrl(Cot::$usr['profile'], '', false, true),
                $usersHelper->getFullName(Cot::$usr['profile'])
            );

        $emailBody = cot_rc(
            $emailBody,
            [
                'user' => $userName,
                'commentTo' => $itemTitle,
                'text' => strip_tags($comment->data['com_text'] ?? ''),
                'url' => $comment->url,
            ]
        );

        cot_mail($user['user_email'], $emailSubject, $emailBody, '', false, '', true);

        if ($tmpLang !== null) {
            Cot::$L = $tmpLang;
        }
    }
}